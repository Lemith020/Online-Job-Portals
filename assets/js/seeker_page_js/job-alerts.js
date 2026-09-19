// assets/js/seeker-job-alerts.js

// assets/js/seeker_page_js/job-alerts.js
function toggleForm() {
    var form = document.getElementById("alert-form");
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}
