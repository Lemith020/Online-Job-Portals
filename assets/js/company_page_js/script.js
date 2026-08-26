document.addEventListener("DOMContentLoaded", function () {

    const hamburgerBtn = document.getElementById("hamburgerBtn");
    const sidebar = document.getElementById("sidebar");

    if (hamburgerBtn && sidebar) {
        hamburgerBtn.addEventListener("click", function () {
            sidebar.classList.toggle("toggled");
        });
    }

    const profileMenu = document.getElementById("profileMenu");
    const profileBtn = document.getElementById("profileBtn");

    if (profileBtn) {
        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            profileMenu.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!profileMenu.contains(e.target)) {
                profileMenu.classList.remove("open");
            }
        });
    }

});
