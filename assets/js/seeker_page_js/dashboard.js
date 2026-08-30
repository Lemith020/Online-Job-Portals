// assets/js/seeker-dashboard.js
// Nothing dynamic needed yet - kept as a hook for future features
// (e.g. animating the progress bar on load).

document.addEventListener("DOMContentLoaded", function () {
    const fill = document.querySelector(".progress-fill");
    if (fill) {
        const target = fill.style.width;
        fill.style.width = "0%";
        setTimeout(() => { fill.style.width = target; }, 100);
    }
});
