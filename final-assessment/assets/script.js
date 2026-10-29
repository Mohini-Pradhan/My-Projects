$(function () {
    $("#userForm").on("submit", function (event) {
        event.preventDefault();

        const name = $("#name").val().trim();
        const email = $("#email").val().trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name) {
            showMessage("Please enter a name.", "danger");
            return;
        }

        if (!emailPattern.test(email)) {
            showMessage("Please enter a valid email address.", "danger");
            return;
        }

        const $button = $("#saveButton");
        $button.prop("disabled", true).text("Saving...");

        $.ajax({
            url: "actions/add_user.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json"
        })
            .done(function (response) {
                showMessage(response.message, response.success ? "success" : "danger");

                if (response.success) {
                    $("#userForm")[0].reset();
                }
            })
            .fail(function (xhr) {
                const response = xhr.responseJSON;
                showMessage(
                    response?.message || "Could not save the user. Please try again.",
                    "danger"
                );
            })
            .always(function () {
                $button.prop("disabled", false).text("Save user");
            });
    });

    $("#clearButton").on("click", function () {
        $("#message").empty();
    });

    $("#changeTitle").on("click", function () {
        $(".title").first().text("PHP Practical Project");
    });

    var welcomeMessage = "Welcome";
    let studentCount = 10;
    const projectName = "PHP Practical Lab";

    console.log(welcomeMessage, studentCount, projectName);
});

function showMessage(message, type) {
    const $alert = $("<div>")
        .addClass("alert alert-" + type + " alert-dismissible fade show")
        .attr("role", "alert")
        .text(message);

    const $close = $("<button>")
        .attr({
            type: "button",
            "data-bs-dismiss": "alert",
            "aria-label": "Close"
        })
        .addClass("btn-close");

    $alert.append($close);
    $("#message").empty().append($alert);
}