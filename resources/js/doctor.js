import $ from "jquery";
import DataTable from "datatables.net-dt";

window.$ = window.jQuery = $;

const ai_Access = document.body.dataset.aiAccess === '1';
const ai_Ready = document.body.dataset.aiReady === '1';
const user_id = document.body.dataset.user;

// Current record being edited/approved
let originalContent = '';
let isEditMode = false;

/* ===============================
   STATE & URL MANAGEMENT
================================ */


const generatingRecords = new Set();





/* ===============================
   SIDE PANEL
================================ */

let currentGeneratedId = null;
let currentRecordId = null;
let originalPanelContent = '';
let panelEditMode = false;

function openGeneratedPanel() {
    $("#generatedPanel").removeClass("translate-x-full");
}

function closeGeneratedPanel() {
    $("#generatedPanel").addClass("translate-x-full");
    $("#panelContent").attr("contenteditable", "false");
    $("#panelFooter").addClass("hidden");
    panelEditMode = false;
    currentGeneratedId = null;
    currentRecordId = null;
}


/* ===============================
   LOADING
================================ */
function showLoading() {
    $("#tableLoading")
        .removeClass("hidden -z-10")
        .addClass("z-50");
}

function hideLoading() {
    $("#tableLoading")
        .addClass("hidden -z-10")
        .removeClass("z-50");
}

/* ===============================
   LOAD STATUS COUNTS
================================ */
function loadStatusCounts() {
    const year = 'all';
    const apiUrl = year !== 'all'
        ? `/api/getStatusCount?year=${year}&archived=false`
        : `/api/getStatusCount?archived=false`;

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            let pending = 0;
            let notEvaluated = 0;
            let approved = 0;

            data.forEach(status => {
                const count = status.count || 0;
                const name = status.status_name?.toLowerCase() || '';

                if (name === 'pending') {
                    pending = count;
                } else if (name === 'not evaluated') {
                    notEvaluated = count;
                } else if (name === 'approved') {
                    approved = count;
                }
            });

            $("#pending-count").text(pending);
            $("#not-evaluated-count").text(notEvaluated);
            $("#approved-count").text(approved);
        })
        .catch(err => {
            console.error('Error loading status counts:', err);
        });
}




/* ===============================
   DATATABLE INIT
================================ */
const table = $("#records-table").DataTable({
    serverSide: true,
    processing: false,
    pageLength: 20,

    scrollY: "calc(100vh - 380px)",
    scrollCollapse: true,

    autoWidth: false,
    paging: true,
    info: false,
    lengthChange: false,

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: "/table/records",
        type: "GET",
        data: d => {
            d.search =  '';
            d.status = 'pending';
            d.year   =  'all';
            loadStatusCounts()
            showLoading();
        },
        complete: hideLoading
    },

    columnDefs: [
        { targets: 0, width: "35%" },
        { targets: 1, width: "15%" },
        { targets: 2, width: "10%" },
        { targets: 3, width: "20%" },
        { targets: 4, width: "20%" }
    ],

    columns: [
        {
            data: "patient",
            render: function(p) {
                if (!p) return "—";
                return `
                    <div class="leading-tight">
                        <div class="font-medium text-gray-900 text-sm">
                            (${p.unit}) ${p.last_name}, ${p.first_name} ${p.middle_name ?? ""}
                        </div>
                        <div class="text-xs text-gray-500">${p.age} y.o.</div>
                    </div>
                `;
            }
        },
        {
            data: "status.status_name",
            orderable: false,
            className: "CENTER"
        },
        {
            data: "staff",
            render: function(data) {
                return data || "—";
            }
        },
        {
            data: "created_at",
            render: function(data) {
                if (!data) return "—";
                return new Date(data).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
            }
        },
        {
            data: null,
            orderable: false,
            className: "text-center",
            render: r => {
                const hasGenerated = r.generated_id && r.generated_id !== null && r.generated_id !== '';
                const hasDoctorApproval = r.doctor  !== null && r.doctor !== '';


                const generatingButton = `
                <div id="generateBtn-${r.id}" class="generate-btn hidden">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Generating
                </div>`

                const printBtn = hasGenerated
                    ? `<button class="hhi-btn hhi-btn-view icon-only"
                            title="Print"
                            onclick="printRow('${r.id}')">
                        <i class="fa-solid fa-print"></i>
                    </button>`
                    : `<button class="hhi-btn hhi-btn-view icon-only opacity-50 cursor-not-allowed"
                            title="No report available"
                            disabled>
                        <i class="fa-solid fa-print"></i>
                    </button>`;


                // View button - blue (only if generated)
                const viewBtn =
                    `<button class="hhi-btn hhi-btn-view icon-only view-generated-btn" ${!hasGenerated ? "disabled" : ' '}
                            title="View Evaluation"
                            data-id="${r.generated_id}">
                        <i class="fa-solid fa-eye"></i>
                    </button>`


                // Evaluate button - purple (only if not generated)
                const evaluateBtn = !hasGenerated && ai_Access && ai_Ready
                    ? `<button class="hhi-btn hhi-btn-primary icon-only evaluate-btn"
                            title="Evaluate with AI"

                            data-record-id="${r.id}">
                        <i class="fa-solid fa-brain"></i>
                    </button>`
                    : '';

                // Print button - green (only if generated)
                const printBtnStyled = hasDoctorApproval && hasGenerated
                    ? `<button class="hhi-btn hhi-btn-edit icon-only"
                            title="Print"
                            onclick="printRow('${r.id}')">
                        <i class="fa-solid fa-print"></i>
                    </button>`
                    :  hasGenerated ? `<button
                            class="hhi-btn icon-only bg-transparent border border-gray-200 text-gray-400 opacity-50 cursor-not-allowed"
                            title="Need Approval from a Doctor"
                            disabled
                        >
                            <i class="fa-solid fa-print"></i>
                        </button>
                        ` : '';

                return `
                <div class="flex flex-col items-center justify-center gap-1">
                    ${generatingButton}
                    <div id="actionsBtn-${r.id}" class="actions-btn">
                        ${viewBtn}
                        ${evaluateBtn}
                        ${printBtnStyled}
                    </div>

                </div>
            `;
            }
        }

    ]
});

