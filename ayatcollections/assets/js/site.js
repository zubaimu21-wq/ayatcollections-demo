/**
 * Header: mobile menu and search panel.
 */
(function () {
	'use strict';

	function setup() {
		var header = document.getElementById('site-header');
		if (!header) {
			return;
		}
		var menuBtn = header.querySelector('.menu-toggle');
		var nav = document.getElementById('primary-nav');
		var searchBtn = header.querySelector('.search-toggle');
		var search = document.getElementById('header-search');

		function closeMenu() {
			if (!menuBtn) { return; }
			menuBtn.setAttribute('aria-expanded', 'false');
			header.classList.remove('menu-open');
			document.body.classList.remove('no-scroll');
		}

		if (menuBtn && nav) {
			menuBtn.addEventListener('click', function () {
				var open = menuBtn.getAttribute('aria-expanded') === 'true';
				menuBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
				header.classList.toggle('menu-open', !open);
				document.body.classList.toggle('no-scroll', !open);
				if (!open) {
					var first = nav.querySelector('a');
					if (first) { first.focus(); }
				}
			});
		}

		if (searchBtn && search) {
			searchBtn.addEventListener('click', function () {
				var open = searchBtn.getAttribute('aria-expanded') === 'true';
				searchBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
				search.hidden = open;
				if (!open) {
					closeMenu();
					var input = search.querySelector('input[type="search"]');
					if (input) { input.focus(); }
				}
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') { return; }
			if (header.classList.contains('menu-open')) {
				closeMenu();
				menuBtn.focus();
			}
			if (search && !search.hidden) {
				search.hidden = true;
				searchBtn.setAttribute('aria-expanded', 'false');
				searchBtn.focus();
			}
		});

		if (nav) {
			nav.addEventListener('click', function (e) { if (e.target.closest('a')) { closeMenu(); } });
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();
