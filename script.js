/**
 * User Login Interface Script
 * Handles client-side form validation, accessibility, and interactive feedback.
 */

document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const emailGroup = document.getElementById('emailGroup');
  const passwordGroup = document.getElementById('passwordGroup');
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const rememberMeCheckbox = document.getElementById('rememberMe');
  const loginBtn = document.getElementById('loginBtn');
  const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
  const signUpLink = document.getElementById('signUpLink');
  const toastNotification = document.getElementById('toastNotification');

  const googleLogin = document.getElementById('googleLogin');
  const linkedinLogin = document.getElementById('linkedinLogin');
  const facebookLogin = document.getElementById('facebookLogin');

  // Load saved email if 'Remember Me' was checked previously
  const savedEmail = localStorage.getItem('login_remembered_email');
  if (savedEmail) {
    emailInput.value = savedEmail;
    rememberMeCheckbox.checked = true;
  }

  // Toast feedback helper
  let toastTimeout;
  function showToast(message, type = 'info') {
    clearTimeout(toastTimeout);
    toastNotification.textContent = message;
    toastNotification.className = `toast-notification show ${type}`;

    toastTimeout = setTimeout(() => {
      toastNotification.className = 'toast-notification';
    }, 3000);
  }

  // Clear errors on input
  function clearFieldError(group, errorElement) {
    group.classList.remove('has-error');
    errorElement.textContent = '';
  }

  function setFieldError(group, errorElement, message) {
    group.classList.add('has-error');
    errorElement.textContent = message;
  }

  emailInput.addEventListener('input', () => {
    clearFieldError(emailGroup, emailError);
  });

  passwordInput.addEventListener('input', () => {
    clearFieldError(passwordGroup, passwordError);
  });

  // Email validation regex
  function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email.trim());
  }

  // Form submission handler
  loginForm.addEventListener('submit', (e) => {
    e.preventDefault();

    let isValid = true;
    const emailVal = emailInput.value.trim();
    const passwordVal = passwordInput.value;

    // Validate Email
    if (!emailVal) {
      setFieldError(emailGroup, emailError, 'Email address is required.');
      isValid = false;
    } else if (!isValidEmail(emailVal)) {
      setFieldError(emailGroup, emailError, 'Please enter a valid email address.');
      isValid = false;
    } else {
      clearFieldError(emailGroup, emailError);
    }

    // Validate Password
    if (!passwordVal) {
      setFieldError(passwordGroup, passwordError, 'Password is required.');
      isValid = false;
    } else if (passwordVal.length < 6) {
      setFieldError(passwordGroup, passwordError, 'Password must be at least 6 characters.');
      isValid = false;
    } else {
      clearFieldError(passwordGroup, passwordError);
    }

    if (!isValid) {
      // Focus on first invalid field
      if (emailGroup.classList.contains('has-error')) {
        emailInput.focus();
      } else if (passwordGroup.classList.contains('has-error')) {
        passwordInput.focus();
      }
      return;
    }

    // Remember Me Persistence
    if (rememberMeCheckbox.checked) {
      localStorage.setItem('login_remembered_email', emailVal);
    } else {
      localStorage.removeItem('login_remembered_email');
    }

    // Simulated login process
    loginBtn.classList.add('loading');
    loginBtn.disabled = true;

    setTimeout(() => {
      loginBtn.classList.remove('loading');
      loginBtn.disabled = false;
      showToast('Logged in successfully!', 'success');
      console.log('User logged in with email:', emailVal);
    }, 1200);
  });

  // Social Login Button Handlers
  if (googleLogin) {
    googleLogin.addEventListener('click', () => {
      showToast('Redirecting to Google authentication...');
    });
  }

  if (linkedinLogin) {
    linkedinLogin.addEventListener('click', () => {
      showToast('Redirecting to LinkedIn authentication...');
    });
  }

  if (facebookLogin) {
    facebookLogin.addEventListener('click', () => {
      showToast('Redirecting to Facebook authentication...');
    });
  }

  // Forgot password click handler
  if (forgotPasswordBtn) {
    forgotPasswordBtn.addEventListener('click', (e) => {
      e.preventDefault();
      showToast('Password reset link sent to your registered email.');
    });
  }

  // Sign up link click handler
  if (signUpLink) {
    signUpLink.addEventListener('click', (e) => {
      e.preventDefault();
      showToast('Redirecting to registration page...');
    });
  }
});
