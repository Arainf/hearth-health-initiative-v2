import $ from "jquery";
import DataTablerd from "datatables.net-dt";
import { setupSearchFilterAccounts } from "@/filters/filter-search.js";

window.$ = window.jQuery = $;

/* ===============================
   LOADING
================================ */
function showLoading() {
    $("#loadingModal")
        .removeClass("hidden -z-10")
        .addClass("z-50");
}

function hideLoading() {
    $("#loadingModal")
        .addClass("hidden -z-10")
        .removeClass("z-50");
}

/* ===============================
   DATATABLE INIT
================================ */
const table = $("#accounts-table").DataTable({
    serverSide: true,
    processing: false,
    searching: false,
    pageLength: 20,
    lengthChange: false,
    info: false,
    autoWidth: false,
    paging: true,

    scrollY: "calc(100vh - 260px)",
    scrollCollapse: true,

    order: [[4, "desc"]],

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: "/table/accounts",
        type: "GET",
        data: d => {
            d.search = $("#record-search").val();
            showLoading();
        },
        complete: hideLoading
    },

    columns: [
        {
            data: "is_admin",
            width: "10%",
            className: "text-center",
            render: (data, type, row) => {
                const isAdmin  = row.is_admin == 1;
                const isDoctor = row.is_doctor == 1;

                return `
            <span class="px-3 py-1 text-xs rounded-full font-semibold
                ${
                    isAdmin
                        ? "badge badge-admin"
                        : isDoctor
                            ? "badge badge-doctor"
                            : "badge badge-user"
                }">
                ${isAdmin ? "Admin" : isDoctor ? "Doctor" : "User"}
            </span>
        `;
            }
        },

        { data: "name", width: "22%" },

        { data: "username", width: "22%" },

        {
            data: "ai_access",
            width: "14%",
            className: "text-center",
            render: v => `
                <span class="px-3 py-1 text-xs rounded-full font-semibold
                    ${v
                        ? "badge badge-enabled"
                        : "badge badge-disabled"}">
                    ${v ? "Enabled" : "Disabled"}
                </span>`
        },

        {
            data: "created_at",
            width: "16%",
            render: d =>
                new Date(d).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "short",
                    day: "numeric"
                })
        },

        {
            data: null,
            orderable: false,
            width: "6%",
            className: "text-center",
            render: r => `
                <button
                    class="action-toggle hhi-btn hhi-btn-secondary icon-only"
                    onclick="event.stopPropagation(); toggleActionMenu(${r.id}, this)"
                >
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
            `
        }
    ]
});

window.table = table;

/* ===============================
   SEARCH FILTER
================================ */
setupSearchFilterAccounts(table);

/* ===============================
   FLOATING ACTION MENU
================================ */
function removeFloatingMenus() {
    $(".floating-action-menu").remove();
}

function buildFloatingMenu(row) {
    const hasAI = !!row.ai_access;
    const isAdmin = !!row.is_admin;
    const isDoctor  = !!row.is_doctor;

    const $menu = $(`
        <div class="floating-action-menu dark:bg-[#212121] dark:border-gray-600 border shadow-xl rounded-xl py-2">
            <button class="fam-item text-gray-400" data-action="toggle-admin">
                <i class="fa-solid fa-user-shield text-blue-600 w-4"></i>
                ${isAdmin ? "Remove Admin" : "Make Admin"}
            </button>

            <button class="fam-item text-gray-400" data-action="toggle-ai">
                <i class="fa-solid fa-brain text-green-600 w-4"></i>
                ${hasAI ? "Disable AI Access" : "Enable AI Access"}
            </button>

            <button class="fam-item text-gray-400" data-action="toggle-doctor">
                <i class="fa-solid fa-stethoscope w-4"></i>
                ${isDoctor ? "Remove Doctor" : "Make Doctor"}
            </button>

            <button class="fam-item text-red-600" data-action="delete">
                <i class="fa-solid fa-trash w-4"></i>
                Delete Account
            </button>
        </div>
    `);

    $menu.find(".fam-item").css({
        display: "flex",
        gap: "8px",
        width: "100%",
        padding: "10px 14px",
        background: "transparent",
        border: "none",
        cursor: "pointer",
        textAlign: "left"
    });

    $menu.on("click", ".fam-item", function () {
        const action = $(this).data("action");
        removeFloatingMenus();

        if (action === "toggle-ai") toggleAI(row.id, !hasAI);
        if (action === "toggle-admin") toggleAdmin(row.id, !isAdmin);
        if (action === "toggle-doctor") toggleDoctor(row.id, !isDoctor);
        if (action === "delete") deleteAccount(row.id);
    });

    return $menu;
}

