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
		for (var k = 0; k < slides.length; k++) { slides[k].classList.toggle('is-active', k === index); }
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
	paint();
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

/* Product reviews: open the form, sort, show more. */
(function () {
	var box = document.querySelector('.rv');
	if (!box) return;
	var btn = box.querySelector('.rv__write');
	var form = document.getElementById('rv-form');
	if (btn && form) {
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			form.hidden = open;
			if (!open) { var first = form.querySelector('input[type=radio]'); if (first) first.focus(); }
		});
	}
	var list = box.querySelector('[data-rv-list]');
	var sort = box.querySelector('[data-rv-sort]');
	var more = box.querySelector('[data-rv-more]');
	function limit(all) {
		var cards = list.children;
		for (var i = 0; i < cards.length; i++) { cards[i].hidden = !all && i >= 5; }
	}
	if (list && sort) {
		sort.addEventListener('change', function () {
			var cards = Array.prototype.slice.call(list.children);
			var by = sort.value;
			cards.sort(function (a, b) {
				var ra = +a.dataset.rating, rb = +b.dataset.rating, ta = +a.dataset.time, tb = +b.dataset.time;
				if (by === 'high') return rb - ra || tb - ta;
				if (by === 'low') return ra - rb || tb - ta;
				if (by === 'photos') return (+b.dataset.photos) - (+a.dataset.photos) || tb - ta;
				return tb - ta;
			});
			cards.forEach(function (c) { list.appendChild(c); });
			limit(!more || more.hidden);
		});
	}
	if (more && list) {
		more.addEventListener('click', function () { limit(true); more.hidden = true; });
	}
})();

/* Sale bar countdown: hides the bar when the sale ends. */
(function () {
	var bar = document.querySelector('.sale-bar[data-sale-end]');
	if (!bar) return;
	var end = parseInt(bar.getAttribute('data-sale-end'), 10);
	if (!end) return;
	var el = { d: bar.querySelector('[data-d]'), h: bar.querySelector('[data-h]'), m: bar.querySelector('[data-m]'), s: bar.querySelector('[data-s]') };
	function pad(n) { return (n < 10 ? '0' : '') + n; }
	function tick() {
		var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
		if (!left) { bar.classList.add('is-ended'); clearInterval(t); return; }
		if (el.d) el.d.textContent = pad(Math.floor(left / 86400));
		if (el.h) el.h.textContent = pad(Math.floor(left % 86400 / 3600));
		if (el.m) el.m.textContent = pad(Math.floor(left % 3600 / 60));
		if (el.s) el.s.textContent = pad(left % 60);
	}
	var t = setInterval(tick, 1000);
	tick();
})();

/* Floating Home button: tap = home page (or back to top on home), drag = move it; it snaps to the nearest side and remembers the spot. */
(function () {
	var fab = document.querySelector('[data-home-fab]');
	if (!fab) return;
	var KEY = 'ayatHomeFab', M = 12, start = null, moved = false;
	function size() { return fab.offsetWidth || 60; }
	function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }
	function place(x, y) { fab.style.left = x + 'px'; fab.style.top = y + 'px'; fab.style.bottom = 'auto'; fab.style.right = 'auto'; }
	function snap(save) {
		var s = size(), r = fab.getBoundingClientRect(), W = innerWidth, H = innerHeight;
		var side = (r.left + s / 2) < W / 2 ? 'l' : 'r';
		var y = clamp(r.top, M + 60, H - s - M), x = side === 'l' ? M + 2 : W - s - M - 2;
		fab.classList.add('is-snapping'); place(x, y);
		setTimeout(function () { fab.classList.remove('is-snapping'); }, 400);
		if (save) { try { localStorage.setItem(KEY, JSON.stringify({ side: side, y: y / H })); } catch (e) {} }
	}
	function restore() {
		var p = null;
		try { p = JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) {}
		if (!p) return;
		var s = size(), H = innerHeight, W = innerWidth;
		place(p.side === 'r' ? W - s - M - 2 : M + 2, clamp(p.y * H, M + 60, H - s - M));
	}
	restore();
	fab.addEventListener('dragstart', function (e) { e.preventDefault(); });
	addEventListener('resize', function () { if (fab.style.top) snap(false); });
	fab.addEventListener('pointerdown', function (e) {
		if (e.button) return;
		var r = fab.getBoundingClientRect();
		start = { x: e.clientX, y: e.clientY, dx: e.clientX - r.left, dy: e.clientY - r.top, id: e.pointerId };
		moved = false;
		if (e.pointerType === 'mouse') e.preventDefault();
		try { fab.setPointerCapture(e.pointerId); } catch (err) {}
	});
	fab.addEventListener('pointermove', function (e) {
		if (!start || e.pointerId !== start.id) return;
		if (!moved && Math.abs(e.clientX - start.x) + Math.abs(e.clientY - start.y) < 8) return;
		if (!moved) { moved = true; fab.classList.add('is-dragging'); }
		var s = size();
		place(clamp(e.clientX - start.dx, 0, innerWidth - s), clamp(e.clientY - start.dy, 0, innerHeight - s));
		e.preventDefault();
	});
	function end() {
		if (!start) return;
		start = null;
		if (moved) { fab.classList.remove('is-dragging'); snap(true); }
	}
	fab.addEventListener('pointerup', end);
	fab.addEventListener('pointercancel', end);
	fab.addEventListener('click', function (e) {
		if (moved) { e.preventDefault(); moved = false; return; }
		if (fab.hasAttribute('data-is-home')) {
			e.preventDefault();
			var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
			scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
		}
	});
})();

