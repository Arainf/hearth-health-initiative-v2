import $ from "jquery";

window.$ = window.jQuery = $;

$(document).ready(function () {

    const $form = $("form");
    const $submitBtn = $("#createAccountBtn");

    $form.on("submit", function () {
        // show loading modal
        $("#loadingModal").removeClass("hidden");

        // disable submit button
        $submitBtn.prop("disabled", true)
            .addClass("opacity-70 cursor-not-allowed");
    });

    // If redirected back with success (Laravel flash)
    if (window.accountCreated === true) {
        $("#loadingModal").addClass("hidden");
        $("#successModal").removeClass("hidden");
    }

    // Create another account
    $("#createAnotherBtn").on("click", function () {
        window.location.reload();
    });
});
