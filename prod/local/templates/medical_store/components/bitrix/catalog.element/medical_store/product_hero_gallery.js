(function () {
	'use strict';

	var CSS_SWIPER = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
	var JS_SWIPER = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
	var CSS_GLIGHTBOX = 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css';
	var JS_GLIGHTBOX = 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js';

	var assetsPromise = null;

	function ensureLink(href) {
		if (document.querySelector('link[href="' + href + '"]')) {
			return;
		}
		var l = document.createElement('link');
		l.rel = 'stylesheet';
		l.href = href;
		document.head.appendChild(l);
	}

	function loadScript(src) {
		return new Promise(function (resolve, reject) {
			var s = document.createElement('script');
			s.src = src;
			s.async = true;
			s.onload = function () { resolve(); };
			s.onerror = function () { reject(new Error('load ' + src)); };
			document.head.appendChild(s);
		});
	}

	function podexpertHeroDebugActive() {
		return window.__PODEXPERT_CATALOG_HERO_DEBUG
			|| (typeof document !== 'undefined' && document.getElementById('podexpert-hero-debug'));
	}

	function applyPodexpertHeroDebug(payload) {
		// Снимок для ручной проверки: window.__PODEXPERT_HERO_DEBUG
		window.__PODEXPERT_HERO_DEBUG = payload;
		if (!podexpertHeroDebugActive()) {
			return;
		}
		if (typeof console !== 'undefined' && console.info) {
			console.info('[podexpert-hero]', payload);
		}
		var pre = document.getElementById('podexpert-hero-debug');
		if (pre) {
			try {
				pre.textContent = JSON.stringify(payload, null, 2);
			} catch (e2) {
				pre.textContent = String(payload);
			}
		}
	}

	function loadAssets() {
		if (assetsPromise) {
			return assetsPromise;
		}
		ensureLink(CSS_SWIPER);
		ensureLink(CSS_GLIGHTBOX);
		assetsPromise = new Promise(function (resolve, reject) {
			var p = Promise.resolve();
			if (typeof window.Swiper === 'undefined') {
				p = p.then(function () { return loadScript(JS_SWIPER); });
			}
			if (typeof window.GLightbox === 'undefined') {
				p = p.then(function () { return loadScript(JS_GLIGHTBOX); });
			}
			p.then(function () {
				if (window.Swiper && typeof window.GLightbox === 'function') {
					resolve();
				} else {
					reject(new Error('podexpert: Swiper/GLightbox missing'));
				}
			}).catch(reject);
		});
		return assetsPromise;
	}

	/**
	 * @param {HTMLElement} imageContainer
	 * @param {object} [catalog]
	 */
	function runRefresh(imageContainer, catalog) {
		var Swiper = window.Swiper;
		if (!Swiper) {
			applyPodexpertHeroDebug({
				ok: false,
				step: 'runRefresh',
				reason: 'window.Swiper отсутствует (CDN Swiper не загрузился?)',
				glightboxConstructor: typeof window.GLightbox,
				podexpertCatalogElement: !!window.podexpertCatalogElement
			});
			return;
		}

		if (window.__podexpertHeroGlightbox) {
			try {
				if (typeof window.__podexpertHeroGlightbox.destroy === 'function') {
					window.__podexpertHeroGlightbox.destroy();
				}
			} catch (e) {}
			window.__podexpertHeroGlightbox = null;
		}
		if (catalog) {
			if (catalog.productHeroMainSwiper) {
				try { catalog.productHeroMainSwiper.destroy(true, true); } catch (e) {}
				catalog.productHeroMainSwiper = null;
			}
			if (catalog.productHeroThumbsSwiper) {
				try { catalog.productHeroThumbsSwiper.destroy(true, true); } catch (e) {}
				catalog.productHeroThumbsSwiper = null;
			}
			if (catalog.productHeroGlightbox) {
				catalog.productHeroGlightbox = null;
			}
		}

		if (!imageContainer) {
			applyPodexpertHeroDebug({ ok: false, step: 'runRefresh', reason: 'imageContainer пустой' });
			return;
		}

		var mainEl = imageContainer.querySelector('.js-product-hero-main');
		if (!mainEl) {
			applyPodexpertHeroDebug({ ok: false, step: 'runRefresh', reason: 'нет .js-product-hero-main внутри images-container' });
			return;
		}

		var bigSlider = imageContainer.closest('[data-product-hero-root]');
		var thumbsEl = null;
		if (bigSlider) {
			var allThumbs = bigSlider.querySelectorAll('.js-product-hero-thumbs');
			for (var b = 0; b < allThumbs.length; b++) {
				var wrap = allThumbs[b].parentElement;
				if (wrap && window.getComputedStyle(wrap).display !== 'none') {
					thumbsEl = allThumbs[b];
					break;
				}
			}
			if (!thumbsEl && allThumbs.length) {
				thumbsEl = allThumbs[0];
			}
		}

		var glJson = document.getElementById('product-hero-glightbox-elements');
		var elements = [];
		var glJsonParseError = null;
		var glJsonArrayLen = 0;
		var elementsFromDom = false;
		if (glJson && glJson.textContent) {
			try {
				elements = JSON.parse(glJson.textContent);
				if (!Array.isArray(elements)) {
					elements = [];
				}
				glJsonArrayLen = elements.length;
			} catch (err) {
				glJsonParseError = err && err.message ? String(err.message) : 'parse error';
				elements = [];
			}
		}
		// Подстраховка, если JSON пуст (исторически затиралось из drawImages при пустом SLIDER — исправлено в script.js).
		if (!elements.length) {
			var domSlides = mainEl.querySelectorAll('.swiper-slide');
			for (var si = 0; si < domSlides.length; si++) {
				var domImg = domSlides[si].querySelector('img');
				if (domImg && domImg.getAttribute('src')) {
					elements.push({
						href: domImg.getAttribute('src'),
						type: 'image',
						alt: domImg.getAttribute('alt') || '',
						zoomable: false,
						draggable: true
					});
					elementsFromDom = true;
				}
			}
		}
		if (typeof window.GLightbox === 'function' && elements.length > 0) {
			var glInst = window.GLightbox({
				elements: elements,
				loop: true,
				touchNavigation: true,
				keyboardNavigation: true,
				openEffect: 'fade',
				closeEffect: 'fade',
				zoomable: false
			});
			window.__podexpertHeroGlightbox = glInst;
			if (catalog) {
				catalog.productHeroGlightbox = glInst;
			}
		}

		var mainSlideN = mainEl.querySelectorAll('.swiper-slide').length;
		var thumbSlideN = thumbsEl ? thumbsEl.querySelectorAll('.swiper-slide').length : 0;
		var thumbInstance = null;
		var mainSwiper = null;
		if (thumbsEl && thumbSlideN > 1) {
			thumbInstance = new Swiper(thumbsEl, {
				spaceBetween: 5,
				slidesPerView: 3,
				freeMode: true,
				watchSlidesProgress: true,
				breakpoints: {
					0: { slidesPerView: 3, spaceBetween: 5 },
					480: { slidesPerView: 3, spaceBetween: 6 }
				}
			});
			if (catalog) { catalog.productHeroThumbsSwiper = thumbInstance; }
		}

		if (mainSlideN > 0) {
			mainSwiper = new Swiper(mainEl, {
				effect: 'fade',
				fadeEffect: { crossFade: true },
				speed: 450,
				spaceBetween: 0,
				allowTouchMove: true,
				preventClicks: false,
				preventClicksPropagation: false,
				threshold: 20,
				thumbs: thumbInstance ? { swiper: thumbInstance } : undefined,
				on: {
					slideChange: function () {
						if (!catalog) { return; }
						var slide = this.slides[this.activeIndex];
						var node = slide && slide.querySelector('[data-entity="image"]');
						if (!node) { return; }
						var id = String(node.getAttribute('data-id'));
						var list = (catalog.offers && catalog.offers.length)
							? (catalog.offers[catalog.offerNum] && catalog.offers[catalog.offerNum].SLIDER)
							: (catalog.params && catalog.params.PRODUCT && catalog.params.PRODUCT.SLIDER);
						if (!list) { return; }
						for (var i = 0; i < list.length; i++) {
							if (String(list[i].ID) === id) {
								if (String(catalog.currentImg && catalog.currentImg.id) !== id) {
									catalog.setCurrentImg(list[i], true, false);
								}
								return;
							}
						}
					},
					click: function (swiper, evt) {
						var gInst = (catalog && catalog.productHeroGlightbox) || window.__podexpertHeroGlightbox;
						if (!gInst) { return; }
						var s = swiper || this;
						var idx = typeof s.activeIndex === 'number' ? s.activeIndex : 0;
						if (evt && typeof evt.stopPropagation === 'function') {
							evt.stopPropagation();
						}
						if (typeof gInst.openAt === 'function') {
							gInst.openAt(idx);
						} else if (typeof gInst.open === 'function') {
							gInst.open();
						}
					}
				}
			});
			if (catalog) { catalog.productHeroMainSwiper = mainSwiper; }
		}

		var gRef = window.__podexpertHeroGlightbox;
		applyPodexpertHeroDebug({
			ok: true,
			step: 'runRefresh',
			ts: new Date().toISOString(),
			glightbox: {
				glLightboxType: typeof window.GLightbox,
				instance: !!gRef,
				elementsInScriptTag: glJsonArrayLen,
				elementsAfterDomFallback: elements.length,
				elementsFromDom: elementsFromDom,
				glJsonOk: !glJsonParseError,
				parseError: glJsonParseError || null
			},
			swiper: {
				mainSlides: mainSlideN,
				thumbSlides: thumbSlideN,
				thumbSwiper: !!thumbInstance,
				mainSwiper: !!mainSwiper
			},
			catalog: !!catalog
		});

		mainEl.setAttribute('data-initialized', 'true');
	}

	window.podexpertProductHeroRefresh = function (imageContainer, catalog) {
		loadAssets().then(
			function () {
				runRefresh(imageContainer, catalog);
			},
			function (err) {
				applyPodexpertHeroDebug({
					ok: false,
					step: 'loadAssets',
					reason: err && err.message ? String(err.message) : 'loadAssets отклонён (CDN Swiper/GLightbox)',
					glightboxConstructor: typeof window.GLightbox,
					swiperPresent: typeof window.Swiper
				});
			}
		);
	};

	function scheduleInit() {
		var c = document.querySelector('[data-entity="images-container"]');
		var m = c && c.querySelector('.js-product-hero-main');
		if (m && c && m.getAttribute('data-initialized') !== 'true') {
			// Без JCCatalogElement — всё равно поднять Swiper/GLightbox; catalog опционален.
			window.podexpertProductHeroRefresh(c, window.podexpertCatalogElement || null);
		}
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', scheduleInit, { once: true });
	} else {
		scheduleInit();
	}
}());