/* Shop the Look photo slider: swipe/scroll, dots, gentle autoplay (pauses on hover/touch). */
(function () {
	var box = document.querySelector('[data-look-slides]');
	if (!box) return;
	var slides = box.children, dots = document.querySelector('[data-look-dots]');
	if (slides.length < 2 || !dots) return;
	var i = 0, timer = null, reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
	for (var k = 0; k < slides.length; k++) {
		(function (n) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('aria-label', 'Photo ' + (n + 1));
			b.addEventListener('click', function () { go(n); restart(); });
			dots.appendChild(b);
		})(k);
	}
	dots.removeAttribute('aria-hidden');
	function paint() { for (var k = 0; k < dots.children.length; k++) dots.children[k].setAttribute('aria-current', k === i ? 'true' : 'false'); }
	function go(n) { i = (n + slides.length) % slides.length; box.scrollTo({ left: i * box.clientWidth, behavior: reduce ? 'instant' : 'smooth' }); paint(); }
	box.addEventListener('scroll', function () { var n = Math.round(box.scrollLeft / Math.max(1, box.clientWidth)); if (n !== i) { i = n; paint(); } }, { passive: true });
	function stop() { clearInterval(timer); timer = null; }
	function restart() { stop(); timer = setInterval(function () { if (!document.hidden) go(i + 1); }, 3500); }
	box.addEventListener('touchstart', stop, { passive: true }); box.addEventListener('touchend', restart, { passive: true });
	box.addEventListener('keydown', function (e) { if (e.key === 'ArrowRight') { go(i + 1); e.preventDefault(); } if (e.key === 'ArrowLeft') { go(i - 1); e.preventDefault(); } });
	paint(); restart();
})();

/* Header: compact + shadow after scrolling. */
(function () {
	var h = document.querySelector('.site-header--v3');
	if (!h) return;
	var on = false;
	function check() { var s = window.scrollY > 24; if (s !== on) { on = s; h.classList.toggle('is-scrolled', s); } }
	addEventListener('scroll', check, { passive: true });
	check();
})();

/* Banner safety net: 1.8s after a banner becomes active, show it fully even if the browser paused the CSS animation. */
(function () {
	var slides = document.querySelectorAll('.ayat-slide--banner');
	if (!slides.length || !window.MutationObserver) return;
	function arm(sl) {
		clearTimeout(sl._shownT);
		sl.classList.remove('is-shown');
		if (sl.classList.contains('is-active')) sl._shownT = setTimeout(function () { sl.classList.add('is-shown'); }, 1800);
	}
	var mo = new MutationObserver(function (list) {
		list.forEach(function (m) {
			var sl = m.target, act = sl.classList.contains('is-active');
			if (act !== !!sl._wasActive) { sl._wasActive = act; arm(sl); }
		});
	});
	slides.forEach(function (sl) { sl._wasActive = sl.classList.contains('is-active'); arm(sl); mo.observe(sl, { attributes: true, attributeFilter: ['class'] }); });
})();
