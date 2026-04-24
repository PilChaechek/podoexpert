(function (window) {
	'use strict';

	function escAttr(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

	function escHtml(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function baseOpts(overrides) {
		var o = {
			duration: 4000,
			gravity: 'top',
			position: 'right',
			stopOnFocus: true,
			close: true
		};
		if (overrides) {
			for (var k in overrides) {
				if (Object.prototype.hasOwnProperty.call(overrides, k)) {
					o[k] = overrides[k];
				}
			}
		}
		return o;
	}

	window.podexpertToastifySimple = function (message, variant) {
		if (typeof Toastify === 'undefined') {
			return;
		}
		variant = variant || 'success';
		Toastify(baseOpts({
			text: String(message == null ? '' : message),
			className: 'podexpert-toastify podexpert-toastify--simple podexpert-toastify--' + variant,
			duration: variant === 'error' ? 6500 : 4000
		})).showToast();
	};

	window.podexpertToastifyCart = function (payload) {
		if (typeof Toastify === 'undefined' || !payload) {
			return;
		}
		var html = ''
			+ '<div class="podexpert-cart-toast">'
			+ '<div class="podexpert-cart-toast__img"><img src="' + escAttr(payload.imageSrc) + '" width="56" height="56" alt="" loading="lazy" decoding="async"></div>'
			+ '<div class="podexpert-cart-toast__body">'
			+ '<div class="podexpert-cart-toast__title">' + escHtml(payload.title) + '</div>'
			+ '<div class="podexpert-cart-toast__name">' + escHtml(payload.productName) + '</div>'
			+ '<a class="podexpert-cart-toast__link" href="' + escAttr(payload.basketUrl) + '">' + escHtml(payload.cartLinkText) + '</a>'
			+ '</div>'
			+ '</div>';

		Toastify(baseOpts({
			text: html,
			escapeMarkup: false,
			duration: 8000,
			className: 'podexpert-toastify podexpert-toastify--cart'
		})).showToast();
	};
})(window);
