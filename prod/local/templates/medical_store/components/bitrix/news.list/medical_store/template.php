<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
?>
<?php if (empty($arResult['ITEMS'])) { ?>
    <p class="news-empty">Новостей не найдено.</p>
<?php } else { ?>
<div class="news__grid grid gap-4 md:grid-cols-2 xl:grid-cols-3" id="news-grid">
<?php
foreach ($arResult['ITEMS'] as $arItem) {
    $this->AddEditAction(
        $arItem['ID'],
        $arItem['EDIT_LINK'],
        CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT')
    );
    $this->AddDeleteAction(
        $arItem['ID'],
        $arItem['DELETE_LINK'],
        CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE')
    );

    $title = (string) ($arItem['NAME'] ?? '');
    $url = (string) ($arItem['DETAIL_PAGE_URL'] ?? '');
    if ($url === '') {
        $url = '#';
    }
    $previewRaw = (string) ($arItem['PREVIEW_TEXT'] ?? '');
    $previewText = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $previewRaw)));
    $date = (string) ($arItem['DISPLAY_ACTIVE_FROM'] ?? '');

    $imgSrc = '';
    $imgAlt = $title;
    if (!empty($arItem['PREVIEW_PICTURE']) && is_array($arItem['PREVIEW_PICTURE'])) {
        $imgSrc = (string) ($arItem['PREVIEW_PICTURE']['SRC'] ?? '');
        if (isset($arItem['PREVIEW_PICTURE']['ALT']) && (string) $arItem['PREVIEW_PICTURE']['ALT'] !== '') {
            $imgAlt = (string) $arItem['PREVIEW_PICTURE']['ALT'];
        }
    }
    ?>
    <article class="news-card" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
        <?php if ($imgSrc !== '') { ?>
            <a class="news-card__image-link" href="<?= htmlspecialcharsbx($url) ?>" aria-label="<?= htmlspecialcharsbx($title) ?>">
                <img
                    class="news-card__image"
                    src="<?= htmlspecialcharsbx($imgSrc) ?>"
                    alt="<?= htmlspecialcharsbx($imgAlt) ?>"
                    width="500"
                    height="500"
                    loading="lazy"
                />
            </a>
        <?php } ?>
        <?php if ($date !== '') { ?>
            <p class="news-card__date"><?= htmlspecialcharsbx($date) ?></p>
        <?php } ?>
        <h2 class="news-card__title">
            <a class="news-card__link" href="<?= htmlspecialcharsbx($url) ?>"><?= htmlspecialcharsbx($title) ?></a>
        </h2>
        <?php if ($previewText !== '') { ?>
            <p class="news-card__excerpt"><?= htmlspecialcharsbx($previewText) ?></p>
        <?php } ?>
    </article>
    <?php
}
?>
</div>
<?php } ?>

<?php if (!empty($arResult['NAV_STRING'])) { ?>
<nav class="pagination" aria-label="Страницы новостей">
    <?= $arResult['NAV_STRING'] ?>
</nav>
<?php } ?>
