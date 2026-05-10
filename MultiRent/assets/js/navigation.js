(function () {
	const nav = document.getElementById('site-navigation');
	if (!nav) {
		return;
	}

	const button = nav.querySelector('.menu-toggle');
	const menu = nav.querySelector('ul');
	if (!button || !menu) {
		return;
	}

	button.addEventListener('click', function () {
		const expanded = button.getAttribute('aria-expanded') === 'true';
		button.setAttribute('aria-expanded', String(!expanded));
		nav.classList.toggle('is-open');
	});
}());