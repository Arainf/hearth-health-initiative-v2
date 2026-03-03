import $ from "jquery";
import { createTiptapEditor } from "@/tip-tap/index.js";
import { createIcons, icons} from "lucide";

window.$ = window.jQuery = $;

let reportEditor = null;
let originalPanelContent = ``;
let isEditMode = false;

const currentRecordId = window.reportConfig.id;
const modeSave = window.reportConfig.modeSave;
const modeSaveApprove = window.reportConfig.modeApprove;
const token = window.reportConfig.token;


document.addEventListener('DOMContentLoaded', () => {
    // Initialize editor with existing content
    reportEditor = createTiptapEditor({
        element: document.querySelector('[x-ref="editor"]'),
        content: window.reportConfig.initialContent || '', // Pass from blade
        editable: false,
    });

    // Keep the toolbar helper for the buttons
    window.ReportEditor = {
        toggleBold: () => reportEditor.toggleBold(),
        toggleItalic: () => reportEditor.toggleItalic(),
        toggleUnderline: () => reportEditor.toggleUnderline(),
        toggleStrike: () => reportEditor.toggleStrike(),
        toggleHeading: l => reportEditor.toggleHeading(l),
        toggleSize: s => reportEditor.toggleSize(s),
        toggleBulletList: () => reportEditor.toggleBulletList(),
        toggleOrderedList: () => reportEditor.toggleOrderedList(),
        setAlign: a => reportEditor.setAlign(a),
        setEditable: v => reportEditor.setEditable(v),
        getHTML: () => reportEditor.getHTML(),
        isActive: (a, opts) => reportEditor.isActive(a, opts),
        setContent: html => reportEditor.setContent(html),
    };
});

function editBtnState(isEditing) {
    isEditMode = !isEditing;
    reportEditor.setEditable(isEditMode);

    if (isEditMode) {
        $("#editorToolbar").css("display", "flex");
        $("#panelFooter").removeClass("hidden");
    } else {
        $("#editorToolbar").css("display", "none");
        $("#panelFooter").addClass("hidden");
    }

    $("#panelEditBtn").html(
        isEditMode
            ? '<i data-lucide="x" class="mr-1 h-3 w-3"></i> Cancel'
            : '<i data-lucide="edit" class="mr-1 h-3 w-3"></i> Edit'
    );

    createIcons({icons});
}

$("#panelEditBtn").on("click", function() {
    editBtnState(isEditMode);
});


$("#panelSaveBtn").on("click", function () {
    const content = reportEditor.getHTML();
    const $btn = $(this);

    $btn.prop("disabled", true).text("Saving…");

    $.ajax({
        url: `/store/` + token,
        method: "POST",
        contentType: "application/json",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        data: JSON.stringify({ content, id : currentRecordId, mode : modeSave }),
        success: function () {
            alert("Report saved successfully.");
            // Optional: window.location.reload();
        },
        error: () => alert("Failed to save."),
        complete: () => $btn.prop("disabled", false).text("Save")
    });
});

$("#panelSaveApproveBtn").on("click", function () {
    const content = reportEditor.getHTML();
    const $btn = $(this);

    $btn.prop("disabled", true).text("Approving…");

    $.ajax({
        url: `/store/` + token,
        method: "POST",
        contentType: "application/json",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        data: JSON.stringify({ id: currentRecordId, mode: modeSaveApprove, content: content }),
        success: function () {
            alert("Report Approved!");
            window.close(); // Close the tab if it was opened via _blank
        },
        error: () => alert("Approval failed."),
        complete: () => $btn.prop("disabled", false).text("Save & Approve")
    });
});


$("#panelSaveBtn").on("click", function () {
    const content = reportEditor.getHTML();
    const $btn = $(this);

    $btn.prop("disabled", true).text("Saving…");

    $.ajax({
        url: `/store/` + token,
        method: "POST",
        contentType: "application/json",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        data: JSON.stringify({ content, id : currentRecordId, mode : modeSave }),
        success: function () {
            alert("Report saved successfully.");
            // Optional: window.location.reload();
        },
        error: () => alert("Failed to save."),
        complete: () => $btn.prop("disabled", false).text("Save")
    });
});

$("#panelSaveApproveBtn").on("click", function () {
    const content = reportEditor.getHTML();
    const $btn = $(this);

    $btn.prop("disabled", true).text("Approving…");

    $.ajax({
        url: `/store/` + token,
        method: "POST",
        contentType: "application/json",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        data: JSON.stringify({ id: currentRecordId, mode: modeSaveApprove, content: content }),
        success: function () {
            alert("Report Approved!");
            window.close(); // Close the tab if it was opened via _blank
        },
        error: () => alert("Approval failed."),
        complete: () => $btn.prop("disabled", false).text("Save & Approve")
    });
});


window.handleBack = function() {
    // 1. Check for unsaved changes if currently in edit mode
    if (isEditMode) {
        const currentContent = reportEditor.getHTML();
        // Compare current content with the initial content passed from Blade
        if (currentContent.trim() !== window.reportConfig.initialContent.trim()) {
            if (!confirm("You have unsaved changes. Are you sure you want to go back?")) {
                return; // User cancelled the back action
            }
        }
    }

    // 2. Go back in browser history
    // This preserves the DataTable state (page, search, filters) on the previous page
    if (document.referrer.indexOf(window.location.host) !== -1) {
        history.back();
    } else {
        // Fallback: If there's no history, redirect to the main patient list
        window.location.href = `/page/${window.reportConfig.token}`;
    }
};

