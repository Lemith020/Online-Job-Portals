// assets/js/seeker-applications.js

function openModal(appId) {
  const modal = document.getElementById("modal-" + appId);
  if (modal) modal.classList.add("show");
}

function closeModal(appId) {
  const modal = document.getElementById("modal-" + appId);
  if (modal) modal.classList.remove("show");
}

// Stop clicks inside the "View" button from also triggering the row click twice,
// and let clicking outside the modal box close it.
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".modal-overlay").forEach(function (overlay) {
    overlay.addEventListener("click", function (e) {
      if (e.target === overlay) overlay.classList.remove("show");
    });
  });
});
