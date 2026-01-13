import $ from 'jquery';
import { setupSearchFilterRecords } from "@/filters/filter-search.js";
import { setupStatusFilter } from "@/filters/filter-status.js";
import { setupYearFilterRecords} from "@/filters/filter-year.js";
import "tabulator-tables/dist/css/tabulator.min.css";
import { TabulatorFull as Tabulator } from "tabulator-tables";

window.$ = window.jQuery = $;

let statusOptions = [];
let table;
let currentStatus = "all";
let currentYear = "all";

function hideLoading() {
    $('#loadingModal')
        .addClass('hidden -z-10')
        .removeClass('z-50');
}

function showLoading() {
    $('#loadingModal')
        .removeClass('hidden -z-10')
        .addClass('z-50');
}


fetch('/api/statuses')
    .then(res => res.json())
    .then(data => {
        statusOptions = data.map(s => ({
            label: s.status_name,
            value: s.id
        }));
        initTabulator();
    });

function applyFilters({ status = currentStatus, year = currentYear } = {}) {
    currentStatus = status;
    currentYear = year;

    const filters = [];

    if (currentStatus !== "all") {
        const formatted = currentStatus.split('-').join(' ');
        filters.push({ field: "status.status_name", type: "=", value: formatted });
    }

    if (currentYear !== "all") {
        filters.push({ field: "created_at", type: "like", value: currentYear });
    }

    if (filters.length > 0) {
        table.setFilter(filters);
    } else {
        table.clearFilter();
    }
}
function initTabulator() {
    table = new Tabulator("#records", {
        ajaxURL: "/table/records",
        ajaxConfig: {
            method: "GET",
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
        },
        ajaxRequesting: function () {
            showLoading();
            return true;
        },
        ajaxResponse: function (url, params, response) {
            hideLoading();
            return response;
        },
        height: "100%",
        rowHeight: 40,
        layout: "fitColumns",
        resizableColumns: false, // 🔒 disable globally
        columnDefaults: {
            resizable: false, // 🔒 also disable per-column safety
        },
        pagination:"local",
        movableColumns:false,
        paginationSize:20,
        paginationCounter:"rows",
        index: "id",
        selectable: false,
        
        columns: [
            {
                title: "SortDate",
                field: "sort_date",
                visible: false,
                mutator: function (value, data) {
                    return new Date(data.created_at).getTime();
                }
            },
            {
                title: "Patient Name",
                field: "patient_name", // a virtual field name
                headerSort: false,
                width: "18%",
                vertAlign: "middle",
                // 👇 Define how to display it
                formatter: function (cell) {
                    const p = cell.getRow().getData().patient;
                    if (!p) return "—";
                    return `${p.last_name}, ${p.first_name} ${p.middle_name || ''}`;
                },
            },
            {
                title: "Patient_name",
                field: "patient_name_filter",
                visible: false,
                mutator: function(value, data, type, mutatorParams){
                    const p = data.patient;
                    if (!p) return "—";
                    return `${p.last_name}, ${p.first_name} ${p.middle_name || ''}`;
                },
            },
            {
                title: "Unit",
                field: "patient.unit",
                headerSort: false,
                width: "10%",
                vertAlign: "middle",

            },
            {
                title: "Phone",
                field: "patient.phone_number",
                headerSort: false,
                width: "10%",
                vertAlign: "middle",

            },
            {
                title: "Gender",
                field: "patient.sex",
                headerSort: false,
                width: "8%",
                vertAlign: "middle",

            },
            {
                title: "Status",
                field: "status.status_name",
                headerSort: false,
                width: "10%",
                vertAlign: "middle",

                // 🧩 Formatter = how it looks (badge)
                formatter: function(cell) {
                    const id = cell.getValue();
                    const record = cell.getRow().getData();
                    const name = record.status?.status_name || "—";
                    let color = "";
                    let statusId = "statusCell-" + record.id;

                    switch (name) {
                        case "approved": color = "#16a34a"; break;  // green
                        case "pending": color = "#f59e0b"; break;   // yellow
                        case "not evaluated": color = "#9ca3af"; break; // gray
                        default: color = "#6b7280";
                    }

                    return `<span id="${statusId}" style="
                            display:inline-block;
                            width: 100%;
                            padding:4px 8px;
                            border-radius:8px;
                            background:${color}20;
                            color:${color};
                            font-weight:600;
                            text-transform:capitalize;
                            margin-inline: 0 12px;
                            text-align: center;
                          ">${name}</span>`;
                },

            },
            {title: "Age", field: "patient.age", headerSort: false, width: "5%", vertAlign: "middle"},
            {
                title: "Date Recorded",
                field: "date",
                headerSort: false,
                width: "12%",
                vertAlign: "middle",
                formatter: function (cell) {
                    const data = cell.getRow().getData();
                    const date = new Date(data.created_at);

                    const formatted = date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    return formatted;
                }

            },
            {
                title: "Actions",
                field: "actions",
                headerSort: false,
                formatter: function (cell) {
                    const data = cell.getRow().getData();
                    let action = ``;

                    if(data.status.status_name === "approved"){
                        action += `
                        <a href="javascript:void(0)"
                           title="Print"
                           onclick="event.stopPropagation(); printRow(${data.id})"
                           class="px-2 hover:scale-110 transition-all ease-out duration-200">
                            <i class="fa-solid fa-print"></i> Print
                        </a>
                    `;
                    } else if (data.status.status_name === "pending" ) {
                        action += `
                        <span
                           class="px-2 hover:scale-110 transition-all ease-out duration-200">
                           Need Approval
                        </span>`;
                    } else {
                        action += `
                        <a href="javascript:void(0)"
                           title="Print"
                           onclick="event.stopPropagation(); editRow(${data.id})"
                           class="px-2 hover:scale-110 transition-all ease-out duration-200">
                            <i class="fa-solid fa-pen"></i> Edit
                        </a>
                    `;
                    }

                    return `<span class="flex flex-row gap-2">${action}</span>`;
                },

            },
        ],
        initialSort:[
            {column:"sort_date", dir:"desc"},
        ],
    });

    table.on("cellEdited", function (cell) {
        // console.log("📋 All table data:", table.getData());
        const data = cell.getRow().getData();


        const confirmation = confirm("Are you sure to change the status?");

        if (confirmation) {
            $.ajax({
                url: `/api/statusUpdate/${data.id}`,
                type: "PUT",
                data: JSON.stringify({status_id: data.status_id}),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (updatedRecord) {
                    console.log("✅ Updated record:", updatedRecord);
                    // 🔄 Only refresh that row
                    const $badge = $(`#statusCell-${updatedRecord.id}`);

                    // Determine new color based on the status name
                    const name = updatedRecord.status?.status_name || "—";
                    let color = "";

                    switch (name) {
                        case "complete":
                            color = "#16a34a";
                            break;       // green
                        case "pending":
                            color = "#f59e0b";
                            break;        // yellow
                        case "not evaluated":
                            color = "#9ca3af";
                            break;  // gray
                        default:
                            color = "#6b7280";
                    }

                    $badge
                        .text(updatedRecord.status.status_name)   // change the text
                        .css({
                            color: color,
                            background: color + "20"  // lighter tone
                        });
                },
                error: function (xhr) {
                    console.error("❌ Failed to update status:", xhr.responseText);
                }
            });
        }
    });

    setupSearchFilterRecords(table, applyFilters);
    setupStatusFilter(table, applyFilters);
    setupYearFilterRecords(table, applyFilters);

    // --- Row Click Sidebar ---
    table.on("rowClick", function (e, row) {
        const data = row.getData();
        const sidebar = document.getElementById("recordSidebar");
        const overlay = document.getElementById("recordOverlay");
        const content = document.getElementById("recordContent");

        const selected = document.querySelector("#records .tabulator-row.selected");
        const rowEl = row.getElement();

        if (selected && selected === rowEl && sidebar.classList.contains("sidebar-open")) {
            closeSidebar();
            return;
        }

        if (selected) selected.classList.remove("selected");
        rowEl.classList.add("selected");

        // Fill Details
        $("#reference-number").text(`REF#${data.id}${new Date(data.created_at).toISOString().slice(8,10)}${new Date(data.created_at).toISOString().slice(5,7)}${new Date(data.created_at).getFullYear()}`);
        $("#created-at").text(`${new Date(data.created_at).toLocaleString()}`);
        $("#status").text(`${data.status?.status_name ?? 'N/A'}`);
        $("#patient-name").text(`${data.patient?.last_name}, ${data.patient?.first_name} ${data.patient?.middle_name ?? ''}`);
        $("#gender").text(`${data.patient?.sex}`);
        $("#age").text(`${data.patient?.age}`);
        $("#height").text(`H: ${data.patient?.height} m`);
        $("#weight").text(`W: ${data.patient?.weight} kg`);
        $("#bmi").text(`BMI: ${data.patient?.bmi ?? 'N/A'}`);
        $("#phone").text(`${data.patient?.phone_number ?? 'N/A'}`);
        $("#unit").text(`${data.patient?.unit ?? 'N/A'}`);
        $("#cholesterol").text(`${data.cholesterol ?? 'N/A'} mg/dL`);
        $("#hdl").text(`${data.hdl_cholesterol ?? 'N/A'} mg/dL`);
        $("#systolic").text(`${data.systolic_bp ?? 'N/A'} mmHg`);
        $("#hba").text(`${data.hba1c ?? 'N/A'}`);
        $("#fbs").text(`${data.fbs ?? 'N/A'}`);

        $(`#edit_button`).data('id', data.id);
        $(`#approve_button`).data('id', data.id);

        const isApproved = data.status_id === 1;

// EDIT button
        if (isApproved) {
            $("#edit_button")
                .addClass("opacity-50 cursor-not-allowed pointer-events-none")
                .attr("title", "This record is already approved");
        } else {
            $("#edit_button")
                .removeClass("opacity-50 cursor-not-allowed pointer-events-none")
                .attr("title", "Edit record");
        }

// APPROVE button
        if (isApproved) {
            $("#approve_button")
                .addClass("opacity-50 bg-[#9ca3af20] text-[#9ca3af] border border-[#9ca3af40] cursor-not-allowed pointer-events-none")
                .attr("title", "This record is already approved");
        } else {
            $("#approve_button")
                .removeClass("opacity-50 cursor-not-allowed pointer-events-none")
                .attr("title", "Approve record");
        }


        // Existing Conditions
        content.innerHTML = `
            <div class="flex flex-wrap gap-2">
                ${data.smoking ? '<span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">Smoking</span>' : ''}
                ${data.diabetes ? '<span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">Diabetes</span>' : ''}
                ${data.hypertension ? '<span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">Hypertension</span>' : ''}
                ${!data.smoking && !data.diabetes && !data.hypertension ? '<span class="text-sm text-gray-500">No known existing conditions</span>' : ''}
            </div>
        `;

        // Show sidebar
        overlay.classList.remove("hidden");
        requestAnimationFrame(() => {
            overlay.classList.add("overlay-visible");
            sidebar.classList.add("sidebar-open");
        });

        // Setup AI analysis section
        setupAnalysisSection(data);
    });
    // --- Sidebar Close ---
    document.getElementById("closeSidebar").addEventListener("click", closeSidebar);
    document.getElementById("recordOverlay").addEventListener("click", e => {
        if (e.target.id === "recordOverlay") closeSidebar();
    });

    window.reloadRow = function (id) {
        const row = table.getRow(id);
        const oldStatus = row ? row.getData().status_id : null;

        $.get(`/api/record/${id}`, function (fresh) {

            if (oldStatus === fresh.status_id) return;

            table.updateOrAddData([fresh]);

            const updatedRow = table.getRow(fresh.id);
            if (updatedRow) {
                const el = updatedRow.getElement();
                el.classList.add("row-updated");

                setTimeout(() => {
                    el.classList.remove("row-updated");
                }, 1000);
            }
        });
    };





}

