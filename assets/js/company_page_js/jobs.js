function openJobModal(job) {
    const modal = document.getElementById("jobModal");
    const title = document.getElementById("jobModalTitle");

    if (job) {
        title.innerText = "Edit Job";
        document.getElementById("job_id").value = job.job_id;
        document.getElementById("title").value = job.title;
        document.getElementById("description").value = job.description;
        document.getElementById("category_id").value = job.category_id;
        document.getElementById("location").value = job.location;
        document.getElementById("salary_min").value = job.salary_min;
        document.getElementById("salary_max").value = job.salary_max;
        document.getElementById("job_type").value = job.job_type;
        document.getElementById("expiry_date").value = job.expiry_date;
    } else {
        title.innerText = "Post New Job";
        document.getElementById("job_id").value = "";
        document.querySelector("#jobModal form").reset();
    }

    modal.classList.add("open");
}

function closeJobModal() {
    document.getElementById("jobModal").classList.remove("open");
}
