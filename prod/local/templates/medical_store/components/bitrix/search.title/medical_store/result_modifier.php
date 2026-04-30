<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!CModule::IncludeModule('iblock')) {
    return;
}

// Collect element IDs from iblock catalog items
$elementIds = [];
foreach ($arResult['CATEGORIES'] as $categoryId => $arCategory) {
    if ($categoryId === 'all') {
        continue;
    }
    foreach ($arCategory['ITEMS'] as $arItem) {
        if (!isset($arItem['ITEM_ID']) || mb_substr($arItem['ITEM_ID'], 0, 1) === 'S') {
            continue;
        }
        $elementIds[(int)$arItem['ITEM_ID']] = (int)$arItem['PARAM2'];
    }
}

$imageMap = [];
if (!empty($elementIds)) {
    $ids = array_keys($elementIds);

    $rsElements = CIBlockElement::GetList(
        [],
        ['=ID' => $ids],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_GALLERY']
    );

    while ($arEl = $rsElements->GetNext()) {
        $fileId = null;

        // GALLERY — type File, can be single or multiple value
        $gallery = $arEl['PROPERTY_GALLERY_VALUE'] ?? null;
        if (is_array($gallery)) {
            $fileId = reset($gallery); // first element
        } elseif ($gallery) {
            $fileId = $gallery;
        }

        if ($fileId) {
            $arFile = CFile::GetFileArray($fileId);
            if ($arFile) {
                $resized = CFile::ResizeImageGet(
                    $arFile,
                    ['width' => 80, 'height' => 80],
                    BX_RESIZE_IMAGE_PROPORTIONAL
                );
                $imageMap[$arEl['ID']] = $resized ? $resized['src'] : $arFile['SRC'];
            }
        }
    }
}

// Attach image src to each item
foreach ($arResult['CATEGORIES'] as $categoryId => &$arCategory) {
    if ($categoryId === 'all') {
        continue;
    }
    foreach ($arCategory['ITEMS'] as &$arItem) {
        if (!isset($arItem['ITEM_ID'])) {
            continue;
        }
        $id = (int)$arItem['ITEM_ID'];
        if (isset($imageMap[$id])) {
            $arItem['ICON'] = $imageMap[$id];
        }
    }
    unset($arItem);
}
unset($arCategory);
