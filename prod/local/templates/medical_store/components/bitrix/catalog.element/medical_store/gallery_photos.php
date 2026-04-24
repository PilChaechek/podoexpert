<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @return array<int, array> списки CFile (ID, SRC, WIDTH, HEIGHT)
 */
function medical_store_podexpert_gallery_photos(array $arResult, array $arParams): array
{
    if (empty($arResult['ID']) || (int) $arResult['ID'] <= 0) {
        return [];
    }
    if (!\Bitrix\Main\Loader::includeModule('iblock')) {
        return [];
    }

    $galleryCode = (string) ($arParams['ADD_PICT_PROP'] ?? 'GALLERY');
    if ($galleryCode === '' || $galleryCode === '-') {
        $galleryCode = 'GALLERY';
    }

    $ids = [];
    $res = \CIBlockElement::GetProperty(
        (int) $arResult['IBLOCK_ID'],
        (int) $arResult['ID'],
        ['sort' => 'asc', 'id' => 'asc'],
        ['CODE' => $galleryCode]
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
    $ids = array_values(array_unique($ids, SORT_REGULAR));

    $photos = [];
    foreach ($ids as $fid) {
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
        $row['ID'] = (int) $fid;
        $photos[] = $row;
    }
    if ($photos !== []) {
        return $photos;
    }
    if (!empty($arResult['MORE_PHOTO']) && is_array($arResult['MORE_PHOTO'])) {
        return $arResult['MORE_PHOTO'];
    }
    if (!empty($arResult['DETAIL_PICTURE']['SRC'])) {
        $dp = $arResult['DETAIL_PICTURE'];
        return [[
            'ID' => (int) ($dp['ID'] ?? 0),
            'SRC' => (string) $dp['SRC'],
            'WIDTH' => (int) ($dp['WIDTH'] ?? 0),
            'HEIGHT' => (int) ($dp['HEIGHT'] ?? 0),
        ]];
    }
    if (!empty($arResult['PREVIEW_PICTURE']['SRC'])) {
        $pp = $arResult['PREVIEW_PICTURE'];
        return [[
            'ID' => (int) ($pp['ID'] ?? 0),
            'SRC' => (string) $pp['SRC'],
            'WIDTH' => (int) ($pp['WIDTH'] ?? 0),
            'HEIGHT' => (int) ($pp['HEIGHT'] ?? 0),
        ]];
    }
    return [];
}
