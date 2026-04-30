<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */

if (empty($arResult['CATEGORIES']) || !$arResult['CATEGORIES_ITEMS_EXISTS']) {
    return;
}
?>
<div class="header-search-dropdown">
    <?php foreach ($arResult['CATEGORIES'] as $category_id => $arCategory): ?>
        <?php foreach ($arCategory['ITEMS'] as $arItem): ?>

            <?php if ($category_id === 'all'): ?>
                <div class="header-search-dropdown__all">
                    <a class="header-search-dropdown__all-link" href="<?= $arItem['URL'] ?>">
                        <?= $arItem['NAME'] ?>
                    </a>
                </div>

            <?php elseif (isset($arItem['ICON'])): ?>
                <div class="header-search-dropdown__item">
                    <a class="header-search-dropdown__link" href="<?= $arItem['URL'] ?>">
                        <img class="header-search-dropdown__img" src="<?= $arItem['ICON'] ?>" alt="">
                        <span class="header-search-dropdown__name"><?= $arItem['NAME'] ?></span>
                    </a>
                </div>

            <?php else: ?>
                <div class="header-search-dropdown__item">
                    <a class="header-search-dropdown__link" href="<?= $arItem['URL'] ?>">
                        <span class="header-search-dropdown__name"><?= $arItem['NAME'] ?></span>
                    </a>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<?php
// Stub table required by JCTitleSearch JS for keyboard navigation
$rows = '';
foreach ($arResult['CATEGORIES'] as $category_id => $arCategory) {
    foreach ($arCategory['ITEMS'] as $arItem) {
        $url = $category_id === 'all'
            ? $arItem['URL']
            : (isset($arItem['ICON']) ? $arItem['URL'] : $arItem['URL']);
        $rows .= '<tr><td><a href="' . $arItem['URL'] . '">' . $arItem['NAME'] . '</a></td></tr>';
    }
}
?>
<table class="title-search-result" style="display:none">
    <?= $rows ?>
</table>
