document.addEventListener('DOMContentLoaded', function() {
	// Auto-dismiss alerts after 2 seconds
	var alerts = document.querySelectorAll('.alert, .alert-success, .alert-error, .alert-danger, .appeal-alert');
	alerts.forEach(function(alert) {
		setTimeout(function() {
			alert.style.transition = 'opacity 0.25s ease';
			alert.style.opacity = '0';
			setTimeout(function() {
				alert.style.display = 'none';
			}, 250);
		}, 2000);
	});

	const showRegister = document.getElementById('show-register');
	const showLogin = document.getElementById('show-login');
	const loginForm = document.querySelector('.login-form');
	const registerForm = document.querySelector('.register-form');

	// If window.showRegister is set, show register form by default
	if (registerForm && loginForm && window.showRegister) {
		loginForm.style.display = 'none';
		registerForm.style.display = 'flex';
	}

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

document.addEventListener('DOMContentLoaded', function() {
	var toggles = document.querySelectorAll('.toggle-password');
	toggles.forEach(function(toggle) {
		var container = toggle.closest('.input-icon') || document;
		var pwd = container.querySelector('.login-password');
		var eye = toggle.querySelector('.eye-icon');
		var eyeClosed = toggle.querySelector('.eye-closed-icon');
		var showing = false;
		if (toggle && pwd && eye && eyeClosed) {
			toggle.addEventListener('click', function() {
				showing = !showing;
				pwd.type = showing ? 'text' : 'password';
				eye.style.display = showing ? 'none' : 'inline';
				eyeClosed.style.display = showing ? 'inline' : 'none';
			});
		}
	});
});

document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.calendar-icon').forEach(function(icon) {
		icon.addEventListener('click', function() {
			var input = this.parentElement.querySelector('input[type="date"]');
			if (input) input.focus();
		});
	});
});