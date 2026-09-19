// assets/js/seeker-browse-jobs.js
// Auto-submit the filter form whenever a dropdown changes,
// so the user doesn't have to click "Search Jobs" for filters.

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".filter-row select").forEach(function (select) {
    select.addEventListener("change", function () {
      select.closest("form").submit();
    });
  });
});
