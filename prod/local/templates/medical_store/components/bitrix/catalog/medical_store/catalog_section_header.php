<?php
/**
 * Крошки + H1 (корень каталога и разделы). Ожидает: $arParams, $arResult, $arCurSection, $APPLICATION.
 *
 * @var CMain $APPLICATION
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

global $APPLICATION;

$catalogHref = $arResult['FOLDER'];
$breadcrumbItems = [];
$sectionIdKnown = isset($arCurSection['ID']);
$sectionId = (int) ($arCurSection['ID'] ?? 0);

if ($sectionIdKnown && $sectionId === 0 && Loader::includeModule('iblock')) {
	$breadcrumbItems[] = ['TITLE' => 'Главная', 'LINK' => SITE_DIR];
	$breadcrumbItems[] = ['TITLE' => 'Каталог', 'LINK' => ''];
} elseif ($sectionIdKnown && $sectionId > 0 && Loader::includeModule('iblock')) {
	$breadcrumbItems[] = ['TITLE' => 'Главная', 'LINK' => SITE_DIR];
	$breadcrumbItems[] = ['TITLE' => 'Каталог', 'LINK' => $catalogHref];
	$dbPath = CIBlockSection::GetNavChain(
		(int) $arParams['IBLOCK_ID'],
		(int) $arCurSection['ID'],
		[],
		['ID', 'NAME', 'CODE', 'SECTION_PAGE_URL']
	);
	$codesAccum = [];
	while ($navRow = $dbPath->Fetch()) {
		if ((string) ($navRow['CODE'] ?? '') !== '') {
			$codesAccum[] = (string) $navRow['CODE'];
		}
		$isCurrentSection = ((int) ($navRow['ID'] ?? 0) === (int) $arCurSection['ID']);
		$href = '';
		if (!$isCurrentSection) {
			$href = trim((string) ($navRow['SECTION_PAGE_URL'] ?? ''));
			if ($href !== '' && strpos($href, '#') !== false) {
				$href = '';
			}
			if ($href === '' && $codesAccum !== []) {
				$href = $catalogHref . implode('/', $codesAccum) . '/';
			}
		}
		$breadcrumbItems[] = ['TITLE' => (string) ($navRow['NAME'] ?? ''), 'LINK' => $href];
	}
}
?>
<section class="section section--t2 header-title">
	<div class="container">
		<div class="mb-2">
			<?php
			if ($breadcrumbItems !== []) {
				$savedResult = $arResult;
				$arResult = $breadcrumbItems;
				$breadcrumbTpl = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/medical_store/components/bitrix/breadcrumb/medical_store/template.php';
				if (is_file($breadcrumbTpl)) {
					echo include $breadcrumbTpl;
				}
				$arResult = $savedResult;
			} else {
				$APPLICATION->IncludeComponent(
					'bitrix:breadcrumb',
					'medical_store',
					[
						'START_FROM' => '0',
						'SITE_ID' => SITE_ID,
					],
					false
				);
			}
			?>
		</div>
		<?php
		$h1 = trim((string) ($arCurSection['NAME'] ?? ''));
		if ($h1 === '') {
			$h1 = (string) $APPLICATION->GetTitle(false);
		}
		if ($h1 === '') {
			$h1 = 'Каталог';
		}
		?>
		<div class="header-title__center2">
			<h1 class="header-title__heading text-3xl md:text-4xl font-bold"><?= htmlspecialcharsbx($h1) ?></h1>
		</div>
	</div>
</section>