window.toggleActionMenu = function (id, btn) {
    removeFloatingMenus();

    const row = table.rows().data().toArray().find(r => r.id === id);
    if (!row) return;

    const $menu = buildFloatingMenu(row);
    $("body").append($menu);

    const rect = btn.getBoundingClientRect();
    $menu.css({
        top: rect.bottom + window.scrollY + 6,
        left: rect.right + window.scrollX - $menu.outerWidth()
    });
};

$(document).on("click scroll resize", removeFloatingMenus);

/* ===============================
   CONFIRM MODAL
================================ */
let confirmCallback = null;

function openConfirmModal({ title, message, confirmText, danger }, cb) {
    confirmCallback = cb;
    $("#confirmTitle").text(title);
    $("#confirmMessage").text(message);
    $("#confirmOkBtn").text(confirmText)
        .toggleClass("bg-red-600", danger)
        .toggleClass("bg-blue-600", !danger);

    $("#confirmModal").removeClass("hidden");
}

$("#confirmCancelBtn").on("click", () => {
    $("#confirmModal").addClass("hidden");
    confirmCallback = null;
});

$("#confirmOkBtn").on("click", () => {
    if (confirmCallback) confirmCallback();
    $("#confirmModal").addClass("hidden");
});

/* ===============================
   ACTIONS
================================ */
window.toggleAI = (id, enable) => {
    openConfirmModal(
        {
            title: enable ? "Enable AI Access" : "Disable AI Access",
            message: "Are you sure?",
            confirmText: enable ? "Enable" : "Disable"
        },
        () => {
            $.ajax({
                url: `/api/accounts/${id}/ai-access`,
                type: "PUT",
                data: { ai_access: enable ? 1 : 0 },
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => table.ajax.reload(null, false)
            });
        }
    );
};

window.toggleAdmin = (id, makeAdmin) => {
    openConfirmModal(
        {
            title: makeAdmin ? "Make Admin" : "Remove Admin",
            message: "Are you sure?",
            confirmText: makeAdmin ? "Make Admin" : "Remove"
        },
        () => {
            $.ajax({
                url: `/api/accounts/${id}/admin`,
                type: "PUT",
                data: { is_admin: makeAdmin ? 1 : 0 },
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => table.ajax.reload(null, false)
            });
        }
    );
};

window.toggleDoctor = (id, makeDoctor) => {
    openConfirmModal(
        {
            title: makeDoctor ? "Make Doctor" : "Remove Doctor",
            message: "Are you sure?",
            confirmText: makeDoctor ? "Make Doctor" : "Remove"
        },
        () => {
            $.ajax({
                url: `/api/accounts/${id}/doctor`,
                type: "PUT",
                data: { is_doctor: makeDoctor ? 1 : 0 },
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => table.ajax.reload(null, false)
            });
        }
    );
};

window.deleteAccount = id => {
    openConfirmModal(
        {
            title: "Delete Account",
            message: "This action cannot be undone.",
            confirmText: "Delete",
            danger: true
        },
        () => {
            $.ajax({
                url: `/api/accounts/delete/${id}`,
                type: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => table.ajax.reload(null, false)
            });
        }
    );
};