// --- Analysis Section Handler ---
function setupAnalysisSection(data) {
    const sidebar = document.getElementById("recordSidebar");
    const gradientFade = document.getElementById("gradientFade");

    // Scoped buttons
    const evaluateBtn = document.querySelector("#analysisSection #evaluateBtn");
    const enlargeBtnOpen = document.querySelector("#analysisSection #enlargeBtnOpen");
    const enlargeBtnClose = document.querySelector("#analysisSectionRight #enlargeBtnClose");

    // ✅ Helper to fully reset analysis section state
    function resetAnalysisUI() {
        sidebar.classList.remove("expanded-sidebar");
        $("#analysisSectionRight").addClass("hidden");
        $("#analysisSection").removeClass("hidden");
        gradientFade.style.display = "block";

        $(".generatedText").text("");
        $(evaluateBtn).addClass("hidden");
        $(enlargeBtnOpen).addClass("hidden");
        $(enlargeBtnClose).addClass("hidden");
    }

    // Always reset on load
    resetAnalysisUI();

    // Determine which buttons to show
    if (data.generated_report?.generated_text) {
        $(".generatedText").text(data.generated_report.generated_text);
        $(enlargeBtnOpen).removeClass("hidden");
    } else {
        $(evaluateBtn).removeClass("hidden");
    }

    // 🧠 Evaluate button
    evaluateBtn.onclick = async () => {
        evaluateBtn.disabled = true;
        evaluateBtn.textContent = "Evaluating...";

        try {
            const res = await fetch(`/api/evaluate/${data.id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const result = await res.json();

            $(".generatedText").text(result.summary || "(No analysis returned)");
            $(evaluateBtn).addClass("hidden");
            $(enlargeBtnOpen).removeClass("hidden");

            if (window.reloadRow) {
                window.reloadRow(data.id);
            }

        } catch (err) {
            console.error("❌ Error:", err);
            $(".generatedText").text("Error generating analysis.");
        } finally {
            evaluateBtn.disabled = false;
            evaluateBtn.textContent = "Evaluate Record";
        }
    };


    // 🧩 Enlarge Open
    $(enlargeBtnOpen).off("click").on("click", function () {
        sidebar.classList.add("expanded-sidebar");
        $("#analysisSectionRight").removeClass("hidden");
        $("#analysisSection").addClass("hidden");
        gradientFade.style.display = "none";

        $(enlargeBtnOpen).addClass("hidden");
        $(enlargeBtnClose).removeClass("hidden");
    });

    // 🧩 Enlarge Close
    $(enlargeBtnClose).off("click").on("click", function () {
        sidebar.classList.remove("expanded-sidebar");
        $("#analysisSectionRight").addClass("hidden");
        $("#analysisSection").removeClass("hidden");
        gradientFade.style.display = "block";

        $(enlargeBtnClose).addClass("hidden");
        $(enlargeBtnOpen).removeClass("hidden");
    });

    // 🧱 Also reset when sidebar closes (X or overlay)
    $("#closeSidebar, #recordOverlay").off("click.resetAnalysis").on("click.resetAnalysis", function (e) {
        if (e.target.id === "closeSidebar" || e.target.id === "recordOverlay") {
            resetAnalysisUI();
        }
    });
}

// --- Sidebar Close Helper ---
function closeSidebar() {
    const sidebar = document.getElementById("recordSidebar");
    const overlay = document.getElementById("recordOverlay");
    sidebar.classList.remove("sidebar-open");
    overlay.classList.remove("overlay-visible");

    const selectedRow = document.querySelector("#records .tabulator-row.selected");
    if (selectedRow) selectedRow.classList.remove("selected");

    setTimeout(() => overlay.classList.add("hidden"), 300);
}
// --- Reset Filters ---
$("#reset-filters").click(function () {
    currentStatus = "all";
    currentYear = "all";
    table.clearFilter();

    $("#record-search").val("");
    $("#status-filter-label").text("All");
    $("#year-filter-label").text("All Years");
});

$(document).on('click', '.edit_generated_text', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    window.location.href = `/editGenerate?special=${id}`;
});


let pendingApproveId = null;

// Open modal
$(document).on('click', '.approve_button_text', function (e) {
    e.preventDefault();

    pendingApproveId = $(this).data('id');

    $("#approveModal")
        .removeClass("hidden")
        .addClass("flex");
});

// Cancel
$("#cancelApprove").on("click", function () {
    pendingApproveId = null;

    $("#approveModal")
        .addClass("hidden")
        .removeClass("flex");
});

// Confirm approval
$("#confirmApprove").on("click", function () {
    if (!pendingApproveId) return;

    const id = pendingApproveId;

    $(this).text("Approving...").prop("disabled", true);

    $.ajax({
        url: '/api/statusUpdate',
        method: 'PUT',
        data: {
            id: id,
            status: 1
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (record) {
            $("#approveModal").addClass("hidden").removeClass("flex");
            $("#confirmApprove").text("Yes, Approve").prop("disabled", false);

            // Refresh table row
            if (window.reloadRow) {
                window.reloadRow(record.id);
            }

            // Lock buttons
            $("#edit_button, #approve_button")
                .addClass("opacity-50 cursor-not-allowed pointer-events-none");
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.error || "Failed to approve");
            $("#confirmApprove").text("Yes, Approve").prop("disabled", false);
        }
    });
});

