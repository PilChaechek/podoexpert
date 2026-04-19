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

if (!isset($arResult['ID'])) {
    return;
}

$this->AddEditAction(
    $arResult['ID'],
    $arResult['EDIT_LINK'],
    CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT')
);
$this->AddDeleteAction(
    $arResult['ID'],
    $arResult['DELETE_LINK'],
    CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_DELETE')
);

$name = (string) ($arResult['NAME'] ?? '');
$date = (string) ($arResult['DISPLAY_ACTIVE_FROM'] ?? '');
$previewText = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", (string) ($arResult['PREVIEW_TEXT'] ?? ''))));
$detailHtml = (string) ($arResult['DETAIL_TEXT'] ?? '');

$imgSrc = '';
$imgAlt = $name;
$picture = $arResult['DETAIL_PICTURE'] ?? $arResult['PREVIEW_PICTURE'] ?? null;
if (is_array($picture) && !empty($picture['SRC'])) {
    $imgSrc = (string) $picture['SRC'];
    if (!empty($picture['ALT'])) {
        $imgAlt = (string) $picture['ALT'];
    }
}
?>
<article class="news-detail" id="<?= $this->GetEditAreaId($arResult['ID']) ?>">
    <?php if ($imgSrc !== '') { ?>
        <div class="news-detail__media">
            <img
                class="news-detail__image"
                src="<?= htmlspecialcharsbx($imgSrc) ?>"
                alt="<?= htmlspecialcharsbx($imgAlt) ?>"
                width="1200"
                height="675"
                loading="eager"
            />
        </div>
    <?php } ?>

    <?php if ($date !== '') { ?>
        <p class="news-detail__date"><?= htmlspecialcharsbx($date) ?></p>
    <?php } ?>

    <h1 class="news-detail__title"><?= htmlspecialcharsbx($name) ?></h1>

    <?php if ($previewText !== '' && $detailHtml === '') { ?>
        <p class="news-detail__lead"><?= htmlspecialcharsbx($previewText) ?></p>
    <?php } ?>

    <?php if ($detailHtml !== '') { ?>
        <div class="news-detail__body content-editor">
            <?= $detailHtml ?>
        </div>
    <?php } elseif ($previewText !== '') { ?>
        <p class="news-detail__body"><?= nl2br(htmlspecialcharsbx($previewText)) ?></p>
    <?php } ?>
</article>
