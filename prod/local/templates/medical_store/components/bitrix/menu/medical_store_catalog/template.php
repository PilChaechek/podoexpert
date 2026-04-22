<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Многоуровневое меню каталога (данные из bitrix:menu.sections → тип меню catalog).
 *
 * @var array $arResult
 * @var array $arParams
 */

$skin = isset($arParams['CATALOG_MENU_SKIN']) ? (string) $arParams['CATALOG_MENU_SKIN'] : 'header';

if ($skin === 'mobile') {
    $rootUlClass = 'mobile-nav__catalog-list list flex flex-col gap-2';
    $liClass = 'mobile-nav__catalog-item';
    $linkClass = 'mobile-nav__catalog-link link';
    $nestedUlClass = 'mobile-nav__catalog-list list flex flex-col gap-2 pl-4 mt-1 border-l border-slate-200';
    $rootUlExtra = '';
} else {
    $rootUlClass = 'main-nav__catalog-list list';
    $liClass = 'main-nav__catalog-list-item';
    $linkClass = 'main-nav__catalog-link link';
    $nestedUlClass = 'main-nav__catalog-list list flex flex-col gap-1 pl-5 mt-1';
    $rootUlExtra = ' role="menu" aria-label="Каталог"';
}

if (empty($arResult) || !is_array($arResult)) {
    return;
}
?>
<ul class="<?= htmlspecialcharsbx($rootUlClass) ?>"<?= $rootUlExtra ?>>
<?php
$previousLevel = 0;
foreach ($arResult as $arItem) {
    if (!is_array($arItem) || !isset($arItem['DEPTH_LEVEL'])) {
        continue;
    }
    $depth = (int) $arItem['DEPTH_LEVEL'];
    $isParent = !empty($arItem['IS_PARENT']);
    $perm = $arItem['PERMISSION'] ?? 'R';
    $text = isset($arItem['TEXT']) ? (string) $arItem['TEXT'] : '';
    $link = isset($arItem['LINK']) ? (string) $arItem['LINK'] : '';
    $selected = !empty($arItem['SELECTED']);
    $liSel = $selected ? ' selected' : '';

    if ($previousLevel && $depth < $previousLevel) {
        echo str_repeat('</ul></li>', $previousLevel - $depth);
    }

    if ($isParent) {
        ?>
        <li class="<?= htmlspecialcharsbx($liClass) . $liSel ?>" role="none">
            <?php if ($perm > 'D') { ?>
                <a href="<?= htmlspecialcharsbx($link) ?>" class="<?= htmlspecialcharsbx($linkClass) ?>" role="menuitem"><?= htmlspecialcharsbx($text) ?></a>
            <?php } else { ?>
                <a href="" class="<?= htmlspecialcharsbx($linkClass) ?>" role="menuitem" title="<?= GetMessage('MENU_ITEM_ACCESS_DENIED') ?>"><?= htmlspecialcharsbx($text) ?></a>
            <?php } ?>
            <ul class="<?= htmlspecialcharsbx($nestedUlClass) ?>" role="group">
        <?php
    } elseif ($perm > 'D') {
        ?>
        <li class="<?= htmlspecialcharsbx($liClass) . $liSel ?>" role="none">
            <a href="<?= htmlspecialcharsbx($link) ?>" class="<?= htmlspecialcharsbx($linkClass) ?>" role="menuitem"><?= htmlspecialcharsbx($text) ?></a>
        </li>
        <?php
    } else {
        ?>
        <li class="<?= htmlspecialcharsbx($liClass) . $liSel ?>" role="none">
            <a href="" class="<?= htmlspecialcharsbx($linkClass) ?>" role="menuitem" title="<?= GetMessage('MENU_ITEM_ACCESS_DENIED') ?>"><?= htmlspecialcharsbx($text) ?></a>
        </li>
        <?php
    }

    $previousLevel = $depth;
}

if ($previousLevel > 1) {
    echo str_repeat('</ul></li>', $previousLevel - 1);
}
?>
</ul>
