<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

$this->setFrameMode(true);

global $searchFilter;

$searchQuery = trim($_REQUEST['q'] ?? '');

$elementOrder = [
    'ELEMENT_SORT_FIELD'  => $arParams['ELEMENT_SORT_FIELD'],
    'ELEMENT_SORT_ORDER'  => $arParams['ELEMENT_SORT_ORDER'],
    'ELEMENT_SORT_FIELD2' => $arParams['ELEMENT_SORT_FIELD2'],
    'ELEMENT_SORT_ORDER2' => $arParams['ELEMENT_SORT_ORDER2'],
];

$notFound = false;

if (Loader::includeModule('search') && $searchQuery !== '') {
    $arElements = $APPLICATION->IncludeComponent(
        'bitrix:search.page',
        '.default',
        [
            'RESTART'            => $arParams['RESTART'],
            'NO_WORD_LOGIC'      => $arParams['NO_WORD_LOGIC'],
            'USE_LANGUAGE_GUESS' => $arParams['USE_LANGUAGE_GUESS'],
            'CHECK_DATES'        => $arParams['CHECK_DATES'],
            'arrFILTER'          => ['iblock_' . $arParams['IBLOCK_TYPE']],
            'arrFILTER_iblock_' . $arParams['IBLOCK_TYPE'] => [$arParams['IBLOCK_ID']],
            'USE_TITLE_RANK'     => $arParams['USE_TITLE_RANK'],
            'DEFAULT_SORT'       => 'rank',
            'FILTER_NAME'        => '',
            'SHOW_WHERE'         => 'N',
            'arrWHERE'           => [],
            'SHOW_WHEN'          => 'N',
            'PAGE_RESULT_COUNT'  => ($arParams['PAGE_RESULT_COUNT'] ?? 100),
            'DISPLAY_TOP_PAGER'  => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'N',
            'PAGER_TITLE'        => '',
            'PAGER_SHOW_ALWAYS'  => 'N',
            'PAGER_TEMPLATE'     => 'N',
        ],
        $component,
        ['HIDE_ICONS' => 'Y']
    );

    if (!empty($arElements) && is_array($arElements)) {
        $searchFilter = ['ID' => $arElements];
    } elseif (is_array($arElements)) {
        $notFound = true;
    }
}
?>

<div class="search-results">
    <div class="container">





        <section class="section section--t2 header-title">
            <div class="container">
                <div class="header-title__center2">
                    <h1 class="header-title__heading text-3xl md:text-4xl font-bold">
                        <?php if ($searchQuery !== ''): ?>
                            Результаты поиска: <span class="search-results__query"><?= htmlspecialchars($searchQuery) ?></span>
                        <?php else: ?>
                            Поиск по каталогу
                        <?php endif; ?>
                    </h1>
                </div>
            </div>
        </section>







