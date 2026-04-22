<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 */

$ulClass = isset($arParams['MENU_UL_CLASS']) ? (string) $arParams['MENU_UL_CLASS'] : 'top-nav__list flex gap-2 list';
$liClass = isset($arParams['MENU_LI_CLASS']) ? (string) $arParams['MENU_LI_CLASS'] : 'top-nav__item';
$linkClass = isset($arParams['MENU_LINK_CLASS']) ? (string) $arParams['MENU_LINK_CLASS'] : 'top-nav__link link';

if (empty($arResult) || !is_array($arResult)) {
    return;
}

$maxLevel = isset($arParams['MAX_LEVEL']) ? (int) $arParams['MAX_LEVEL'] : 1;
?>
<ul class="<?= htmlspecialcharsbx($ulClass) ?>">
<?php
foreach ($arResult as $arItem) {
    if (!is_array($arItem) || !isset($arItem['LINK'], $arItem['TEXT'])) {
        continue;
    }
    if ($maxLevel === 1 && (int) ($arItem['DEPTH_LEVEL'] ?? 1) > 1) {
        continue;
    }
    $liSelected = !empty($arItem['SELECTED']) ? ' selected' : '';
    ?>
    <li class="<?= htmlspecialcharsbx($liClass) ?><?= $liSelected ?>">
        <a href="<?= htmlspecialcharsbx($arItem['LINK']) ?>" class="<?= htmlspecialcharsbx($linkClass) ?>"><?= htmlspecialcharsbx($arItem['TEXT']) ?></a>
    </li>
    <?php
}
?>
</ul>
