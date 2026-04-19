<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
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
$picture = null;
if (!empty($arResult['PREVIEW_PICTURE']) && is_array($arResult['PREVIEW_PICTURE'])) {
    $picture = $arResult['PREVIEW_PICTURE'];
} elseif (!empty($arResult['DETAIL_PICTURE']) && is_array($arResult['DETAIL_PICTURE'])) {
    $picture = $arResult['DETAIL_PICTURE'];
}
if ($picture !== null) {
    if (!empty($picture['ALT'])) {
        $imgAlt = (string) $picture['ALT'];
    }
    if (!empty($picture['ID'])) {
        $resized = CFile::ResizeImageGet(
            (int) $picture['ID'],
            ['width' => 1400, 'height' => 1400],
            BX_RESIZE_IMAGE_EXACT,
            true
        );
        if (!empty($resized['src'])) {
            $imgSrc = (string) $resized['src'];
        }
    }
    if ($imgSrc === '') {
        $imgSrc = (string) ($picture['SRC'] ?? '');
    }
}

global $APPLICATION;
?>

<div class="container">
    <article class="news-detail mx-auto w-full max-w-4xl" id="<?= $this->GetEditAreaId($arResult['ID']) ?>">
        <header class="news-detail__head mb-6 md:mb-8">
            <div class="mb-2">
                <?php
                $APPLICATION->IncludeComponent(
                        'bitrix:breadcrumb',
                        'medical_store',
                        [
                                'START_FROM' => '0',
                                'SITE_ID' => SITE_ID,
                        ],
                        false
                );
                ?>
            </div>
            <h1 class="news-detail__title text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl text-balance">
                <?= htmlspecialcharsbx($name) ?>
            </h1>
            <?php if ($date !== '') { ?>
                <p class="news-detail__date mt-3 text-sm text-neutral-400"><?= htmlspecialcharsbx($date) ?></p>
            <?php } ?>
        </header>

        <?php if ($imgSrc !== '') { ?>
            <div class="news-detail__media mb-8 overflow-hidden rounded-xl md:mb-10">
                <img
                    class="news-detail__image aspect-square w-full object-cover"
                    src="<?= htmlspecialcharsbx($imgSrc) ?>"
                    alt="<?= htmlspecialcharsbx($imgAlt) ?>"
                    width="1400"
                    height="1400"
                    loading="eager"
                />
            </div>
        <?php } ?>

        <?php if ($previewText !== '' && $detailHtml === '') { ?>
            <p class="news-detail__lead text-base leading-relaxed text-neutral-700"><?= htmlspecialcharsbx($previewText) ?></p>
        <?php } ?>

        <?php if ($detailHtml !== '') { ?>
            <div class="news-detail__body content-editor text-base leading-relaxed text-neutral-700">
                <?= $detailHtml ?>
            </div>
        <?php } elseif ($previewText !== '') { ?>
            <p class="news-detail__body text-base leading-relaxed text-neutral-700"><?= nl2br(htmlspecialcharsbx($previewText)) ?></p>
        <?php } ?>
    </article>
</div>
