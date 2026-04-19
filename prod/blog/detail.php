<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */

$cfg = $GLOBALS['PODEXPERT_BLOG'] ?? [];
$iblockId = (int) ($cfg['NEWS_IBLOCK_ID'] ?? 0);
$iblockType = (string) ($cfg['NEWS_IBLOCK_TYPE'] ?? 'news');

$elementCode = trim((string) ($_REQUEST['ELEMENT_CODE'] ?? ''));
if ($elementCode === '' || strpbrk($elementCode, '/\\') !== false) {
    \CHTTP::SetStatus('404 Not Found');
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
    return;
}

if ($iblockId <= 0) {
    ?>
    <div class="container py-8">
        <p>Укажите <strong>NEWS_IBLOCK_ID</strong> в <code>/local/php_interface/podexpert_blog.php</code>.</p>
    </div>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
    return;
}

?>
<section class="section news news--detail" aria-label="Материал">
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:news.detail',
        'medical_store',
        [
            'IBLOCK_TYPE' => $iblockType,
            'IBLOCK_ID' => $iblockId,
            'ELEMENT_CODE' => $elementCode,
            'FIELD_CODE' => [
                'NAME',
                'PREVIEW_TEXT',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'DETAIL_TEXT',
                'DATE_ACTIVE_FROM',
            ],
            'PROPERTY_CODE' => [],
            'CHECK_DATES' => 'Y',
            'ACTIVE_DATE_FORMAT' => 'd.m.Y',
            'SET_TITLE' => 'Y',
            'SET_BROWSER_TITLE' => 'Y',
            'SET_META_KEYWORDS' => 'N',
            'SET_META_DESCRIPTION' => 'N',
            'SET_LAST_MODIFIED' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N',
            'ADD_ELEMENT_CHAIN' => 'Y',
            'USE_PERMISSIONS' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600',
            'CACHE_GROUPS' => 'Y',
            'DISPLAY_DATE' => 'Y',
            'DISPLAY_PICTURE' => 'Y',
            'DISPLAY_PREVIEW_TEXT' => 'Y',
            'STRICT_SECTION_CHECK' => 'N',
            'SET_STATUS_404' => 'Y',
            'SHOW_404' => 'Y',
        ],
        false
    );
    ?>
</section>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
