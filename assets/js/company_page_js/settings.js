document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const newPassword = document.getElementById("new_password");
    const confirmPassword = document.getElementById("confirm_password");

    if (form && newPassword && confirmPassword) {
        form.addEventListener("submit", function (e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert("New password and confirm password do not match.");
            }
        });
    }
});
