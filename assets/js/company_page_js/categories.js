document.addEventListener("DOMContentLoaded", function () {

    // clicking a tag toggles its checkbox + the "selected" look
    document.querySelectorAll("#tagGrid .tag").forEach(function (tag) {
        tag.addEventListener("click", function () {
            const checkbox = tag.querySelector("input[type=checkbox]");
            const checkIcon = tag.querySelector(".check-icon");
            checkbox.checked = !checkbox.checked;
            tag.classList.toggle("selected", checkbox.checked);
            checkIcon.style.display = checkbox.checked ? "inline-block" : "none";
        });
    });

    // live search filter for the category list
    const searchInput = document.getElementById("categorySearch");
    searchInput.addEventListener("input", function () {
        const term = searchInput.value.toLowerCase();
        document.querySelectorAll("#tagGrid .tag").forEach(function (tag) {
            const name = tag.getAttribute("data-name");
            tag.style.display = name.includes(term) ? "flex" : "none";
        });
    });

});
