<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @var CBitrixComponentTemplate $this */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

if (!isset($arParams['FILTER_VIEW_MODE']) || (string) $arParams['FILTER_VIEW_MODE'] === '') {
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
}
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] === 'Y' ? 'Y' : 'N');

$isVerticalFilter = ($arParams['USE_FILTER'] === 'Y' && $arParams['FILTER_VIEW_MODE'] === 'VERTICAL');
$isSidebar = ($arParams['SIDEBAR_SECTION_SHOW'] === 'Y' && !empty($arParams['SIDEBAR_PATH'] ?? ''));
$isFilter = ($arParams['USE_FILTER'] === 'Y');

if (!isset($arResult['VARIABLES']['SECTION_ID'])) {
	$arResult['VARIABLES']['SECTION_ID'] = 0;
} else {
	$arResult['VARIABLES']['SECTION_ID'] = (int) $arResult['VARIABLES']['SECTION_ID'];
}
if (!isset($arResult['VARIABLES']['SECTION_CODE'])) {
	$arResult['VARIABLES']['SECTION_CODE'] = '';
}
if (!isset($arResult['VARIABLES']['SMART_FILTER_PATH'])) {
	$arResult['VARIABLES']['SMART_FILTER_PATH'] = '';
}

if ($isFilter) {
	// Корень каталога: весь ИБ, как в умном фильтре (SECTION_ID = 0)
	$arCurSection = ['ID' => 0];
} else {
	$arCurSection = [];
}
?>
<div class="container">
	<div class="row">
		<?php
		if ($isVerticalFilter) {
			include $_SERVER['DOCUMENT_ROOT'] . '/' . $this->GetFolder() . '/section_vertical.php';
		} else {
			include $_SERVER['DOCUMENT_ROOT'] . '/' . $this->GetFolder() . '/section_horizontal.php';
		}
		?>
	</div>
</div>
