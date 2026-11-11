/* Ayatcollections hosted demo: bag, checkout, order confirmation, search and
   sorting that run in the visitor's browser (no server). Orders are not real. */
(function () {
	'use strict';

	var P = location.pathname.indexOf('/ayatcollections-demo') === 0 ? '/ayatcollections-demo' : '';
	var KEY = 'ayatDemoBag';
	var ORDER = 'ayatDemoOrder';
	var DELIVERY = 250;
	var FREE_OVER = 5000;

	/* ---------- helpers ---------- */
	function $(s, r) { return (r || document).querySelector(s); }
	function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
	function money(n) { return 'Rs ' + Math.round(n).toLocaleString('en-US'); }
	function num(t) { var m = String(t || '').replace(/,/g, '').match(/(\d+(?:\.\d+)?)(?!.*\d)/); return m ? parseFloat(m[1]) : 0; }
	function load(k, d) { try { return JSON.parse(localStorage.getItem(k)) || d; } catch (e) { return d; } }
	function save(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* private mode */ } }
	function bag() { return load(KEY, []); }
	function setBag(b) { save(KEY, b); updateCount(); }
	function lastAmount(el) { var a = el ? $$('.woocommerce-Price-amount', el) : []; return a.length ? num(a[a.length - 1].textContent) : 0; }

	/* ---------- styles for the demo views ---------- */
	var css = document.createElement('style');
	css.textContent = [
		'.demo-toast{position:fixed;left:16px;right:16px;bottom:16px;z-index:9999;max-width:440px;margin:0 auto;background:#22092f;color:#fff;padding:14px 16px;box-shadow:0 10px 30px rgba(0,0,0,.3);display:flex;gap:12px;align-items:center;justify-content:space-between;font-size:15px}',
		'.demo-toast a{color:#e4c77f;font-weight:600;white-space:nowrap}',
		'.demo-view h1{margin-bottom:20px}',
		'.demo-empty{padding:40px 0;text-align:center}',
		'.demo-lines{list-style:none;margin:0 0 24px;padding:0;border-top:1px solid var(--purple-line)}',
		'.demo-line{display:grid;grid-template-columns:72px minmax(0,1fr) auto;gap:14px;align-items:start;padding:16px 0;border-bottom:1px solid var(--purple-line)}',
		'.demo-line img{width:72px;aspect-ratio:1;object-fit:cover;background:#fff}',
		'.demo-line h3{font-size:1.1rem;margin:0 0 2px}.demo-line h3 a{text-decoration:none}',
		'.demo-line small{display:block;color:var(--muted)}',
		'.demo-qty{display:inline-flex;align-items:center;border:1px solid var(--purple-line);margin-top:8px;background:#fff}',
		'.demo-qty button{width:40px;height:40px;border:0;background:none;font-size:18px;cursor:pointer;color:var(--purple)}',
		'.demo-qty span{min-width:28px;text-align:center}',
		'.demo-remove{background:none;border:0;color:var(--muted);text-decoration:underline;cursor:pointer;padding:8px 0 0;font-size:.85rem;display:block}',
		'.demo-price{font-weight:500;text-align:right;white-space:nowrap}',
		'.demo-grid{display:grid;grid-template-columns:minmax(0,1.3fr) minmax(0,1fr);gap:32px;align-items:start}',
		'.demo-summary{background:#fff;border:1px solid var(--purple-line);padding:20px}',
		'.demo-summary h2{font-size:1.5rem}',
		'.demo-row{display:flex;justify-content:space-between;gap:12px;padding:6px 0}',
		'.demo-row.total{border-top:1px solid var(--purple-line);margin-top:8px;padding-top:12px;font-weight:600;font-size:1.1rem}',
		'.demo-summary .btn{width:100%;margin-top:14px}',
		'.demo-form label{display:block;font-weight:500;font-size:.92rem;margin:0 0 4px}',
		'.demo-form .f{margin:0 0 14px}',
		'.demo-form input,.demo-form select,.demo-form textarea{width:100%;min-height:48px;padding:10px 12px;border:1px solid var(--purple-line);background:#fff;font-size:16px;border-radius:0}',
		'.demo-form .two{display:grid;grid-template-columns:1fr 1fr;gap:12px}',
		'.demo-pay{border:1px solid var(--purple-line);background:#fff;padding:14px;margin:6px 0 14px}',
		'.demo-form input[type=radio]{width:20px;height:20px;min-height:0;padding:0;margin:0;flex:0 0 auto;accent-color:var(--purple)}',
		'.demo-row span:last-child{white-space:nowrap}',
		'.demo-pay label{display:flex;gap:10px;align-items:center;font-weight:500;margin:0}',
		'.demo-pay p{margin:6px 0 0 28px;color:var(--muted);font-size:.9rem}',
		'.demo-err{color:#a12626;font-size:.85rem;margin-top:4px}',
		'.demo-note{font-size:.85rem;color:var(--muted);margin-top:12px}',
		'.demo-thanks{background:var(--purple-soft,#f5eff8);border-left:3px solid var(--gold);padding:18px 20px;font-family:var(--font-serif);font-size:1.5rem;color:var(--purple-ink);margin-bottom:20px}',
		'.demo-meta{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:24px}',
		'.demo-meta div{border:1px solid var(--purple-line);background:#fff;padding:12px}.demo-meta small{display:block;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;font-size:.7rem}',
		'@media (max-width:800px){.demo-grid{grid-template-columns:minmax(0,1fr)}}',
		'@media (max-width:420px){.demo-form .two{grid-template-columns:1fr}.demo-line{grid-template-columns:60px minmax(0,1fr) auto;gap:10px}.demo-line img{width:60px}}'
	].join('');
	document.head.appendChild(css);

	/* ---------- toast ---------- */
	var toastTimer;
	function toast(html) {
		var t = $('.demo-toast');
		if (!t) { t = document.createElement('div'); t.className = 'demo-toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
		t.innerHTML = html;
		t.hidden = false;
		clearTimeout(toastTimer);
		toastTimer = setTimeout(function () { t.hidden = true; }, 4500);
	}

	/* ---------- header bag ---------- */
	function updateCount() {
		var n = bag().reduce(function (s, i) { return s + i.qty; }, 0);
		$$('.header-bag').forEach(function (a) {
			a.setAttribute('href', P + '/bag/');
			a.setAttribute('aria-label', 'Bag, ' + n + (n === 1 ? ' item' : ' items'));
			var c = $('.bag-count', a);
			if (c) { c.textContent = n; c.classList.toggle('is-empty', !n); }
		});
	}

	function addItem(item) {
		var b = bag();
		var found = b.filter(function (i) { return i.key === item.key; })[0];
		if (found) { found.qty += item.qty; } else { b.push(item); }
		setBag(b);
		toast('<span>Added to bag: ' + esc(item.name) + '</span><a href="' + P + '/bag/">View bag</a>');
	}

	/* ---------- add to bag: product page ---------- */
	document.addEventListener('submit', function (e) {
		var f = e.target;

		if (f.matches('form.cart')) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var sels = $$('.variations select', f);
			var missing = sels.filter(function (s) { return !s.value; });
			if (missing.length) { toast('<span>Please choose a size first.</span>'); missing[0].focus(); return; }
			var variant = sels.map(function (s) { var l = $('label[for="' + s.id + '"]', f); return (l ? l.textContent.trim() + ': ' : '') + s.options[s.selectedIndex].text; }).join(', ');
			var price = lastAmount($('.woocommerce-variation-price', f)) || lastAmount($('.summary .price')) || lastAmount($('p.price'));
			var img = $('.woocommerce-product-gallery img');
			var qty = Math.max(1, parseInt(($('input.qty', f) || {}).value, 10) || 1);
			addItem({
				key: location.pathname + '|' + variant,
				name: ($('.product_title') || {}).textContent || document.title,
				variant: variant,
				price: price,
				qty: qty,
				img: img ? (img.getAttribute('src') || '') : '',
				url: location.pathname
			});
			return;
		}

		if (f.matches('.woocommerce-ordering')) { e.preventDefault(); e.stopImmediatePropagation(); sortProducts($('select.orderby', f).value); return; }

		if (f.matches('.ayat-search, .woocommerce-product-search, .search-form')) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var q = ($('input[type=search]', f) || {}).value || '';
			location.href = P + '/search/?q=' + encodeURIComponent(q.trim());
		}
	}, true);

	/* ---------- clicks: loop add to bag, account ---------- */
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a');
		if (!a) { return; }
		var href = a.getAttribute('href') || '';
		if (/add-to-cart=/.test(href) && a.closest('li.product')) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var li = a.closest('li.product');
			var link = $('a.woocommerce-LoopProduct-link', li);
			var img = $('img', li);
			addItem({
				key: (link ? link.getAttribute('href') : href) + '|',
				name: ($('.woocommerce-loop-product__title', li) || {}).textContent || '',
				variant: '',
				price: lastAmount($('.price', li)),
				qty: 1,
				img: img ? img.getAttribute('src') : '',
				url: link ? link.getAttribute('href') : '#'
			});
			return;
		}
		if (a.matches('.header-account') || /\/my-account\//.test(href)) {
			e.preventDefault();
			toast('<span>Customer accounts are available on the live store.</span>');
		}
	}, true);

	/* ---------- sorting on shop/collection pages ---------- */
	function sortProducts(mode) {
		var ul = $('ul.products');
		if (!ul) { return; }
		var items = $$('li.product', ul);
		items.forEach(function (li, i) { if (li.dataset.idx === undefined) { li.dataset.idx = i; } });
		items.sort(function (a, b) {
			var pa = lastAmount($('.price', a)), pb = lastAmount($('.price', b));
			if (mode === 'price') { return pa - pb; }
			if (mode === 'price-desc') { return pb - pa; }
			return a.dataset.idx - b.dataset.idx;
		});
		items.forEach(function (li) { ul.appendChild(li); });
	}
	var ob = $('select.orderby');
	if (ob) {
		ob.addEventListener('change', function (e) { e.stopImmediatePropagation(); sortProducts(ob.value); }, true);
		// WooCommerce submits the sort form on change; there is no server here, so detach it.
		if (window.jQuery) {
			window.jQuery(function () {
				setTimeout(function () {
					window.jQuery('.woocommerce-ordering').off('change');
					window.jQuery('select.orderby').off('change').on('change', function () { sortProducts(this.value); });
				}, 0);
			});
		}
	}

	/* ---------- views: bag, checkout, order, search ---------- */
	var app = $('#ayat-app');
	if (app) {
		var view = app.getAttribute('data-view');
		if (view === 'bag') { renderBag(); }
		if (view === 'checkout') { renderCheckout(); }
		if (view === 'order') { renderOrder(); }
		if (view === 'search') { renderSearch(); }
	}

	function totals(b) {
		var sub = b.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
		var del = sub >= FREE_OVER || !sub ? 0 : DELIVERY;
		return { sub: sub, del: del, total: sub + del };
	}

	function summaryRows(t) {
		return '<div class="demo-row"><span>Subtotal</span><span>' + money(t.sub) + '</span></div>' +
			'<div class="demo-row"><span>Delivery</span><span>' + (t.del ? money(t.del) : 'Free') + '</span></div>' +
			'<div class="demo-row total"><span>Total</span><span>' + money(t.total) + '</span></div>';
	}

	function renderBag() {
		var b = bag();
		if (!b.length) {
			app.innerHTML = '<div class="demo-view"><h1 class="page-title">Your Bag</h1><div class="demo-empty"><p>Your bag is empty.</p><p><a class="btn" href="' + P + '/shop/">Continue shopping</a></p></div></div>';
			return;
		}
		var t = totals(b);
		app.innerHTML = '<div class="demo-view"><h1 class="page-title">Your Bag</h1><div class="demo-grid"><ul class="demo-lines">' +
			b.map(function (i, n) {
				return '<li class="demo-line"><a href="' + esc(i.url) + '"><img src="' + esc(i.img) + '" alt=""></a><div><h3><a href="' + esc(i.url) + '">' + esc(i.name) + '</a></h3>' +
					(i.variant ? '<small>' + esc(i.variant) + '</small>' : '') + '<small>' + money(i.price) + ' each</small>' +
					'<div class="demo-qty"><button type="button" data-q="' + n + '" data-d="-1" aria-label="Decrease quantity">−</button><span>' + i.qty + '</span><button type="button" data-q="' + n + '" data-d="1" aria-label="Increase quantity">+</button></div>' +
					'<button type="button" class="demo-remove" data-r="' + n + '">Remove</button></div><div class="demo-price">' + money(i.price * i.qty) + '</div></li>';
			}).join('') +
			'</ul><aside class="demo-summary"><h2>Order summary</h2>' + summaryRows(t) +
			(t.del ? '<p class="demo-note">Free delivery on orders over ' + money(FREE_OVER) + '.</p>' : '') +
			'<a class="btn" href="' + P + '/checkout/">Proceed to checkout</a><a class="link-more" style="display:inline-block;margin-top:14px" href="' + P + '/shop/">Continue shopping</a></aside></div></div>';
		app.onclick = function (e) {
			var q = e.target.closest('[data-q]'), r = e.target.closest('[data-r]');
			if (!q && !r) { return; }
			var b2 = bag();
			if (q) { var i = b2[+q.dataset.q]; i.qty = Math.max(1, i.qty + (+q.dataset.d)); }
			if (r) { b2.splice(+r.dataset.r, 1); }
			setBag(b2);
			renderBag();
		};
	}

	function renderCheckout() {
		var b = bag();
		if (!b.length) { location.replace(P + '/bag/'); return; }
		var t = totals(b);
		var cities = ['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala', 'Hyderabad', 'Other'];
		app.innerHTML = '<div class="demo-view"><h1 class="page-title">Checkout</h1><form class="demo-form demo-grid" novalidate><div>' +
			'<h2>Delivery details</h2>' +
			'<div class="f"><label for="d-name">Full name *</label><input id="d-name" name="name" autocomplete="name" required></div>' +
			'<div class="two"><div class="f"><label for="d-phone">Phone *</label><input id="d-phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="03XX XXXXXXX" required></div>' +
			'<div class="f"><label for="d-email">Email (optional)</label><input id="d-email" name="email" type="email" autocomplete="email"></div></div>' +
			'<div class="f"><label for="d-address">Address *</label><input id="d-address" name="address" autocomplete="street-address" placeholder="House, street, area" required></div>' +
			'<div class="f"><label for="d-city">City *</label><select id="d-city" name="city" required><option value="">Choose a city</option>' + cities.map(function (c) { return '<option>' + c + '</option>'; }).join('') + '</select></div>' +
			'<div class="f"><label for="d-notes">Order notes (optional)</label><textarea id="d-notes" name="notes" rows="3"></textarea></div>' +
			'<h2>Payment</h2><div class="demo-pay"><label><input type="radio" name="pay" value="cod" checked> Cash on Delivery</label><p>Pay in cash when your order is delivered.</p></div>' +
			'</div><aside class="demo-summary"><h2>Your order</h2>' +
			b.map(function (i) { return '<div class="demo-row"><span>' + esc(i.name) + (i.variant ? ' <small style="color:var(--muted)">(' + esc(i.variant) + ')</small>' : '') + ' × ' + i.qty + '</span><span>' + money(i.price * i.qty) + '</span></div>'; }).join('') +
			summaryRows(t) + '<button type="submit" class="btn">Place order</button><p class="demo-note">Demo store: no real order or payment is made.</p></aside></form></div>';
		var form = $('form', app);
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			$$('.demo-err', form).forEach(function (x) { x.remove(); });
			var bad = [];
			['name', 'phone', 'address', 'city'].forEach(function (n) {
				var el = form.elements[n];
				var ok = el.value.trim() && (n !== 'phone' || /^[0-9+\-\s]{10,15}$/.test(el.value.trim()));
				if (!ok) { bad.push(el); var m = document.createElement('div'); m.className = 'demo-err'; m.textContent = n === 'phone' ? 'Enter a valid phone number.' : 'This field is required.'; el.after(m); }
			});
			if (bad.length) { bad[0].focus(); return; }
			var order = {
				no: 'AYC-' + Math.floor(100000 + Math.random() * 900000),
				date: new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }),
				name: form.elements.name.value.trim(),
				city: form.elements.city.value,
				items: b,
				totals: totals(b)
			};
			save(ORDER, order);
			setBag([]);
			location.href = P + '/order/';
		});
	}

	function renderOrder() {
		var o = load(ORDER, null);
		if (!o) { location.replace(P + '/'); return; }
		app.innerHTML = '<div class="demo-view"><h1 class="page-title">Order received</h1>' +
			'<p class="demo-thanks">Thank you, ' + esc(o.name.split(' ')[0]) + '. Your order has been received.</p>' +
			'<div class="demo-meta"><div><small>Order number</small>' + esc(o.no) + '</div><div><small>Date</small>' + esc(o.date) + '</div><div><small>Total</small>' + money(o.totals.total) + '</div><div><small>Payment</small>Cash on Delivery</div></div>' +
			'<div class="demo-summary"><h2>Order details</h2>' +
			o.items.map(function (i) { return '<div class="demo-row"><span>' + esc(i.name) + (i.variant ? ' (' + esc(i.variant) + ')' : '') + ' × ' + i.qty + '</span><span>' + money(i.price * i.qty) + '</span></div>'; }).join('') +
			summaryRows(o.totals) + '<p class="demo-note">Delivery to ' + esc(o.city) + ' in 3–5 working days. Please keep the exact amount ready.</p></div>' +
			'<p style="margin-top:24px"><a class="btn" href="' + P + '/shop/">Continue shopping</a></p></div>';
	}

	function renderSearch() {
		var q = (new URLSearchParams(location.search).get('q') || '').trim();
		app.innerHTML = '<div class="demo-view"><h1 class="page-title">' + (q ? 'Results for “' + esc(q) + '”' : 'Search') + '</h1>' +
			'<form class="ayat-search" role="search" style="margin:0 0 24px"><label class="screen-reader-text" for="dq">Search products</label><input id="dq" type="search" class="search-field" value="' + esc(q) + '" placeholder="Search earrings, necklaces, rings…"><button type="submit" class="ayat-search__btn" aria-label="Search">⌕</button></form>' +
			'<div class="woocommerce" id="demo-results"><p>Searching…</p></div></div>';
		fetch(P + '/shop/').then(function (r) { return r.text(); }).then(function (html) {
			var doc = new DOMParser().parseFromString(html, 'text/html');
			var words = q.toLowerCase().split(/\s+/).filter(Boolean);
			var hits = $$('li.product', doc).filter(function (li) {
				var text = li.textContent.toLowerCase();
				return words.every(function (w) { return text.indexOf(w) !== -1; });
			});
			var out = $('#demo-results');
			out.innerHTML = hits.length ? '<ul class="products columns-4">' + hits.map(function (li) { return li.outerHTML; }).join('') + '</ul>' :
				'<p>No products match “' + esc(q) + '”. Try “pearl”, “kundan”, “ring” or browse the <a href="' + P + '/shop/">shop</a>.</p>';
		});
	}

	updateCount();
})();
