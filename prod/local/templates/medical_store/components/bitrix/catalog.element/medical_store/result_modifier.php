<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

/**
 * @var CBitrixComponentTemplate $this
 * @var array $arResult
 * @var array $arParams
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

if (!Loader::includeModule('iblock') || empty($arResult['ID']) || (int) $arResult['ID'] <= 0) {
    return;
}

$galleryCode = (string) ($arParams['ADD_PICT_PROP'] ?? 'GALLERY');
$galleryCode = $galleryCode !== '' && $galleryCode !== '-' ? $galleryCode : 'GALLERY';

$collectGalleryFileIds = static function (int $iblockId, int $elementId, string $code): array {
    $ids = [];
    $res = \CIBlockElement::GetProperty(
        $iblockId,
        $elementId,
        ['sort' => 'asc', 'id' => 'asc'],
        ['CODE' => $code]
    );
    while ($row = $res->Fetch()) {
        if (($row['PROPERTY_TYPE'] ?? '') !== 'F') {
            continue;
        }
        $v = $row['VALUE'] ?? null;
        if ($v === false || $v === null || $v === '') {
            continue;
        }
        if (is_array($v)) {
            foreach ($v as $id) {
                $id = (int) $id;
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        } else {
            $id = (int) $v;
            if ($id > 0) {
                $ids[] = $id;
            }
        }
    }
    return array_values(array_unique($ids, SORT_REGULAR));
};

$fileIdSetFromPhotos = static function (array $photos): array {
    $s = [];
    foreach ($photos as $p) {
        if (!empty($p['ID'])) {
            $s[(int) $p['ID']] = true;
        }
    }
    return $s;
};

$appendFilesByIds = static function (array &$photos, array $fileIds) use ($fileIdSetFromPhotos): void {
    if ($fileIds === []) {
        return;
    }
    $have = $fileIdSetFromPhotos($photos);
    foreach ($fileIds as $fid) {
        $fid = (int) $fid;
        if ($fid <= 0 || isset($have[$fid])) {
            continue;
        }
        $row = \CFile::GetFileArray($fid);
        if (!is_array($row)) {
            continue;
        }
        if ($row['SRC'] === '' || $row['SRC'] === null) {
            $path = \CFile::GetPath($fid);
            if ($path === '' || $path === null) {
                continue;
            }
            $row['SRC'] = $path;
        }
        $row['ID'] = $fid;
        $have[$fid] = true;
        $photos[] = $row;
    }
    $photos = array_values($photos);
};

$mainIblockId = (int) $arResult['IBLOCK_ID'];
$mainElementId = (int) $arResult['ID'];

if (!isset($arResult['MORE_PHOTO']) || !is_array($arResult['MORE_PHOTO'])) {
    $arResult['MORE_PHOTO'] = [];
}
$mainGalleryIds = $collectGalleryFileIds($mainIblockId, $mainElementId, $galleryCode);
$appendFilesByIds($arResult['MORE_PHOTO'], $mainGalleryIds);
$arResult['MORE_PHOTO_COUNT'] = count($arResult['MORE_PHOTO']);

if (!empty($arResult['OFFERS']) && is_array($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $k => $offer) {
        if (empty($offer['ID']) || empty($offer['IBLOCK_ID'])) {
            continue;
        }
        if (!isset($arResult['OFFERS'][$k]['MORE_PHOTO']) || !is_array($arResult['OFFERS'][$k]['MORE_PHOTO'])) {
            $arResult['OFFERS'][$k]['MORE_PHOTO'] = [];
        }
        $oGids = $collectGalleryFileIds(
            (int) $offer['IBLOCK_ID'],
            (int) $offer['ID'],
            $galleryCode
        );
        $appendFilesByIds($arResult['OFFERS'][$k]['MORE_PHOTO'], $oGids);
        $appendFilesByIds($arResult['OFFERS'][$k]['MORE_PHOTO'], $mainGalleryIds);
        $arResult['OFFERS'][$k]['MORE_PHOTO_COUNT'] = count($arResult['OFFERS'][$k]['MORE_PHOTO']);
    }
}

if (!empty($arResult['JS_OFFERS']) && is_array($arResult['JS_OFFERS']) && !empty($arResult['OFFERS'])) {
    foreach ($arResult['JS_OFFERS'] as $ind => $js) {
        if (!isset($arResult['OFFERS'][$ind]) || !is_array($arResult['OFFERS'][$ind]['MORE_PHOTO'] ?? null)) {
            continue;
        }
        $m = $arResult['OFFERS'][$ind]['MORE_PHOTO'];
        $arResult['JS_OFFERS'][$ind]['SLIDER'] = $m;
        $arResult['JS_OFFERS'][$ind]['SLIDER_COUNT'] = (int) ($arResult['OFFERS'][$ind]['MORE_PHOTO_COUNT'] ?? count($m));
    }
}
