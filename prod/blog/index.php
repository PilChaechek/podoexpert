<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */

$cfg = $GLOBALS['PODEXPERT_BLOG'] ?? [];
$iblockId = (int) ($cfg['NEWS_IBLOCK_ID'] ?? 0);
$iblockType = (string) ($cfg['NEWS_IBLOCK_TYPE'] ?? 'news');
$sectionId = (int) ($cfg['NEWS_SECTION_ID'] ?? 0);
$pageSize = (int) ($cfg['NEWS_PAGE_SIZE'] ?? 6);
if ($pageSize < 1) {
    $pageSize = 6;
}

$APPLICATION->SetTitle('Новости');

$searchQ = trim((string) ($_REQUEST['q'] ?? ''));

if ($iblockId <= 0) {
    ?>
    <div class="container py-8">
        <p>Укажите <strong>NEWS_IBLOCK_ID</strong> (и при необходимости тип ИБ и раздел) в файле
            <code>/local/php_interface/podexpert_blog.php</code> — ID инфоблока из админки Битрикс.</p>
    </div>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
    return;
}

global $arrBlogFilter;
$arrBlogFilter = [];
if ($searchQ !== '') {
    $arrBlogFilter = [
        [
            'LOGIC' => 'OR',
            ['%NAME' => $searchQ],
            ['%PREVIEW_TEXT' => $searchQ],
        ],
    ];
}

?>
<section class="section header-title">
    <div class="container">
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "medical_store",
            [
                "START_FROM" => "0",
                "SITE_ID" => "s2",
                "COMPONENT_TEMPLATE" => "medical_store",
                "PATH" => ""
            ],
            false
            );
        ?>

        <div class="header-title__center">
            <h1 class="header-title__heading text-3xl md:text-4xl font-bold">База знаний</h1>
            <p class="header-title__lead">
                Проверенные источники знаний и разные точки зрения от экспертов по подологии.
            </p>
            <form class="header-title__search" role="search" action="/blog/" method="get">
                <div class="input-icon header-title__search-field">
                    <span class="input-icon__icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.2-3.2" stroke-linecap="round" />
                        </svg>
                    </span>
                    <label class="sr-only" for="blog-search-q">Поиск по базе</label>
                    <input
                        id="blog-search-q"
                        class="input input--search-header input-icon__input"
                        type="search"
                        name="q"
                        value="<?= htmlspecialcharsbx($searchQ) ?>"
                        placeholder="Поиск..."
                        autocomplete="off"
                    />
                </div>
            </form>
        </div>
    </div>
</section>

<section class="section news" aria-label="Список новостей">
    <div class="container">
        <?php
        $newsListParams = [
            'IBLOCK_TYPE' => $iblockType,
            'IBLOCK_ID' => $iblockId,
            'NEWS_COUNT' => $pageSize,
            'SORT_BY1' => 'ACTIVE_FROM',
            'SORT_ORDER1' => 'DESC',
            'SORT_BY2' => 'SORT',
            'SORT_ORDER2' => 'ASC',
            'FILTER_NAME' => 'arrBlogFilter',
            'FIELD_CODE' => [
                'NAME',
                'PREVIEW_TEXT',
                'PREVIEW_PICTURE',
                'DATE_ACTIVE_FROM',
            ],
            'PROPERTY_CODE' => [],
            'CHECK_DATES' => 'Y',
            'LIST_ACTIVE_DATE_FORMAT' => 'd.m.Y',
            'SET_TITLE' => 'N',
            'SET_BROWSER_TITLE' => 'N',
            'SET_META_KEYWORDS' => 'N',
            'SET_META_DESCRIPTION' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600',
            'CACHE_FILTER' => 'Y',
            'DISPLAY_TOP_PAGER' => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'Y',
            'PAGER_SHOW_ALWAYS' => 'N',
            'PAGER_TEMPLATE' => '',
            'DISPLAY_DATE' => 'Y',
            'DISPLAY_PICTURE' => 'Y',
            'DISPLAY_PREVIEW_TEXT' => 'Y',
            'USE_PERMISSIONS' => 'N',
            'INCLUDE_SUBSECTIONS' => 'Y',
            'STRICT_SECTION_CHECK' => 'N',
        ];
        if ($sectionId > 0) {
            $newsListParams['SECTION_ID'] = $sectionId;
        }

        $APPLICATION->IncludeComponent(
            'bitrix:news.list',
            'medical_store',
            $newsListParams,
            false
        );
        ?>
    </div>
</section>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
