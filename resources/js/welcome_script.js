document.addEventListener('DOMContentLoaded', function() {
	const showRegister = document.getElementById('show-register');
	const showLogin = document.getElementById('show-login');
	const loginForm = document.querySelector('.login-form');
	const registerForm = document.querySelector('.register-form');

	if (showRegister && showLogin && loginForm && registerForm) {
		showRegister.addEventListener('click', function(e) {
			e.preventDefault();
			loginForm.style.display = 'none';
			registerForm.style.display = 'flex';
			registerForm.style.animation = 'fadeIn 0.5s';
		});
		showLogin.addEventListener('click', function(e) {
			e.preventDefault();
			registerForm.style.display = 'none';
			loginForm.style.display = 'flex';
			loginForm.style.animation = 'fadeIn 0.5s';
		});
	}
});
