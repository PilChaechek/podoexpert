<?php
/** @global CMain $APPLICATION */
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle('Результаты поиска');
?>

<?php $APPLICATION->IncludeComponent(
    'bitrix:catalog.search',
    'search_results',
    [
        'IBLOCK_TYPE'              => 'catalog',
        'IBLOCK_ID'                => '11',
        'ELEMENT_SORT_FIELD'       => 'sort',
        'ELEMENT_SORT_ORDER'       => 'asc',
        'ELEMENT_SORT_FIELD2'      => 'id',
        'ELEMENT_SORT_ORDER2'      => 'desc',
        'SECTION_URL'              => '',
        'DETAIL_URL'               => '',
        'BASKET_URL'               => '/cart/',
        'ACTION_VARIABLE'          => 'action',
        'PRODUCT_ID_VARIABLE'      => 'id',
        'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
        'PRODUCT_PROPS_VARIABLE'   => 'prop',
        'SECTION_ID_VARIABLE'      => 'SECTION_ID',
        'PAGE_ELEMENT_COUNT'       => '24',
        'LINE_ELEMENT_COUNT'       => '4',
        'PROPERTY_CODE'            => ['GALLERY', ''],
        'OFFERS_FIELD_CODE'        => '',
        'OFFERS_PROPERTY_CODE'     => '',
        'OFFERS_SORT_FIELD'        => 'sort',
        'OFFERS_SORT_ORDER'        => 'asc',
        'OFFERS_SORT_FIELD2'       => 'id',
        'OFFERS_SORT_ORDER2'       => 'desc',
        'OFFERS_LIMIT'             => '5',
        'PRICE_CODE'               => ['base'],
        'USE_PRICE_COUNT'          => 'N',
        'SHOW_PRICE_COUNT'         => '1',
        'PRICE_VAT_INCLUDE'        => 'Y',
        'USE_PRODUCT_QUANTITY'     => 'Y',
        'CACHE_TYPE'               => 'A',
        'CACHE_TIME'               => '36000000',
        'RESTART'                  => 'Y',
        'NO_WORD_LOGIC'            => 'Y',
        'USE_LANGUAGE_GUESS'       => 'Y',
        'CHECK_DATES'              => 'Y',
        'HIDE_NOT_AVAILABLE'       => 'N',
        'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
        'CONVERT_CURRENCY'         => 'Y',
        'CURRENCY_ID'              => 'RUB',
        'USE_TITLE_RANK'           => 'N',
        'USE_SEARCH_RESULT_ORDER'  => 'N',
        'DISPLAY_COMPARE'          => 'N',
        'OFFERS_CART_PROPERTIES'   => '',
        'PRODUCT_PROPERTIES'       => [],
    ],
    false
); ?>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
