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
	"bitrix:catalog",
	"medical_store",
	[
		"IBLOCK_TYPE" => $iblockType,
		"IBLOCK_ID" => $iblockId,
		"TEMPLATE_THEME" => "blue",
		"HIDE_NOT_AVAILABLE" => "Y",
		"HIDE_NOT_AVAILABLE_OFFERS" => "Y",
		"SHOW_DEACTIVATED" => "N",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/catalog/",
		"COMPATIBLE_MODE" => "Y",
		"USE_FILTER" => "Y",
		"FILTER_NAME" => "arrFilter",
		"FILTER_FIELD_CODE" => [
			0 => "NAME",
			1 => "TAGS",
			2 => "",
		],
		"FILTER_PROPERTY_CODE" => [
			0 => "",
			1 => "-",
			2 => "",
		],
		"FILTER_PRICE_CODE" => [
		],
		"USE_MAIN_ELEMENT_SECTION" => "Y",
		"DETAIL_PROPERTY_CODE" => [
			0 => "-",
		],
		"LIST_PROPERTY_CODE" => [
			0 => "-",
		],
		"DETAIL_OFFERS_PROPERTY_CODE" => [
			0 => "-",
		],
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "Y",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SHOW_SKU_DESCRIPTION" => "N",
		"PRICE_CODE" => [
			0 => "base",
		],
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"BASKET_URL" => "/cart/",
		"ACTION_VARIABLE" => "action",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"DISPLAY_COMPARE" => "N",
		"USE_COMPARE" => "N",
		"USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
		"COMMON_ADD_TO_BASKET_ACTION" => "ADD",
		"TOP_ADD_TO_BASKET_ACTION" => "ADD",
		"SECTION_ADD_TO_BASKET_ACTION" => "ADD",
		"DETAIL_ADD_TO_BASKET_ACTION" => [
			0 => "ADD",
		],
		"SHOW_TOP_ELEMENTS" => "N",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "2",
		"PAGE_ELEMENT_COUNT" => "24",
		"LINE_ELEMENT_COUNT" => "4",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"LIST_SHOW_SLIDER" => "Y",
		"DETAIL_SHOW_POPULAR_IN_SECTION" => "N",
		"DETAIL_SHOW_VIEWED_IN_SECTION" => "N",
		"USE_STORE" => "N",
		"USE_ELEMENT_COUNTER" => "Y",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"FILE_404" => "",
		"COMPONENT_TEMPLATE" => "medical_store",
		"COMMON_SHOW_CLOSE_POPUP" => "N",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"SIDEBAR_SECTION_SHOW" => "Y",
		"SIDEBAR_DETAIL_SHOW" => "N",
		"SIDEBAR_PATH" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"DETAIL_STRICT_SECTION_CHECK" => "N",
		"SET_LAST_MODIFIED" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "N",
		"USE_SALE_BESTSELLERS" => "Y",
		"FILTER_VIEW_MODE" => "VERTICAL",
		"FILTER_HIDE_ON_MOBILE" => "N",
		"INSTANT_RELOAD" => "N",
		"USE_REVIEW" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"DETAIL_ADD_TO_BASKET_ACTION_PRIMARY" => [
		],
		"SEARCH_PAGE_RESULT_COUNT" => "50",
		"SEARCH_RESTART" => "N",
		"SEARCH_NO_WORD_LOGIC" => "Y",
		"SEARCH_USE_LANGUAGE_GUESS" => "Y",
		"SEARCH_CHECK_DATES" => "Y",
		"SEARCH_USE_SEARCH_RESULT_ORDER" => "N",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DETAIL_USE_COMMENTS" => "N",
		"DETAIL_BRAND_USE" => "N",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_IMAGE_RESOLUTION" => "16by9",
		"DETAIL_PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
		"DETAIL_PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
		"DETAIL_SHOW_SLIDER" => "N",
		"DETAIL_DETAIL_PICTURE_MODE" => [
			0 => "POPUP",
			1 => "MAGNIFIER",
		],
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"MESS_PRICE_RANGES_TITLE" => "Цены",
		"MESS_DESCRIPTION_TAB" => "Описание",
		"MESS_PROPERTIES_TAB" => "Характеристики",
		"MESS_COMMENTS_TAB" => "Комментарии",
		"DETAIL_SHOW_POPULAR" => "Y",
		"DETAIL_SHOW_VIEWED" => "Y",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_GIFTS_DETAIL" => "Y",
		"USE_GIFTS_SECTION" => "Y",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "Y",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"LAZY_LOAD" => "N",
		"MESS_BTN_LAZY_LOAD" => "Показать ещё",
		"LOAD_ON_SCROLL" => "N",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"SEF_URL_TEMPLATES" => [
			"sections" => "",
			"section" => "#SECTION_CODE_PATH#/",
			"element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
			"compare" => "compare/?action=#ACTION_CODE#",
			"smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
		],
		"VARIABLE_ALIASES" => [
			"compare" => [
				"ACTION_CODE" => "action",
			],
		]
	],
	false
);
?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
