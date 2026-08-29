// -----------------------------------------------------------------
// script.js - Global JavaScript
// -----------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
    // TODO: form validation, navbar toggle, AJAX calls, etc.

    // Example: fade in the stat cards on load
    const cards = document.querySelectorAll(".card");
    cards.forEach((card, index) => {
        card.style.opacity = 0;
        setTimeout(() => {
            card.style.transition = "opacity 0.4s ease";
            card.style.opacity = 1;
        }, index * 100);
    });
});