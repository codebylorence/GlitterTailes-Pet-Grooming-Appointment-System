const loginForm = document.querySelector('.nav-btn-js');
const loginForm2 = document.querySelector('.signup-btn-js');
const showForm = document.querySelector('.login-form-js');

const toggleFormVisibility = () => {

  if (!showForm.classList.contains('showLogin')) {
    showForm.classList.add('showLogin');
  } else
    showForm.classList.remove('showLogin');
}

loginForm.addEventListener('click', toggleFormVisibility);
loginForm2.addEventListener('click', toggleFormVisibility);

function switchToRegister() {
  document.getElementById('register-form').style.display = 'none';
  document.getElementById('Login-form').style.display = '';
}

function switchToLogin() {
  document.getElementById('Login-form').style.display = 'none';
  document.getElementById('register-form').style.display = '';
}