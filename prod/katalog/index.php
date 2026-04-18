<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Каталог');

$cfg = $GLOBALS['PODEXPERT_CATALOG'] ?? [];
$iblockId = (int) ($cfg['CATALOG_IBLOCK_ID'] ?? 0);
$iblockType = (string) ($cfg['IBLOCK_TYPE'] ?? 'catalog');
$priceCodes = $cfg['PRICE_CODE'] ?? ['BASE'];
if (!is_array($priceCodes) || $priceCodes === []) {
    $priceCodes = ['BASE'];
}

if ($iblockId <= 0) {
    ?>
    <div class="container py-8">
        <p>Укажите <strong>CATALOG_IBLOCK_ID</strong> в файле
            <code>/local/php_interface/podexpert_catalog.php</code> (ID инфоблока каталога из админки Битрикс).</p>
    </div>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
    return;
}

?>
<div class="container">
<?php
$APPLICATION->IncludeComponent(
    'bitrix:catalog',
    '.default',
    [
        'IBLOCK_TYPE' => $iblockType,
        'IBLOCK_ID' => $iblockId,
        'TEMPLATE_THEME' => 'blue',
        'HIDE_NOT_AVAILABLE' => 'Y',
        'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
        'SHOW_DEACTIVATED' => 'N',
        'USER_CONSENT' => 'N',
        'USER_CONSENT_ID' => '0',
        'USER_CONSENT_IS_CHECKED' => 'Y',
        'USER_CONSENT_IS_LOADED' => 'N',
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/katalog/',
        'SEF_URL_TEMPLATES' => [
            'sections' => '',
            'section' => '#SECTION_CODE_PATH#/',
            'element' => '#SECTION_CODE_PATH#/#ELEMENT_CODE#/',
            'compare' => 'compare/?action=#ACTION_CODE#',
            'smart_filter' => '#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/',
        ],
        'COMPATIBLE_MODE' => 'Y',
        'USE_FILTER' => 'Y',
        'FILTER_NAME' => 'arrFilter',
        'FILTER_FIELD_CODE' => [
            'NAME',
            'TAGS',
        ],
        'FILTER_PROPERTY_CODE' => [
            '-',
        ],
        'FILTER_PRICE_CODE' => $priceCodes,
        'USE_MAIN_ELEMENT_SECTION' => 'Y',
        'DETAIL_PROPERTY_CODE' => [
            '-',
        ],
        'LIST_PROPERTY_CODE' => [
            '-',
        ],
        'DETAIL_OFFERS_PROPERTY_CODE' => [
            '-',
        ],
        'LIST_META_KEYWORDS' => '-',
        'LIST_META_DESCRIPTION' => '-',
        'LIST_BROWSER_TITLE' => '-',
        'DETAIL_META_KEYWORDS' => '-',
        'DETAIL_META_DESCRIPTION' => '-',
        'DETAIL_BROWSER_TITLE' => '-',
        'DETAIL_SET_CANONICAL_URL' => 'Y',
        'SECTION_ID_VARIABLE' => 'SECTION_ID',
        'SHOW_SKU_DESCRIPTION' => 'N',
        'PRICE_CODE' => $priceCodes,
        'USE_PRICE_COUNT' => 'N',
        'SHOW_PRICE_COUNT' => '1',
        'PRICE_VAT_INCLUDE' => 'Y',
        'PRICE_VAT_SHOW_VALUE' => 'N',
        'CONVERT_CURRENCY' => 'Y',
        'CURRENCY_ID' => 'RUB',
        'BASKET_URL' => '/personal/cart/',
        'ACTION_VARIABLE' => 'action',
        'ADD_PROPERTIES_TO_BASKET' => 'Y',
        'PARTIAL_PRODUCT_PROPERTIES' => 'N',
        'ADD_TO_BASKET_ACTION' => 'ADD',
        'DISPLAY_COMPARE' => 'N',
        'USE_COMPARE' => 'N',
        'USE_COMMON_SETTINGS_BASKET_POPUP' => 'N',
        'COMMON_ADD_TO_BASKET_ACTION' => 'ADD',
        'TOP_ADD_TO_BASKET_ACTION' => 'ADD',
        'SECTION_ADD_TO_BASKET_ACTION' => 'ADD',
        'DETAIL_ADD_TO_BASKET_ACTION' => 'ADD',
        'SHOW_TOP_ELEMENTS' => 'N',
        'SECTION_COUNT_ELEMENTS' => 'Y',
        'SECTION_TOP_DEPTH' => '2',
        'PAGE_ELEMENT_COUNT' => '24',
        'LINE_ELEMENT_COUNT' => '4',
        'ELEMENT_SORT_FIELD' => 'sort',
        'ELEMENT_SORT_ORDER' => 'asc',
        'LIST_SHOW_SLIDER' => 'Y',
        'DETAIL_SHOW_POPULAR_IN_SECTION' => 'N',
        'DETAIL_SHOW_VIEWED_IN_SECTION' => 'N',
        'USE_STORE' => 'N',
        'USE_ELEMENT_COUNTER' => 'Y',
        'SET_TITLE' => 'Y',
        'SET_STATUS_404' => 'Y',
        'SHOW_404' => 'Y',
        'FILE_404' => '',
    ],
    false
);
?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
