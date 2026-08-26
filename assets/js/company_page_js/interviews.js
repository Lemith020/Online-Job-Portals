function openInterviewModal() {
    document.getElementById("interviewModal").classList.add("open");
}

function closeInterviewModal() {
    document.getElementById("interviewModal").classList.remove("open");
}

// If we arrived from Applicants page with a pre-selected app_id, open the modal automatically
document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    if (params.has("app_id")) {
        openInterviewModal();
    }
});
