<?php

require_once __DIR__ . '/classes/CustomTableProperty.php';

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', ['CustomTableProperty', 'GetUserTypeDescription']);

$podexpertCatalogFile = __DIR__ . '/podexpert_catalog.php';
if (is_file($podexpertCatalogFile)) {
    $GLOBALS['PODEXPERT_CATALOG'] = include $podexpertCatalogFile;
    if (!is_array($GLOBALS['PODEXPERT_CATALOG'])) {
        $GLOBALS['PODEXPERT_CATALOG'] = [];
    }
} else {
    $GLOBALS['PODEXPERT_CATALOG'] = [];
}

$podexpertBlogFile = __DIR__ . '/podexpert_blog.php';
if (is_file($podexpertBlogFile)) {
    $GLOBALS['PODEXPERT_BLOG'] = include $podexpertBlogFile;
    if (!is_array($GLOBALS['PODEXPERT_BLOG'])) {
        $GLOBALS['PODEXPERT_BLOG'] = [];
    }
} else {
    $GLOBALS['PODEXPERT_BLOG'] = [];
}

AddEventHandler('main', 'OnBeforeProlog', static function () {
    global $APPLICATION;
    if (!is_object($APPLICATION)) {
        return;
    }
    $path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    if (
        !preg_match('#^/(catalog|cart|personal/order|blog)(/|$)#', $path)
    ) {
        return;
    }
    if (method_exists($APPLICATION, 'SetTemplate')) {
        $APPLICATION->SetTemplate('medical_store');
    } elseif (method_exists($APPLICATION, 'SetTemplateID')) {
        $APPLICATION->SetTemplateID('medical_store');
    }
});

if (!function_exists('podexpert_basket_total_quantity')) {
    /**
     * Суммарное количество единиц товара в корзине текущего посетителя (без отложенных).
     */
    function podexpert_basket_total_quantity(): int
    {
        if (!\Bitrix\Main\Loader::includeModule('sale')) {
            return 0;
        }
        $siteId = defined('SITE_ID') ? (string) SITE_ID : '';
        if ($siteId === '') {
            $siteId = (string) \Bitrix\Main\Context::getCurrent()->getSite();
        }
        try {
            $fuserId = (int) \Bitrix\Sale\Fuser::getId();
            $basket = \Bitrix\Sale\Basket::loadItemsForFUser($fuserId, $siteId);
        } catch (\Throwable $e) {
            return 0;
        }
        $total = 0;
        foreach ($basket as $item) {
            if ($item->isDelay()) {
                continue;
            }
            $total += (int) round((float) $item->getQuantity());
        }

        return max(0, $total);
    }
}

if (!function_exists('podexpert_basket_count_badge_text')) {
    function podexpert_basket_count_badge_text(): string
    {
        $n = podexpert_basket_total_quantity();

        return $n > 99 ? '99+' : (string) $n;
    }
}