window.table = table;




/* ===============================
   VIEW/EDIT GENERATED TEXT
================================ */

$(document).on("click", ".view-generated-btn", function (e) {
    e.stopPropagation();

    const $tr = $(this).closest("tr");
    const rowData = table.row($tr).data();

    currentGeneratedId = $(this).data("id");
    currentRecordId = rowData.id

    $("#panelRecordId").text(`Generated ID #${currentGeneratedId}`);
    openGeneratedPanel();
    setPanelLoading(true);
    setPanelEditMode(false);

    fetch(`/api/getGeneratedContent/${currentGeneratedId}`)
        .then(res => res.json())
        .then(res => {
            originalPanelContent = res.generated_text || "No generated content.";
            $("#panelContent").text(originalPanelContent);

            const isApproved = res.status_id === 1;
            if (!isApproved) {
                $("#panelEditBtn").removeClass("hidden");
            }
        })
        .catch(() => {
            $("#panelContent").text("Failed to load content.");
        })
        .finally(() => {
            setPanelLoading(false);
        });
});



// Enable edit
$("#panelEditBtn").on("click", function () {
    setPanelEditMode(true);
    $("#panelContent").focus();
});


// Cancel edit
$("#panelCancelEdit").on("click", function () {
    $("#panelContent").text(originalPanelContent);
    setPanelEditMode(false);
});


