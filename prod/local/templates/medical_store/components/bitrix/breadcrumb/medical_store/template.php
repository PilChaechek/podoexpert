<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

if (empty($arResult)) {
    return '';
}

$itemSize = count($arResult);
$homeLabel = 'Главная';
$strReturn = '<nav class="header-title__breadcrumbs" aria-label="Навигация по разделу">';

for ($i = 0; $i < $itemSize; $i++) {
    $title = (string) ($arResult[$i]['TITLE'] ?? '');
    $link = (string) ($arResult[$i]['LINK'] ?? '');
    $isLast = $i === $itemSize - 1;
    $isFirst = $i === 0;

    if ($i > 0) {
        $strReturn .= '<span class="header-title__crumb-sep" aria-hidden="true">&gt;</span>';
    }

    $titleEsc = htmlspecialcharsbx($title);
    $linkEsc = htmlspecialcharsbx($link);

    if ($isLast || $link === '') {
        $strReturn .= '<span class="header-title__crumb-current">' . $titleEsc . '</span>';
        continue;
    }

    $isHomeHref = $isFirst && ($link === '/' || $link === SITE_DIR || rtrim($link, '/') === '');
    if ($isHomeHref) {
        $aria = $titleEsc !== '' ? $titleEsc : htmlspecialcharsbx($homeLabel);
        $strReturn .= '<a href="' . $linkEsc . '" aria-label="' . $aria . '">';
        $strReturn .= '<span class="sr-only">' . $aria . '</span>';
        $strReturn .= '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">';
        $strReturn .= '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" stroke-linecap="round" stroke-linejoin="round" />';
        $strReturn .= '</svg></a>';
        continue;
    }

    $strReturn .= '<a href="' . $linkEsc . '">' . $titleEsc . '</a>';
}

$strReturn .= '</nav>';

return $strReturn;
