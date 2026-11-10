/**
 * Ayatcollections hero slider.
 * Autoplay 5–6 s, prev/next, indicators, pause/play, swipe, keyboard,
 * and no autoplay when the visitor prefers reduced motion.
 */
(function () {
	'use strict';

	function initHero(hero) {
		var slides = Array.prototype.slice.call(hero.querySelectorAll('.hero__slide'));
		if (slides.length < 2) {
			return;
		}
		var dots = Array.prototype.slice.call(hero.querySelectorAll('.hero__dot'));
		var prev = hero.querySelector('.hero__prev');
		var next = hero.querySelector('.hero__next');
		var toggle = hero.querySelector('.hero__toggle');
		var interval = parseInt(hero.getAttribute('data-interval'), 10) || 5500;
		var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');

		var index = 0;
		var timer = null;
		var userPaused = reduce.matches; // Reduced motion: start paused.
		var hoverPaused = false;

		function show(i) {
			index = (i + slides.length) % slides.length;
			slides.forEach(function (slide, n) {
				var active = n === index;
				slide.classList.toggle('is-active', active);
				if (active) {
					slide.removeAttribute('aria-hidden');
				} else {
					slide.setAttribute('aria-hidden', 'true');
				}
				Array.prototype.forEach.call(slide.querySelectorAll('a, button'), function (el) {
					if (active) {
						el.removeAttribute('tabindex');
					} else {
						el.setAttribute('tabindex', '-1');
					}
				});
				if (active) {
					var img = slide.querySelector('img[loading="lazy"]');
					if (img) {
						img.loading = 'eager';
					}
				}
			});
			dots.forEach(function (dot, n) {
				if (n === index) {
					dot.setAttribute('aria-current', 'true');
				} else {
					dot.removeAttribute('aria-current');
				}
			});
			var id = slides[index].id;
			if (prev) { prev.setAttribute('aria-controls', id); }
			if (next) { next.setAttribute('aria-controls', id); }
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function start() {
			stop();
			if (userPaused || hoverPaused || document.hidden) {
				return;
			}
			timer = window.setInterval(function () {
				show(index + 1);
			}, interval);
		}

		function renderToggle() {
			if (!toggle) {
				return;
			}
			hero.classList.toggle('is-paused', userPaused);
			toggle.setAttribute('aria-label', userPaused ? toggle.getAttribute('data-label-play') : toggle.getAttribute('data-label-pause'));
		}

		// Slides are only announced when the user drives them.
		var viewport = hero.querySelector('.hero__viewport');
		function userMove(i) {
			if (viewport) { viewport.setAttribute('aria-live', 'polite'); }
			show(i);
			start();
		}

		if (prev) { prev.addEventListener('click', function () { userMove(index - 1); }); }
		if (next) { next.addEventListener('click', function () { userMove(index + 1); }); }
		dots.forEach(function (dot) {
			dot.addEventListener('click', function () {
				userMove(parseInt(dot.getAttribute('data-slide'), 10));
			});
		});

		if (toggle) {
			toggle.addEventListener('click', function () {
				userPaused = !userPaused;
				renderToggle();
				start();
			});
		}

		// Pause while pointer or keyboard focus is inside the banner.
		hero.addEventListener('mouseenter', function () { hoverPaused = true; stop(); });
		hero.addEventListener('mouseleave', function () { hoverPaused = false; start(); });
		hero.addEventListener('focusin', function () { hoverPaused = true; stop(); });
		hero.addEventListener('focusout', function (e) {
			if (!hero.contains(e.relatedTarget)) {
				hoverPaused = false;
				start();
			}
		});

		hero.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { userMove(index - 1); }
			if (e.key === 'ArrowRight') { userMove(index + 1); }
		});

		// Swipe.
		var startX = null;
		var startY = null;
		hero.addEventListener('touchstart', function (e) {
			startX = e.touches[0].clientX;
			startY = e.touches[0].clientY;
		}, { passive: true });
		hero.addEventListener('touchend', function (e) {
			if (startX === null) { return; }
			var dx = e.changedTouches[0].clientX - startX;
			var dy = e.changedTouches[0].clientY - startY;
			if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
				userMove(dx < 0 ? index + 1 : index - 1);
			}
			startX = null;
		}, { passive: true });

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) { stop(); } else { start(); }
		});

		var onMotionChange = function () {
			if (reduce.matches) {
				userPaused = true;
				renderToggle();
				stop();
			}
		};
		if (reduce.addEventListener) {
			reduce.addEventListener('change', onMotionChange);
		}

		hero.classList.add('is-ready');
		show(0);
		renderToggle();
		start();
	}

	function boot() {
		Array.prototype.forEach.call(document.querySelectorAll('.hero'), initHero);
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
