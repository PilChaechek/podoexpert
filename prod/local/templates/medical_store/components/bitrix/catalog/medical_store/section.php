<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;

$this->setFrameMode(true);

if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '')
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');

$isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
$isSidebar = ($arParams["SIDEBAR_SECTION_SHOW"] == "Y" && isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
$isFilter = ($arParams['USE_FILTER'] == 'Y');

$arCurSection = array();
$sectionId = (int)($arResult["VARIABLES"]["SECTION_ID"] ?? 0);
$sectionCode = (string)($arResult["VARIABLES"]["SECTION_CODE"] ?? '');

if ($isFilter || $sectionId > 0 || $sectionCode !== '')
{
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	if ($sectionId > 0)
		$arFilter["ID"] = $sectionId;
	elseif ($sectionCode !== '')
		$arFilter["=CODE"] = $sectionCode;

	$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog_cur_section"))
	{
		$arCurSection = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$arCurSection = array();
		if (Loader::includeModule("iblock"))
		{
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "NAME"));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog_cur_section");

				if ($arCurSection = $dbRes->Fetch())
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);

				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				if(!$arCurSection = $dbRes->Fetch())
					$arCurSection = array();
			}
		}
		$obCache->EndDataCache($arCurSection);
	}
	if (!isset($arCurSection))
		$arCurSection = array();
}
?>
<div class="container">
	<div class="mb-2">
		<?php
		/**
		 * Крошки из $APPLICATION здесь только «Главная» → «Каталог»: цепочку раздела
		 * добавляет bitrix:catalog.section ниже по коду — позже шаблона section.php.
		 * Поэтому путь собираем из CIBlockSection::GetNavChain и тот же шаблон visual.
		 */
		global $APPLICATION;
		$breadcrumbItems = [];
		if (!empty($arCurSection['ID']) && Loader::includeModule('iblock'))
		{
			$catalogHref = $arResult['FOLDER'];
			$breadcrumbItems[] = ['TITLE' => 'Главная', 'LINK' => SITE_DIR];
			$breadcrumbItems[] = ['TITLE' => 'Каталог', 'LINK' => $catalogHref];
			$dbPath = CIBlockSection::GetNavChain(
				(int)$arParams['IBLOCK_ID'],
				(int)$arCurSection['ID'],
				[],
				['ID', 'NAME', 'CODE', 'SECTION_PAGE_URL']
			);
			$codesAccum = [];
			while ($navRow = $dbPath->Fetch())
			{
				if ((string)($navRow['CODE'] ?? '') !== '')
				{
					$codesAccum[] = (string)$navRow['CODE'];
				}
				$isCurrentSection = ((int)($navRow['ID'] ?? 0) === (int)$arCurSection['ID']);
				$href = '';
				if (!$isCurrentSection)
				{
					$href = trim((string)($navRow['SECTION_PAGE_URL'] ?? ''));
					// В ИБ часто хранится шаблон с #SITE_DIR#/#SECTION_CODE# — без подстановки в GetNavChain
					if ($href !== '' && strpos($href, '#') !== false)
					{
						$href = '';
					}
					if ($href === '' && $codesAccum !== [])
					{
						$href = $catalogHref . implode('/', $codesAccum) . '/';
					}
				}
				$breadcrumbItems[] = ['TITLE' => (string)($navRow['NAME'] ?? ''), 'LINK' => $href];
			}
		}

		if ($breadcrumbItems !== [])
		{
			$savedResult = $arResult;
			$arResult = $breadcrumbItems;
			$breadcrumbTpl = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/medical_store/components/bitrix/breadcrumb/medical_store/template.php';
			if (is_file($breadcrumbTpl))
			{
				echo include $breadcrumbTpl;
			}
			$arResult = $savedResult;
		}
		else
		{
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
	$h1 = trim((string)($arCurSection['NAME'] ?? ''));
	if ($h1 === '') {
		$h1 = (string)$APPLICATION->GetTitle(false);
	}
	?>
	<h1><?= htmlspecialcharsbx($h1) ?></h1>
</div>
<section class="section section--p0 catalog-page">
	<div class="container">
		<div class="catalog-page__layout">
			<? include($_SERVER["DOCUMENT_ROOT"] . "/" . $this->GetFolder() . "/section_vertical.php");?>
		</div>
	</div>
</section>