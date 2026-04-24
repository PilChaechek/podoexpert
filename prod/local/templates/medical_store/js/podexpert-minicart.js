(function (window) {
	'use strict';

	var COUNT_ENDPOINT = '/local/ajax/podexpert_basket_count.php';

	window.podexpertSetCartCountBadge = function (data) {
		if (!data || typeof data.count !== 'number') {
			return;
		}
		var n = data.count;
		var text = typeof data.countBadge === 'string' ? data.countBadge : (n > 99 ? '99+' : String(n));
		var show = n > 0;
		document.querySelectorAll('.js-header-cart-count').forEach(function (el) {
			el.textContent = text;
			var wrap = el.closest('.minicart__total');
			if (wrap) {
				wrap.classList.toggle('hidden', !show);
			}
		});
		document.querySelectorAll('.js-total-count-minicart').forEach(function (el) {
			el.textContent = text;
			el.classList.toggle('hidden', !show);
		});
	};

	window.podexpertRefreshMinicartFromServer = function () {
		fetch(COUNT_ENDPOINT, { method: 'GET', credentials: 'same-origin' })
			.then(function (r) {
				if (!r.ok) {
					throw new Error('http');
				}
				return r.json();
			})
			.then(function (data) {
				if (data && typeof data.count === 'number') {
					window.podexpertSetCartCountBadge(data);
				}
			})
			.catch(function () {});
	};

	if (typeof BX !== 'undefined' && BX.ready && BX.addCustomEvent) {
		BX.ready(function () {
			BX.addCustomEvent('OnBasketChange', function () {
				window.podexpertRefreshMinicartFromServer();
			});
		});
	}
})(window);
