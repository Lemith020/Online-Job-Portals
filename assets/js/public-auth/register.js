document.addEventListener('DOMContentLoaded', function () {
    const roleButtons   = document.querySelectorAll('.role-btn');
    const roleInput     = document.getElementById('roleInput');
    const formTitle     = document.getElementById('formTitle');
    const submitBtn     = document.getElementById('submitBtn');

    const jobSeekerFields = document.querySelectorAll('.role-field-job_seeker');
    const companyFields   = document.querySelectorAll('.role-field-company');

    // Fields that are required ONLY for one role
    const birthDay    = document.getElementById('birth_day');
    const companyName = document.getElementById('company_name');
    const industry     = document.getElementById('industry_type');
    const location      = document.getElementById('location');

    function setRole(role) {
        roleInput.value = role;

        roleButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.role === role);
        });

        const isCompany = role === 'company';

        jobSeekerFields.forEach(el => el.style.display = isCompany ? 'none' : '');
        companyFields.forEach(el => el.style.display = isCompany ? '' : 'none');

        // Toggle required attributes so the browser doesn't block submit
        // on hidden fields
        if (birthDay) birthDay.required = !isCompany;
        if (companyName) companyName.required = isCompany;
        if (industry) industry.required = isCompany;
        if (location) location.required = isCompany;

        formTitle.textContent = 'Register as ' + (isCompany ? 'Company' : 'Job Seeker');
        submitBtn.textContent = 'Register as ' + (isCompany ? 'Company' : 'Job Seeker');
    }

    roleButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            setRole(this.dataset.role);
        });
    });

    // Initialize based on the hidden input's current value
    // (keeps the selected role after a failed submit / validation error)
    setRole(roleInput.value || 'job_seeker');
});