// Save only
$("#panelSaveBtn").on("click", async function () {
    if (!currentRecordId) return;

    const $btn = $(this);
    const content = $("#panelContent").text();

    if (content.trim() === originalPanelContent.trim()) {
        alert("No changes to save.");
        return;
    }

    try {
        $btn.prop("disabled", true).text("Saving…");

        const res = await fetch(`/api/saveRecord/${currentRecordId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({ content })
        });

        if (!res.ok) {
            throw new Error(`Save failed (${res.status})`);
        }

        const data = await res.json();

        if (!data.success && !data.updated) {
            throw new Error("Server did not confirm save");
        }

        originalPanelContent = content;
        setPanelEditMode(false);

        showSuccess("Saved", "Generated report updated.");
        table.ajax.reload(null, false);

    } catch (err) {
        console.error("Save error:", err);
        alert("Failed to save report. Please try again.");
    } finally {
        $btn.prop("disabled", false).text("Save");
    }
});


// Save & approve
$("#panelSaveApproveBtn").on("click", async function () {
    if (!currentGeneratedId || !currentRecordId) return;

    const $btn = $(this);
    const content = $("#panelContent").text();

    try {
        $btn.prop("disabled", true).text("Saving & approving…");

        // 1️⃣ Save content
        const saveRes = await fetch(`/api/saveRecord/${currentRecordId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({ content })
        });

        if (!saveRes.ok) {
            throw new Error(`Save failed (${saveRes.status})`);
        }

        const saveData = await saveRes.json();

        if (!saveData.success && !saveData.updated) {
            throw new Error("Server did not confirm save");
        }

        // 2️⃣ Approve record
        const approveRes = await fetch(`/api/statusUpdate`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({
                id: currentRecordId,
                status: 1,
                approved: user_id
            })
        });

        if (!approveRes.ok) {
            throw new Error(`Approve failed (${approveRes.status})`);
        }

        closeGeneratedPanel();
        showSuccess("Approved", "Record saved and approved.");

        table.ajax.reload();
        loadStatusCounts();

    } catch (err) {
        console.error("Save & approve error:", err);
        alert("Failed to save and approve. Please try again.");
    } finally {
        $btn.prop("disabled", false).text("Save & Approve");
    }
});


// Close panel
$("#closeGeneratedPanel").on("click", () => {
    setPanelEditMode(false);
    closeGeneratedPanel();
});



/* ===============================
   EDIT TOGGLE
================================ */
$("#panelEditBtn").on("click", function() {
    if (isEditMode) {
        // Cancel edit
        $("#panelContent").text(originalPanelContent);
        $("#panelContent").attr("contenteditable", "false");
        $("#panelFooter").addClass("hidden");
        isEditMode = false;
        $(this).html('<i class="fa-solid fa-edit mr-1"></i> Edit');
    } else {
        // Enable edit
        $("#panelContent").attr("contenteditable", "true");
        $("#panelContent").focus();
        $("#panelFooter").removeClass("hidden");
        isEditMode = true;
        $(this).html('<i class="fa-solid fa-times mr-1"></i> Cancel');
    }
});

/* ===============================
   SAVE EDITED TEXT
================================ */
$("#saveEditBtn").on("click", function() {
    if (!currentRecordId) return;

    const content = $("#modalContent").text();
    const btn = this;
    btn.disabled = true;
    btn.textContent = "Saving...";

    fetch(`/api/saveRecord/${currentRecordId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ content })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.updated) {
            originalContent = content;
            showSuccess("Changes saved successfully!", "Your edits have been saved.");
            $("#editToggleBtn").click();
            table.ajax.reload();
        } else {
            alert("Failed to save changes.");
        }
    })
    .catch(err => {
        console.error('Error saving:', err);
        alert("Failed to save changes.");
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = "Save Changes";
    });
});

/* ===============================
   SAVE AND APPROVE
================================ */
$("#saveAndApproveBtn").on("click", function() {
    $("#approveModal").removeClass("hidden");
});

$("#cancelApproveBtn").on("click", function() {
    $("#approveModal").addClass("hidden");
});

/* ===============================
   SUCCESS MODAL
================================ */
function showSuccess(title, message) {
    $("#successTitle").text(title);
    $("#successMessage").text(message);
    $("#successModal").removeClass("hidden");
}

$("#closeSuccessBtn").on("click", function() {
    $("#successModal").addClass("hidden");
});

/* ===============================
   CLOSE MODAL
================================ */
$("#closeModal").on("click", function() {
    $("#reportModal").addClass("hidden");
    currentRecordId = null;
    originalContent = '';
    isEditMode = false;
});

$("#cancelEditBtn").on("click", function() {
    $("#editToggleBtn").click();
});

// Close modal on outside click
$("#reportModal").on("click", function(e) {
    if (e.target === this) {
        $(this).addClass("hidden");
        currentRecordId = null;
        originalContent = '';
        isEditMode = false;
    }
});




function setPanelLoading(isLoading) {
    $("#panelSkeleton").toggle(isLoading);
    $("#panelContent")
        .attr("contenteditable", false)
        .toggleClass("opacity-50", isLoading);
}

function setPanelEditMode(enabled) {
    $("#panelContent").attr("contenteditable", enabled);
    $("#panelFooter").toggleClass("hidden", !enabled);
    $("#panelEditBtn").toggleClass("hidden", enabled);
}


