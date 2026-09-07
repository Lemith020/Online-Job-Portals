// assets/js/seeker-settings.js

document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("confirm-text");
  const btn = document.getElementById("confirm-delete-btn");
  if (input && btn) {
    input.addEventListener("input", function () {
      btn.disabled = input.value.trim() !== "DELETE";
    });
  }
});
