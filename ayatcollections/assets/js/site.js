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

	// Jewellery box pause/play (the box opens and closes on a loop) + gentle scroll reveal.
	function motion() {
		var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var box = document.querySelector('[data-jbox]');
		var toggle = document.querySelector('[data-jbox-toggle]');
		if (box && toggle) {
			toggle.addEventListener('click', function () {
				var paused = box.classList.toggle('is-paused');
				toggle.setAttribute('aria-pressed', paused ? 'true' : 'false');
				toggle.querySelector('[data-label]').textContent = paused ? toggle.getAttribute('data-play') : toggle.getAttribute('data-pause');
				toggle.querySelector('[aria-hidden]').textContent = paused ? '▶' : '❚❚';
			});
		}
		var items = document.querySelectorAll('[data-reveal]');
		if (still || !('IntersectionObserver' in window) || !items.length) { return; }
		document.documentElement.classList.add('js-reveal');
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		items.forEach(function (el) { io.observe(el); });
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', motion);
	} else {
		motion();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setup);
	} else {
		setup();
	}
})();

/* Homepage carousel: arrows, dots, autoplay (pauses on hover/focus or when hidden). */
(function () {
	var root = document.querySelector('[data-slider]');
	if (!root) return;
	var track = root.querySelector('[data-slider-track]');
	var slides = track.children;
	var dots = root.querySelectorAll('[data-slider-dots] button');
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var index = 0, timer = null, paused = false;
	function paint() {
		for (var d = 0; d < dots.length; d++) {
			if (d === index) dots[d].setAttribute('aria-current', 'true'); else dots[d].removeAttribute('aria-current');
		}
	}
	var moving = false;
	function go(i) {
		index = (i + slides.length) % slides.length;
		paint();
		moving = true;
		setTimeout(function () { moving = false; }, 700);
		track.scrollTo({ left: slides[index].offsetLeft, behavior: reduce ? 'instant' : 'smooth' });
	}
	function mark() {
		if (moving || !track.clientWidth) return;
		index = Math.round(track.scrollLeft / track.clientWidth);
		paint();
	}
	var t;
	track.addEventListener('scroll', function () { clearTimeout(t); t = setTimeout(mark, 60); }, { passive: true });
	root.querySelector('[data-slider-prev]').addEventListener('click', function () { go(index - 1); });
	root.querySelector('[data-slider-next]').addEventListener('click', function () { go(index + 1); });
	for (var d = 0; d < dots.length; d++) {
		dots[d].addEventListener('click', (function (n) { return function () { go(n); }; })(d));
	}
	function wait() { return parseInt(slides[index].getAttribute('data-duration'), 10) || 5000; }
	function start() { stop(); timer = setTimeout(function tick() { if (!paused && !document.hidden) { go(index + 1); } timer = setTimeout(tick, wait()); }, wait()); }
	function stop() { if (timer) clearTimeout(timer); }
	root.addEventListener('mouseenter', function () { paused = true; });
	root.addEventListener('mouseleave', function () { paused = false; });
	root.addEventListener('focusin', function () { paused = true; });
	root.addEventListener('focusout', function () { paused = false; });
	track.addEventListener('touchstart', function () { paused = true; }, { passive: true });
	track.addEventListener('touchend', function () { setTimeout(function () { paused = false; }, 4000); }, { passive: true });
	start();
})();

/* Category strip: bring the current category into view. */
(function () {
	var cur = document.querySelector('.cat-strip__link.is-current');
	if (!cur) return;
	var list = cur.closest('.cat-strip__list');
	if (list && list.scrollWidth > list.clientWidth) {
		list.scrollLeft = cur.parentElement.offsetLeft - (list.clientWidth - cur.offsetWidth) / 2;
	}
})();
