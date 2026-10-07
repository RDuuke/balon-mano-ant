(function () {
	'use strict';

	var navigation = document.querySelector('[data-labm-navigation]');
	if (!navigation) {
		return;
	}

	var menuToggle = navigation.querySelector('[data-labm-menu-toggle]');
	var menuPanel = navigation.querySelector('[data-labm-menu-panel]');
	var menuLabel = navigation.querySelector('[data-labm-menu-label]');
	var submenu = navigation.querySelector('[data-labm-submenu]');
	var submenuToggle = navigation.querySelector('[data-labm-submenu-toggle]');
	var submenuPanel = navigation.querySelector('[data-labm-submenu-panel]');
	var mobileQuery = window.matchMedia('(max-width: 767px)');
	var wasMobile = mobileQuery.matches;

	function setSubmenu(open, restoreFocus) {
		if (!submenu || !submenuToggle || !submenuPanel) {
			return;
		}
		submenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		submenuToggle.setAttribute('aria-label', open ? 'Cerrar submenú de Selecciones' : 'Abrir submenú de Selecciones');
		submenu.classList.toggle('is-open', open);
		submenuPanel.hidden = !open;
		if (restoreFocus && !open) {
			submenuToggle.focus();
		}
	}

	function setMenu(open, restoreFocus) {
		if (!menuToggle || !menuPanel) {
			return;
		}
		menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		menuLabel.textContent = open ? 'Cerrar menú' : 'Abrir menú';
		menuToggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
		menuPanel.hidden = !open;
		navigation.classList.toggle('is-menu-open', open);
		if (!open) {
			setSubmenu(false, false);
			if (restoreFocus) {
				menuToggle.focus();
			}
		}
	}

	function syncViewport() {
		var isMobile = mobileQuery.matches;
		if (isMobile !== wasMobile) {
			setMenu(!isMobile, false);
			if (!isMobile) {
				setSubmenu(true, false);
			}
			wasMobile = isMobile;
		}
		if (!isMobile) {
			menuPanel.hidden = false;
			menuToggle.setAttribute('aria-expanded', 'true');
		}
	}

	menuToggle.addEventListener('click', function () {
		setMenu(menuToggle.getAttribute('aria-expanded') !== 'true', true);
	});
	submenuToggle.addEventListener('click', function () {
		setSubmenu(submenuToggle.getAttribute('aria-expanded') !== 'true', false);
	});
	navigation.addEventListener('keydown', function (event) {
		if ('Escape' !== event.key) {
			return;
		}
		if (submenuToggle.getAttribute('aria-expanded') === 'true' && submenu.contains(document.activeElement)) {
			event.preventDefault();
			setSubmenu(false, true);
			return;
		}
		if (mobileQuery.matches && menuToggle.getAttribute('aria-expanded') === 'true') {
			event.preventDefault();
			setMenu(false, true);
		}
	});
	document.addEventListener('click', function (event) {
		if (!navigation.contains(event.target)) {
			setSubmenu(false, false);
			if (mobileQuery.matches) {
				setMenu(false, false);
			}
		}
	});
	navigation.addEventListener('focusout', function (event) {
		window.setTimeout(function () {
			if (event.relatedTarget && navigation.contains(event.relatedTarget)) {
				return;
			}
			setSubmenu(false, false);
			if (mobileQuery.matches && event.relatedTarget) {
				setMenu(false, false);
			}
		}, 0);
	});
	window.addEventListener('resize', syncViewport);

	setSubmenu(!mobileQuery.matches, false);
	setMenu(!mobileQuery.matches, false);
}());
