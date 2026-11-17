/* Easypaisa option for the WooCommerce block checkout. */
(function () {
	var wc = window.wc || {};
	if (!wc.wcBlocksRegistry || !wc.wcSettings || !window.wp || !window.wp.element) return;
	var h = window.wp.element.createElement;
	var decode = (window.wp.htmlEntities && window.wp.htmlEntities.decodeEntities) || function (s) { return s; };

	['ayat_easypaisa'].forEach(function (id) {
		var s = wc.wcSettings.getSetting(id + '_data', null);
		if (!s || !s.accountNumber) return;
		var title = decode(s.title || s.brand);
		var Content = function () {
			return h('div', { className: 'ayat-pay-hint' },
				s.description ? h('p', null, decode(s.description)) : null,
				h('p', { className: 'ayat-pay-hint__acc' },
					h('span', null, decode(s.brand) + ' '),
					h('strong', null, s.accountNumber),
					s.accountTitle ? h('span', null, ' (' + decode(s.accountTitle) + ')') : null
				)
			);
		};
		var Label = function () {
			return h('span', { className: 'ayat-pay-label ayat-pay-label--' + id.replace('ayat_', '') }, h('b', { className: 'ayat-pay-mark', 'aria-hidden': 'true' }, 'easypaisa'), h('span', { className: 'screen-reader-text' }, title));
		};
		wc.wcBlocksRegistry.registerPaymentMethod({
			name: id,
			label: h(Label),
			ariaLabel: title,
			content: h(Content),
			edit: h(Content),
			canMakePayment: function () { return true; },
			supports: { features: s.supports || ['products'] }
		});
	});
})();
