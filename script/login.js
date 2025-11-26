const loginForm = document.querySelector('.nav-btn-js');
const loginForm2 = document.querySelector('.signup-btn-js');
const showForm = document.querySelector('.login-form-js');

const toggleFormVisibility = () => {
  if (!showForm.classList.contains('showLogin')) {
    showForm.classList.add('showLogin');
    enableForm(showForm);
  } else {
    showForm.classList.remove('showLogin');
    disableForm(showForm);
  }
};

loginForm.addEventListener('click', toggleFormVisibility);
loginForm2.addEventListener('click', toggleFormVisibility);

function switchToRegister() {
  const registerForm = document.getElementById('register-form');
  const loginForm = document.getElementById('Login-form');
  
  registerForm.style.display = 'none';
  disableForm(registerForm);

  loginForm.style.display = '';
  enableForm(loginForm);
}

function switchToLogin() {
  const loginForm = document.getElementById('Login-form');
  const registerForm = document.getElementById('register-form');
  
  loginForm.style.display = 'none';
  disableForm(loginForm);

  registerForm.style.display = '';
  enableForm(registerForm);
}

function disableForm(form) {
  const inputs = form.querySelectorAll('input, button, select, textarea');
  inputs.forEach(input => input.disabled = true);
}

function enableForm(form) {
  const inputs = form.querySelectorAll('input, button, select, textarea');
  inputs.forEach(input => input.disabled = false);
}
