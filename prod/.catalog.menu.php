<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

$cfg = $GLOBALS['PODEXPERT_CATALOG'] ?? [];
$iblockId = (int) ($cfg['CATALOG_IBLOCK_ID'] ?? 0);
$iblockType = (string) ($cfg['IBLOCK_TYPE'] ?? 'catalog');
$depth = (int) ($cfg['CATALOG_MENU_MAX_DEPTH'] ?? 4);
if ($depth < 1) {
    $depth = 1;
}
if ($depth > 10) {
    $depth = 10;
}
$sefFolder = (string) ($cfg['CATALOG_SEF_FOLDER'] ?? '/catalog/');
$sefFolder = '/' . trim($sefFolder, '/') . '/';

$aMenuLinks = [];

if ($iblockId > 0 && is_object($APPLICATION)) {
    $fromSections = $APPLICATION->IncludeComponent(
        'bitrix:menu.sections',
        '',
        [
            'IS_SEF' => 'Y',
            'SEF_BASE_URL' => $sefFolder,
            'SECTION_PAGE_URL' => '#SECTION_CODE_PATH#/',
            'DETAIL_PAGE_URL' => '#SECTION_CODE_PATH#/#ELEMENT_CODE#/',
            'IBLOCK_TYPE' => $iblockType,
            'IBLOCK_ID' => $iblockId,
            'DEPTH_LEVEL' => (string) $depth,
            'CACHE_TIME' => '36000000',
        ],
        false,
        ['RETURN' => true]
    );
    if (is_array($fromSections)) {
        $aMenuLinks = $fromSections;
    }
}

array_unshift($aMenuLinks, [
    'Все',
    $sefFolder,
    [],
    [
        'FROM_IBLOCK' => false,
        'IS_PARENT' => false,
        'DEPTH_LEVEL' => 1,
    ],
    '',
]);
