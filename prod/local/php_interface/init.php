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

AddEventHandler('main', 'OnBeforeProlog', static function () {
    global $APPLICATION;
    if (!is_object($APPLICATION)) {
        return;
    }
    $path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    if (
        !preg_match('#^/(catalog|personal/cart|personal/order)(/|$)#', $path)
    ) {
        return;
    }
    if (method_exists($APPLICATION, 'SetTemplate')) {
        $APPLICATION->SetTemplate('medical_store');
    } elseif (method_exists($APPLICATION, 'SetTemplateID')) {
        $APPLICATION->SetTemplateID('medical_store');
    }
});
