<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

CModule::IncludeModule('iblock');

$IBLOCK_ID = isset($IBLOCK_ID) ? (int) $IBLOCK_ID : 11;
$SECTION_ID = isset($SECTION_ID) ? (int) $SECTION_ID : 2;

$arSection = null;
$rsSection = CIBlockSection::GetList(
    [],
    ['IBLOCK_ID' => $IBLOCK_ID, 'ID' => $SECTION_ID],
    false,
    ['ID', 'NAME', 'DESCRIPTION', 'PICTURE']
);
if ($row = $rsSection->GetNext()) {
    $arSection = $row;
}

$sectionTitle = $arSection['NAME'] ?? 'Линейка продукции Регенерация';
$rawDesc = $arSection['DESCRIPTION'] ?? 'Линия создана для регенерации кожи и ногтей.';
$sectionDescription = trim(html_entity_decode(strip_tags((string) $rawDesc), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

$hasCatalog = CModule::IncludeModule('catalog');
if ($hasCatalog) {
    CModule::IncludeModule('currency');
}

$res = CIBlockElement::GetList(
    ['SORT' => 'ASC', 'ID' => 'ASC'],
    ['IBLOCK_ID' => $IBLOCK_ID, 'SECTION_ID' => $SECTION_ID, 'ACTIVE' => 'Y'],
    false,
    false,
    [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'CODE',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PAGE_URL',
    ]
);

$products = [];

while ($ob = $res->GetNextElement()) {
    $fields = $ob->GetFields();
    $props = $ob->GetProperties();

    $title = (string) $fields['NAME'];
    $preview = trim((string) $fields['PREVIEW_TEXT']);
    $detailPlain = trim(html_entity_decode(strip_tags((string) $fields['DETAIL_TEXT']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $description = $preview !== ''
        ? preg_replace('/\s+/u', ' ', trim(html_entity_decode(strip_tags($preview), ENT_QUOTES | ENT_HTML5, 'UTF-8')))
        : preg_replace('/\s+/u', ' ', $detailPlain);

    $imgId = (int) ($fields['PREVIEW_PICTURE'] ?: $fields['DETAIL_PICTURE']);
    $imageSrc = $imgId ? (string) CFile::GetPath($imgId) : '';

    $priceCurrent = '';
    $priceOld = '';

    if ($hasCatalog) {
        $optimal = CCatalogProduct::GetOptimalPrice($fields['ID']);
        if (is_array($optimal) && !empty($optimal['RESULT_PRICE']) && is_array($optimal['RESULT_PRICE'])) {
            $rp = $optimal['RESULT_PRICE'];
            $priceCurrent = (string) ($rp['PRINT_DISCOUNT_VALUE'] ?? $rp['PRINT_PRICE'] ?? '');
            if ($priceCurrent === '' && isset($rp['DISCOUNT_PRICE'], $rp['CURRENCY'])) {
                $priceCurrent = CCurrencyLang::CurrencyFormat((float) $rp['DISCOUNT_PRICE'], (string) $rp['CURRENCY']);
            }
            $base = isset($rp['BASE_PRICE']) ? (float) $rp['BASE_PRICE'] : 0.0;
            $disc = isset($rp['DISCOUNT_PRICE']) ? (float) $rp['DISCOUNT_PRICE'] : 0.0;
            if ($base > $disc && $disc > 0) {
                $priceOld = (string) ($rp['PRINT_BASE_PRICE'] ?? '');
                if ($priceOld === '' && isset($rp['CURRENCY'])) {
                    $priceOld = CCurrencyLang::CurrencyFormat($base, (string) $rp['CURRENCY']);
                }
            }
        }
    }

    $badge = '';
    foreach (['BADGE', 'LABEL', 'HIT', 'STICKER'] as $code) {
        if (!empty($props[$code]['VALUE'])) {
            $badge = is_array($props[$code]['VALUE'])
                ? (string) reset($props[$code]['VALUE'])
                : (string) $props[$code]['VALUE'];
            break;
        }
    }

    $detailUrl = trim((string) ($fields['DETAIL_PAGE_URL'] ?? ''));
    if ($detailUrl === '') {
        $catalogCfg = is_array($GLOBALS['PODEXPERT_CATALOG'] ?? null) ? $GLOBALS['PODEXPERT_CATALOG'] : [];
        $sefFolder = trim((string) ($catalogCfg['CATALOG_SEF_FOLDER'] ?? ''));
        if ($sefFolder !== '') {
            if ($sefFolder[0] !== '/') {
                $sefFolder = '/' . $sefFolder;
            }
            $sefFolder = rtrim($sefFolder, '/') . '/';
            $detailUrl = $sefFolder . '?ELEMENT_ID=' . (int) $fields['ID'];
        }
    }

    $products[] = [
        'id' => (int) $fields['ID'],
        'detailUrl' => $detailUrl,
        'title' => $title,
        'description' => $description,
        'imageSrc' => $imageSrc,
        'priceCurrent' => $priceCurrent,
        'priceOld' => $priceOld,
        'badge' => $badge,
    ];
}
?>

<section class="section products<?= $products === [] ? ' products--empty' : '' ?>" aria-label="<?= htmlspecialchars($sectionTitle) ?>">
    <div class="container">
        <div class="section-header text-center mb-8 section-header--products-2">
            <h2 class="section-header__title font-bold"><?= htmlspecialchars($sectionTitle) ?></h2>
            <p class="section-header__text"><?= htmlspecialchars($sectionDescription) ?></p>
        </div>
    </div>
    <div class="container--fullwidth">
        <?php if ($products === []): ?>
            <p class="products__empty">В этой категории пока нет товаров.</p>
        <?php else: ?>
        <div class="products__swiper">
            <div class="swiper js-products-swiper">
                <div class="swiper-wrapper products__wrapper">
                    <?php foreach ($products as $product): ?>
                        <div class="swiper-slide products__slide">
                            <article class="product-card">
                                <?php if ($product['badge'] !== ''): ?>
                                    <p class="product-card__badge bg-gray-800"><?= htmlspecialchars($product['badge']) ?></p>
                                <?php endif; ?>
                                <div class="product-card__media">
                                    <a
                                        class="product-card__detail-link product-card__detail-link--media"
                                        href="<?= htmlspecialchars($product['detailUrl'], ENT_QUOTES, 'UTF-8') ?>"
                                        <?php if ($product['imageSrc'] === ''): ?>aria-label="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                                    >
                                        <?php if ($product['imageSrc'] !== ''): ?>
                                            <img
                                                class="product-card__image"
                                                src="<?= htmlspecialchars($product['imageSrc']) ?>"
                                                alt="<?= htmlspecialchars($product['title']) ?>"
                                                loading="lazy"
                                                decoding="async"
                                            />
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="product-card__content">
                                    <h3 class="product-card__title">
                                        <a
                                            class="product-card__title-link"
                                            href="<?= htmlspecialchars($product['detailUrl'], ENT_QUOTES, 'UTF-8') ?>"
                                        ><?= htmlspecialchars($product['title']) ?></a>
                                    </h3>
                                    <?php if ($product['description'] !== ''): ?>
                                        <p class="product-card__description"><?= htmlspecialchars($product['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="product-card__footer">
                                        <p class="product-card__price">
                                            <?php if ($product['priceCurrent'] !== ''): ?>
                                                <?php
                                                // PRINT_* от каталога уже с HTML-сущностями (&nbsp;, &#8381;); htmlspecialchars ломает отображение
                                                $pc = html_entity_decode(strip_tags((string) $product['priceCurrent']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                                ?>
                                                <span class="product-card__price-current"><?= htmlspecialchars($pc, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                            <?php if ($product['priceOld'] !== ''): ?>
                                                <?php
                                                $po = html_entity_decode(strip_tags((string) $product['priceOld']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                                ?>
                                                <span class="product-card__price-old"><?= htmlspecialchars($po, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                        </p>
                                        <button
                                            type="button"
                                            class="product-card__button btn btn-outline js-product-add-basket"
                                            data-product-id="<?= (int) $product['id'] ?>"
                                            aria-label="В корзину: <?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                            В корзину
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M20 12L20.495 11.505L20.9899 12L20.495 12.495L20 12ZM5 12.7C4.6134 12.7 4.3 12.3866 4.3 12C4.3 11.6134 4.6134 11.3 5 11.3V12.7ZM14.495 5.50503L20.495 11.505L19.505 12.495L13.505 6.49497L14.495 5.50503ZM20.495 12.495L14.495 18.495L13.505 17.505L19.505 11.505L20.495 12.495ZM20 12.7H5V11.3H20V12.7Z" fill="currentColor"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="products__nav products__nav--prev" type="button" aria-label="Предыдущий товар">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                    <path d="M1 13L7 7L1 0.999999" stroke="currentColor" stroke-width="2"/>
                </svg>
            </button>

            <button class="products__nav products__nav--next" type="button" aria-label="Следующий товар">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="14" viewBox="0 0 9 14" fill="none">
                    <path d="M1 13L7 7L1 0.999999" stroke="currentColor" stroke-width="2"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($products !== [] && empty($GLOBALS['products_slider_swiper_assets_loaded'])): ?>
<?php $GLOBALS['products_slider_swiper_assets_loaded'] = true; ?>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
(function () {
    function initProductsSliders() {
        var SwiperConstructor = window.Swiper;
        if (!SwiperConstructor) {
            return;
        }
        document.querySelectorAll('.js-products-swiper').forEach(function (productsSlider) {
            if (productsSlider.dataset.initialized === 'true') {
                return;
            }
            var root = productsSlider.closest('.products__swiper');
            if (!root) {
                return;
            }
            var nextEl = root.querySelector('.products__nav--next');
            var prevEl = root.querySelector('.products__nav--prev');
            var swiper = new SwiperConstructor(productsSlider, {
                slidesPerView: 1.1,
                slidesOffsetBefore: 12,
                slidesOffsetAfter: 12,
                spaceBetween: 12,
                speed: 600,
                navigation: {
                    nextEl: nextEl,
                    prevEl: prevEl
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    992: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                        slidesOffsetBefore: 0,
                        slidesOffsetAfter: 0
                    }
                }
            });
            if (swiper) {
                productsSlider.dataset.initialized = 'true';
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductsSliders, { once: true });
    } else {
        initProductsSliders();
    }
})();
</script>
<?php endif; ?>
<?php if ($products !== [] && empty($GLOBALS['products_slider_basket_js_loaded'])): ?>
<?php $GLOBALS['products_slider_basket_js_loaded'] = true; ?>
<script>
(function () {
    var sessid = <?= json_encode(bitrix_sessid(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var endpoint = <?= json_encode('/local/ajax/podexpert_basket_add.php', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    document.body.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-product-add-basket');
        if (!btn) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var id = btn.getAttribute('data-product-id');
        if (!id) {
            return;
        }
        var fd = new FormData();
        fd.append('sessid', sessid);
        fd.append('productId', id);
        btn.disabled = true;
        fetch(endpoint, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) {
                return r.json();
            })
            .then(function () {})
            .catch(function () {})
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
</script>
<?php endif; ?>