<div class="section section--p0">
        <?php if ($notFound): ?>
            <p class="search-results__empty">По запросу «<?= htmlspecialchars($searchQuery) ?>» ничего не найдено. Попробуйте изменить запрос.</p>

        <?php elseif (!empty($searchFilter) && is_array($searchFilter)): ?>
            <?php
            $APPLICATION->IncludeComponent(
                'bitrix:catalog.section',
                'podoexpert_section',
                [
                    'IBLOCK_TYPE'              => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID'                => $arParams['IBLOCK_ID'],
                    'PAGE_ELEMENT_COUNT'       => $arParams['PAGE_ELEMENT_COUNT'],
                    'LINE_ELEMENT_COUNT'       => '4',
                    'PROPERTY_CODE'            => $arParams['PROPERTY_CODE'],
                    'OFFERS_FIELD_CODE'        => $arParams['OFFERS_FIELD_CODE'],
                    'OFFERS_PROPERTY_CODE'     => $arParams['OFFERS_PROPERTY_CODE'],
                    'OFFERS_SORT_FIELD'        => $arParams['OFFERS_SORT_FIELD'],
                    'OFFERS_SORT_ORDER'        => $arParams['OFFERS_SORT_ORDER'],
                    'OFFERS_SORT_FIELD2'       => $arParams['OFFERS_SORT_FIELD2'],
                    'OFFERS_SORT_ORDER2'       => $arParams['OFFERS_SORT_ORDER2'],
                    'OFFERS_LIMIT'             => $arParams['OFFERS_LIMIT'],
                    'SECTION_URL'              => $arParams['SECTION_URL'],
                    'DETAIL_URL'               => $arParams['DETAIL_URL'],
                    'BASKET_URL'               => $arParams['BASKET_URL'],
                    'ACTION_VARIABLE'          => $arParams['ACTION_VARIABLE'],
                    'PRODUCT_ID_VARIABLE'      => $arParams['PRODUCT_ID_VARIABLE'],
                    'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                    'PRODUCT_PROPS_VARIABLE'   => $arParams['PRODUCT_PROPS_VARIABLE'],
                    'SECTION_ID_VARIABLE'      => $arParams['SECTION_ID_VARIABLE'],
                    'CACHE_TYPE'               => $arParams['CACHE_TYPE'],
                    'CACHE_TIME'               => $arParams['CACHE_TIME'],
                    'DISPLAY_COMPARE'          => 'N',
                    'PRICE_CODE'               => $arParams['~PRICE_CODE'],
                    'USE_PRICE_COUNT'          => $arParams['USE_PRICE_COUNT'],
                    'SHOW_PRICE_COUNT'         => $arParams['SHOW_PRICE_COUNT'],
                    'PRICE_VAT_INCLUDE'        => $arParams['PRICE_VAT_INCLUDE'],
                    'USE_PRODUCT_QUANTITY'     => $arParams['USE_PRODUCT_QUANTITY'],
                    'HIDE_NOT_AVAILABLE'       => $arParams['HIDE_NOT_AVAILABLE'],
                    'DISPLAY_TOP_PAGER'        => 'N',
                    'DISPLAY_BOTTOM_PAGER'     => 'N',
                    'PAGER_SHOW_ALWAYS'        => 'N',
                    'FILTER_NAME'              => 'searchFilter',
                    'SECTION_ID'               => '',
                    'SECTION_CODE'             => '',
                    'INCLUDE_SUBSECTIONS'      => 'Y',
                    'SHOW_ALL_WO_SECTION'      => 'Y',
                    'SET_TITLE'                => 'N',
                    'SET_STATUS_404'           => 'N',
                    'CACHE_FILTER'             => 'N',
                    'CACHE_GROUPS'             => 'N',
                    'ADD_PICT_PROP'            => 'GALLERY',
                    'OFFER_ADD_PICT_PROP'      => 'GALLERY',
                    'LIST_PRODUCT_BLOCKS_ORDER' => 'price,props,sku,quantityLimit,quantity,buttons',
                    'LIST_PRODUCT_ROW_VARIANTS' => '[{"VARIANT":"3","BIG_DATA":false},{"VARIANT":"3","BIG_DATA":false},{"VARIANT":"3","BIG_DATA":false},{"VARIANT":"3","BIG_DATA":false}]',
                    'LIST_ENLARGE_PRODUCT'     => 'STRICT',
                    'SHOW_OLD_PRICE'           => 'Y',
                    'SHOW_DISCOUNT_PERCENT'    => 'N',
                    'MESS_BTN_BUY'             => 'Купить',
                    'MESS_BTN_ADD_TO_BASKET'   => 'В корзину',
                    'MESS_BTN_DETAIL'          => 'Подробнее',
                    'MESS_NOT_AVAILABLE'       => 'Нет в наличии',
                    'MESS_BTN_SUBSCRIBE'       => 'Подписаться',
                    'CONVERT_CURRENCY'         => 'Y',
                    'CURRENCY_ID'              => 'RUB',
                ] + $elementOrder,
                $arResult['THEME_COMPONENT'] ?? false,
                ['HIDE_ICONS' => 'Y']
            );
            ?>

        <?php elseif ($searchQuery === ''): ?>
            <p class="search-results__hint">Введите запрос в строке поиска выше.</p>
        <?php endif; ?>
</div>
    </div>
</div>
