import $ from "jquery";

window.$ = window.jQuery = $;

let originalContent = "";
let isDirty = false;
let pendingSave = false;

$(document).ready(function () {

    const params = new URLSearchParams(window.location.search);
    const patientId = params.get("special");

    if (!patientId) return;

    $.ajax({
        url: `/api/getGeneratedContent/${patientId}`,
        method: "GET",
        success: function (res) {
            const text = res?.generated_report?.generated_text ?? "";
            $("#editableArea").text(text);

            originalContent = text;
            isDirty = false;

            if (res.status_id === 1) {
                window.recordStatus = 1;
                $("#editableArea").attr("contenteditable", "false");
                $("#SaveOutput").addClass("opacity-50 pointer-events-none");
                $("#PrintOutput").removeClass("opacity-50 pointer-events-none");
            } else {
                window.recordStatus = 0;
                $("#PrintOutput").addClass("opacity-50 pointer-events-none");
            }
        }
    });

    $("#editableArea").on("input", function () {
        isDirty = $(this).text() !== originalContent;
    });

    $("#SaveOutput").on("click", function () {
        if (!isDirty || window.recordStatus === 1) return;
        pendingSave = true;
        $("#approveWarningModal").removeClass("hidden");
    });

    $("#cancelApproveSave").on("click", function () {
        pendingSave = false;
        $("#approveWarningModal").addClass("hidden");
    });

    $("#confirmApproveSave").on("click", async function () {
        if (!pendingSave) return;

        const btn = this;
        btn.disabled = true;
        btn.textContent = "Saving...";

        const content = $("#editableArea").text();

        try {
            const saveRes = await fetch(`/api/saveRecord/${patientId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });

            if (!saveRes.ok) throw new Error();

            const approveRes = await fetch(`/api/statusUpdate`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: patientId,
                    status: 1
                })
            });

            if (!approveRes.ok) throw new Error();

            originalContent = content;
            isDirty = false;
            window.recordStatus = 1;

            $("#editableArea").attr("contenteditable", "false");
            $("#SaveOutput").addClass("opacity-50 pointer-events-none");
            $("#PrintOutput").removeClass("opacity-50 pointer-events-none");

            $("#approveWarningModal").addClass("hidden");
            $("#saveSuccessModal").removeClass("hidden");

        } catch {
            alert("Failed to save and approve.");
        } finally {
            btn.disabled = false;
            btn.textContent = "Yes, Save & Approve";
            pendingSave = false;
        }
    });

    $("#stayHereBtn").on("click", function () {
        $("#saveSuccessModal").addClass("hidden");
    });

    $("#goBackBtn").on("click", function () {
        window.location.href = "/dashboard";
    });

    $("#PrintOutput").on("click", function () {
        if (window.recordStatus !== 1) {
            alert("This document must be approved before printing.");
            return;
        }
        window.open(`/export/pdf/${patientId}`, "_blank");
    });

    $("[href], #BackToDashboard").on("click", function (e) {
        const target = $(this).attr("href");
        if (isDirty && target) {
            e.preventDefault();
            showUnsavedWarning(target);
        }
    });

    window.addEventListener("beforeunload", function (e) {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = "";
        }
    });
});

function showUnsavedWarning(targetUrl) {

    if ($("#unsavedWarningModal").length) return;

    $("body").append(`
        <div id="unsavedWarningModal"
             class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-[380px] animate-fadeIn">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Unsaved Changes</h2>
                <p class="text-gray-600 text-sm mb-6">
                    You have unsaved changes. Are you sure you want to leave?
                </p>
                <div class="flex justify-end gap-2">
                    <button id="stayEditBtn"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">
                        Stay
                    </button>
                    <button id="leaveEditBtn"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm">
                        Leave
                    </button>
                </div>
            </div>
        </div>
    `);

    $("#stayEditBtn").on("click", function () {
        $("#unsavedWarningModal").remove();
    });

    $("#leaveEditBtn").on("click", function () {
        isDirty = false;
        window.location.href = targetUrl;
    });
}
