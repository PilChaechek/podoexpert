<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

require_once __DIR__ . '/gallery_photos.php';

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

$templateLibrary = array('popup', 'fx', 'ui.fonts.opensans');
$currencyList = '';

if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
	'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'ITEM' => [
		'ID' => $arResult['ID'],
		'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	],
];
if ($haveOffers)
{
	$templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
	$templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
	'ID' => $mainId,
	'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict',
	'STICKER_ID' => $mainId.'_sticker',
	'BIG_SLIDER_ID' => $mainId.'_big_slider',
	'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont',
	'SLIDER_CONT_ID' => $mainId.'_slider_cont',
	'OLD_PRICE_ID' => $mainId.'_old_price',
	'PRICE_ID' => $mainId.'_price',
	'DESCRIPTION_ID' => $mainId.'_description',
	'DISCOUNT_PRICE_ID' => $mainId.'_price_discount',
	'PRICE_TOTAL' => $mainId.'_price_total',
	'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_',
	'QUANTITY_ID' => $mainId.'_quantity',
	'QUANTITY_DOWN_ID' => $mainId.'_quant_down',
	'QUANTITY_UP_ID' => $mainId.'_quant_up',
	'QUANTITY_MEASURE' => $mainId.'_quant_measure',
	'QUANTITY_LIMIT' => $mainId.'_quant_limit',
	'BUY_LINK' => $mainId.'_buy_link',
	'ADD_BASKET_LINK' => $mainId.'_add_basket_link',
	'BASKET_ACTIONS_ID' => $mainId.'_basket_actions',
	'NOT_AVAILABLE_MESS' => $mainId.'_not_avail',
	'COMPARE_LINK' => $mainId.'_compare_link',
	'TREE_ID' => $mainId.'_skudiv',
	'DISPLAY_PROP_DIV' => $mainId.'_sku_prop',
	'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop',
	'OFFER_GROUP' => $mainId.'_set_group_',
	'BASKET_PROP_DIV' => $mainId.'_basket_prop',
	'SUBSCRIBE_LINK' => $mainId.'_subscribe',
	'TABS_ID' => $mainId.'_tabs',
	'TAB_CONTAINERS_ID' => $mainId.'_tab_containers',
	'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel',
	'TABS_PANEL_ID' => $mainId.'_tabs_panel',
);
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

if ($haveOffers)
{
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
}
else
{
	$actualItem = $arResult;
}

$volumeName = '';
$volumeDisplay = '';
if (!empty($actualItem['DISPLAY_PROPERTIES']['VOLUME']))
{
	$vprop = $actualItem['DISPLAY_PROPERTIES']['VOLUME'];
	$volumeName = (string)($vprop['NAME'] ?? '');
	$volumeDisplay = $vprop['DISPLAY_VALUE'] ?? $vprop['VALUE'] ?? '';
	if (is_array($volumeDisplay))
	{
		$volumeDisplay = implode(', ', $volumeDisplay);
	}
}
elseif (!empty($actualItem['PROPERTIES']['VOLUME']))
{
	$vprop = $actualItem['PROPERTIES']['VOLUME'];
	$volumeName = (string)($vprop['NAME'] ?? '');
	$v = $vprop['VALUE'] ?? '';
	$volumeDisplay = is_array($v) ? implode(', ', $v) : (string)$v;
}

$countryName = '';
$countryDisplay = '';
if (!empty($actualItem['DISPLAY_PROPERTIES']['COUNTRY']))
{
	$cprop = $actualItem['DISPLAY_PROPERTIES']['COUNTRY'];
	$countryName = (string)($cprop['NAME'] ?? '');
	$countryDisplay = $cprop['DISPLAY_VALUE'] ?? $cprop['VALUE'] ?? '';
	if (is_array($countryDisplay))
	{
		$countryDisplay = implode(', ', $countryDisplay);
	}
}
elseif (!empty($actualItem['PROPERTIES']['COUNTRY']))
{
	$cprop = $actualItem['PROPERTIES']['COUNTRY'];
	$countryName = (string)($cprop['NAME'] ?? '');
	$v = $cprop['VALUE'] ?? '';
	$countryDisplay = is_array($v) ? implode(', ', $v) : (string)$v;
	if ($countryDisplay === '' && !empty($cprop['VALUE_ENUM']))
	{
		$ve = $cprop['VALUE_ENUM'];
		$countryDisplay = is_array($ve) ? implode(', ', $ve) : (string)$ve;
	}
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y')
{
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '')
		{
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}
else
{
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE)
{
	$arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;

	$arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
}
else
{
	$arParams['~MESS_NOT_AVAILABLE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;

	$arParams['MESS_NOT_AVAILABLE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
	'left' => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right' => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$podexpertGalleryPhotos = medical_store_podexpert_gallery_photos($arResult, $arParams);
$showSliderControls = count($podexpertGalleryPhotos) > 1;
$podexpertGlightboxElements = array();
foreach ($podexpertGalleryPhotos as $gp)
{
	$podexpertGlightboxElements[] = array(
		'href' => $gp['SRC'],
		'type' => 'image',
		'alt' => $alt,
		'zoomable' => false,
		'draggable' => true,
	);
}
$podexpertCatalogGalleryJs = array();
foreach ($podexpertGalleryPhotos as $gp)
{
	$podexpertCatalogGalleryJs[] = array(
		'ID' => (int)($gp['ID'] ?? 0),
		'SRC' => (string)($gp['SRC'] ?? ''),
		'WIDTH' => (int)($gp['WIDTH'] ?? 0),
		'HEIGHT' => (int)($gp['HEIGHT'] ?? 0),
	);
}
$podexpertShowHeroDebug = isset($_GET['debug_hero']) && (string)$_GET['debug_hero'] !== '0';
?>
<div class="product-tmp" id="<?=$itemIds['ID']?>"
	itemscope itemtype="http://schema.org/Product">
	<script>window.PODEXPERT_CATALOG_GALLERY_SLIDER=<?=CUtil::PhpToJSObject($podexpertCatalogGalleryJs, false, false, true)?>;</script>
	<?php if (!empty($podexpertShowHeroDebug)): ?>
	<script>window.__PODEXPERT_CATALOG_HERO_DEBUG=1;</script>
	<div class="mb-4 max-w-4xl rounded border border-amber-600/80 bg-amber-50 p-3 text-left text-xs text-neutral-900" id="podexpert-hero-debug-wrap" role="region" aria-label="Отладка product-hero (debug_hero)">
		<div class="mb-1 text-[11px] text-neutral-600">URL-параметр <code>debug_hero=1</code> · <code>window.__PODEXPERT_HERO_DEBUG</code> · при флаге — консоль: <code>[podexpert-hero]</code></div>
		<pre id="podexpert-hero-debug" class="m-0 max-h-96 overflow-auto whitespace-pre-wrap break-words font-mono">ожидание init…</pre>
	</div>
	<?php endif; ?>
	<section class="section section--t2 product">
		<div class="container">
			<div class="grid grid-cols-1 gap-8 items-start mt-6 lg:mt-8 lg:grid-cols-2 lg:gap-x-12">
				<div class="min-w-0 order-1 lg:order-1 w-full">
					<div class="product-hero w-full max-w-2xl mx-auto lg:mx-0 lg:max-w-none">
				<div class="relative w-full" id="<?=$itemIds['BIG_SLIDER_ID']?>" data-product-hero-root="1">
					<span class="hidden" data-entity="close-popup" aria-hidden="true"></span>
					<div class="!pt-0 !h-auto w-full" data-entity="images-slider-block" data-podexpert-hero="1">
						<span class="hidden" data-entity="slider-control-left" aria-hidden="true"></span>
						<span class="hidden" data-entity="slider-control-right" aria-hidden="true"></span>
						<div class="product-hero__main-wrap relative aspect-square w-full overflow-hidden bg-neutral-50 rounded-sm">
							<div class="product-item-label-text <?=$labelPositionClass?> absolute left-0 top-0 z-20 max-w-[min(100%,20rem)]" id="<?=$itemIds['STICKER_ID']?>"
								<?=(!$arResult['LABEL'] ? 'style="display: none;"' : '' )?>>
								<?php
								if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE']))
								{
									foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value)
									{
										?>
										<div<?=(!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
											<span title="<?=$value?>"><?=$value?></span>
										</div>
										<?php
									}
								}
								?>
							</div>
							<?php
							if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
							{
								if ($haveOffers)
								{
									?>
									<div class="product-item-label-ring <?=$discountPositionClass?> absolute z-20" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
										style="display: none;">
									</div>
									<?php
								}
								else
								{
									if ($price['DISCOUNT'] > 0)
									{
										?>
										<div class="product-item-label-ring <?=$discountPositionClass?> absolute z-20" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
											title="<?=-$price['PERCENT']?>%">
											<span><?=-$price['PERCENT']?>%</span>
										</div>
										<?php
									}
								}
							}
							?>
							<div class="h-full" data-entity="images-container">
							<?php
							if (!empty($podexpertGalleryPhotos))
							{
								?>
								<div class="swiper product-hero__main js-product-hero-main h-full w-full" aria-label="Галерея товара">
									<div class="swiper-wrapper">
								<?php
								foreach ($podexpertGalleryPhotos as $key => $photo)
								{
									?>
									<div class="swiper-slide h-full w-full !flex items-center justify-center p-2">
										<div class="h-full w-full max-h-full<?=($key == 0 ? ' active' : '')?>" data-entity="image" data-id="<?=(int)$photo['ID']?>">
											<img class="h-full w-full object-contain object-center" src="<?=htmlspecialchars($photo['SRC'])?>" alt="<?=htmlspecialchars($alt)?>" title="<?=htmlspecialchars($title)?>" loading="<?= $key === 0 ? 'eager' : 'lazy' ?>" decoding="async"<?=($key == 0 ? ' itemprop="image"' : '')?>>
										</div>
									</div>
									<?php
								}
								?>
									</div>
								</div>
								<?php
							}
							if ($arParams['SLIDER_PROGRESS'] === 'Y')
							{
								?>
								<div class="absolute bottom-0 left-0 z-[180] h-0.5 w-0 bg-neutral-400" data-entity="slider-progress-bar" style="width: 0;"></div>
								<?php
							}
							?>
							</div>
						</div>
					</div>
					<script type="application/json" id="product-hero-glightbox-elements"><?= htmlspecialcharsbx(json_encode($podexpertGlightboxElements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></script>
					<?php
					if ($showSliderControls)
					{
						if ($haveOffers)
						{
							foreach ($arResult['OFFERS'] as $keyOffer => $offer)
							{
								$strVisible = $arResult['OFFERS_SELECTED'] == $keyOffer ? '' : 'none';
								?>
								<div id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>" style="display: <?=$strVisible?>;">
									<div class="swiper product-hero__thumbs js-product-hero-thumbs mt-4 w-full min-w-0 max-w-full self-start" aria-label="Миниатюры">
										<div class="swiper-wrapper">
									<?php
									if (!empty($podexpertGalleryPhotos))
									{
									foreach ($podexpertGalleryPhotos as $keyPhoto => $photo)
									{
										?>
										<div class="swiper-slide h-auto cursor-pointer">
										<div class="product-hero__thumb box-border aspect-square w-full overflow-hidden rounded border-2 border-transparent bg-neutral-50 p-0.5 transition-[border-color,opacity]<?=($keyPhoto == 0 ? ' active' : '')?>"
											data-entity="slider-control" data-value="<?=(int)$offer['ID']?>_<?=(int)$photo['ID']?>">
											<img class="h-full w-full object-contain" src="<?=htmlspecialchars($photo['SRC'])?>" alt="" loading="lazy" decoding="async" sizes="(max-width: 640px) 15vw, 60px">
										</div>
										</div>
										<?php
									}
									}
									?>
										</div>
									</div>
								</div>
								<?php
							}
						}
						else
						{
							?>
							<div id="<?=$itemIds['SLIDER_CONT_ID']?>">
								<div class="swiper product-hero__thumbs js-product-hero-thumbs mt-4 w-full min-w-0 max-w-full self-start" aria-label="Миниатюры">
									<div class="swiper-wrapper">
							<?php
							if (!empty($podexpertGalleryPhotos))
							{
								foreach ($podexpertGalleryPhotos as $key => $photo)
								{
									?>
									<div class="swiper-slide h-auto cursor-pointer">
									<div class="product-hero__thumb box-border aspect-square w-full overflow-hidden rounded border-2 border-transparent bg-neutral-50 p-0.5 transition-[border-color,opacity]<?=($key == 0 ? ' active' : '')?>"
										data-entity="slider-control" data-value="<?=(int)$photo['ID']?>">
										<img class="h-full w-full object-contain" src="<?=htmlspecialchars($photo['SRC'])?>" alt="" loading="lazy" decoding="async" sizes="(max-width: 640px) 15vw, 60px">
									</div>
									</div>
									<?php
								}
							}
							?>
									</div>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
					</div>
				</div>
				<div class="min-w-0 order-2 lg:order-2 space-y-5 lg:space-y-4 w-full">
					<?php
					if ($arParams['DISPLAY_NAME'] === 'Y')
					{
						?>
					<div>
						<h1 class="product__title text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl text-balance" itemprop="name">
							<?=htmlspecialcharsEx($name)?>
						</h1>
						<?php
						if ($volumeDisplay !== '' || $countryDisplay !== '')
						{
							?>
						<p class="mt-2 font-bold text-neutral-800">
							<?php
							if ($volumeDisplay !== '')
							{
								?>
							<span class="block"><?= $volumeName !== '' ? htmlspecialcharsEx($volumeName).': ' : '' ?><?=htmlspecialcharsEx($volumeDisplay)?></span>
								<?php
							}
							if ($countryDisplay !== '')
							{
								?>
							<span class="block<?= $volumeDisplay !== '' ? ' mt-1' : '' ?>"><?= $countryName !== '' ? htmlspecialcharsEx($countryName).': ' : '' ?><?=htmlspecialcharsEx($countryDisplay)?></span>
								<?php
							}
							?>
						</p>
							<?php
						}
						?>
					</div>
						<?php
					}
					?>
						<div class="product-item-detail-info-section">
							<?php
							foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'sku':
										if ($haveOffers && !empty($arResult['OFFERS_PROP']))
										{
											?>
											<div id="<?=$itemIds['TREE_ID']?>">
												<?php
												foreach ($arResult['SKU_PROPS'] as $skuProperty)
												{
													if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
														continue;

													$propertyId = $skuProperty['ID'];
													$skuProps[] = array(
														'ID' => $propertyId,
														'SHOW_MODE' => $skuProperty['SHOW_MODE'],
														'VALUES' => $skuProperty['VALUES'],
														'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
													);
													?>
													<div class="product-item-detail-info-container" data-entity="sku-line-block">
														<div class="product-item-detail-info-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
														<div class="product-item-scu-container">
															<div class="product-item-scu-block">
																<div class="product-item-scu-list">
																	<ul class="product-item-scu-item-list">
																		<?php
																		foreach ($skuProperty['VALUES'] as &$value)
																		{
																			$value['NAME'] = htmlspecialcharsbx($value['NAME']);

																			if ($skuProperty['SHOW_MODE'] === 'PICT')
																			{
																				?>
																				<li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
																					data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																					data-onevalue="<?=$value['ID']?>">
																					<div class="product-item-scu-item-color-block">
																						<div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
																							style="background-image: url('<?=$value['PICT']['SRC']?>');">
																						</div>
																					</div>
																				</li>
																				<?php
																			}
																			else
																			{
																				?>
																				<li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
																					data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																					data-onevalue="<?=$value['ID']?>">
																					<div class="product-item-scu-item-text-block">
																						<div class="product-item-scu-item-text"><?=$value['NAME']?></div>
																					</div>
																				</li>
																				<?php
																			}
																		}
																		?>
																	</ul>
																	<div style="clear: both;"></div>
																</div>
															</div>
														</div>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										}

										break;

									case 'props':
										if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS'])
										{
											?>
											<div class="product-item-detail-info-container">
												<?php
												if (!empty($arResult['DISPLAY_PROPERTIES']))
												{
													?>
													<dl class="product-item-detail-properties">
														<?php
														foreach ($arResult['DISPLAY_PROPERTIES'] as $property)
														{
															if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']]))
															{
																?>
																<dt><?=$property['NAME']?></dt>
																<dd><?=(is_array($property['DISPLAY_VALUE'])
																		? implode(' / ', $property['DISPLAY_VALUE'])
																		: $property['DISPLAY_VALUE'])?>
																</dd>
																<?php
															}
														}
														unset($property);
														?>
													</dl>
													<?php
												}

												if ($arResult['SHOW_OFFERS_PROPS'])
												{
													?>
													<dl class="product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></dl>
													<?php
												}
												?>
											</div>
											<?php
										}

										break;
								}
							}
							?>
					</div>
					<?php
					if ($showDescription)
					{
						?>
					<div class="border-b border-gray-300 pb-8 lg:pb-5">
						<p
							class="text-white text-xl font-bold px-2 py-1.5 leading-snug font-headings inline-block my-0"
							style="background-color: var(--color-main);"
						>Линия регенерации</p>
						<div
							class="product__description mt-3 text-neutral-700"
							id="<?=$itemIds['DESCRIPTION_ID']?>"
							itemprop="description"
						>
							<?php
							if (
								$arResult['PREVIEW_TEXT'] != ''
								&& (
									$arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'S'
									|| ($arParams['DISPLAY_PREVIEW_TEXT_MODE'] === 'E' && $arResult['DETAIL_TEXT'] == '')
								)
							)
							{
								echo $arResult['PREVIEW_TEXT_TYPE'] === 'html' ? $arResult['PREVIEW_TEXT'] : '<p>'.$arResult['PREVIEW_TEXT'].'</p>';
							}
							if ($arResult['DETAIL_TEXT'] != '')
							{
								echo $arResult['DETAIL_TEXT_TYPE'] === 'html' ? $arResult['DETAIL_TEXT'] : '<p>'.$arResult['DETAIL_TEXT'].'</p>';
							}
							?>
						</div>
					</div>
						<?php
					}
					?>
					<div class="product__pay-block">
							<?php
							$productPayBlockOrder = $arParams['PRODUCT_PAY_BLOCK_ORDER'];
							$productPayQtyIdx = is_array($productPayBlockOrder)
								? array_search('quantity', $productPayBlockOrder, true)
								: false;
							$productPayButtonsIdx = is_array($productPayBlockOrder)
								? array_search('buttons', $productPayBlockOrder, true)
								: false;
							$productCartQtyButtonsMerged = !empty($arParams['USE_PRODUCT_QUANTITY'])
								&& $actualItem['CAN_BUY']
								&& $productPayQtyIdx !== false
								&& $productPayButtonsIdx !== false
								&& $productPayQtyIdx < $productPayButtonsIdx;
							unset($productPayBlockOrder, $productPayQtyIdx, $productPayButtonsIdx);

							foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
							{
								switch ($blockName)
								{
									case 'rating':
										if ($arParams['USE_VOTE_RATING'] === 'Y')
										{
											?>
											<div class="product-item-detail-info-container">
												<?php
												$APPLICATION->IncludeComponent(
													'bitrix:iblock.vote',
													'stars',
													array(
														'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
														'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
														'IBLOCK_ID' => $arParams['IBLOCK_ID'],
														'ELEMENT_ID' => $arResult['ID'],
														'ELEMENT_CODE' => '',
														'MAX_VOTE' => '5',
														'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
														'SET_STATUS_404' => 'N',
														'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
														'CACHE_TYPE' => $arParams['CACHE_TYPE'],
														'CACHE_TIME' => $arParams['CACHE_TIME']
													),
													$component,
													array('HIDE_ICONS' => 'Y')
												);
												?>
											</div>
											<?php
										}

										break;

									case 'price':
										?>
										<p class="product-price price mt-0 mb-5 flex flex-wrap items-center gap-x-3 gap-y-1">
											<span
												class="text-2xl font-bold tabular-nums" style="color: var(--color-accent);"
											>
												<span class="price-amount" id="<?=$itemIds['PRICE_ID']?>" data-wg-notranslate=""><?=$price['PRINT_RATIO_PRICE']?></span>
											</span>
											<?php
											if ($arParams['SHOW_OLD_PRICE'] === 'Y')
											{
												?>
											<span
												class="text-sm font-medium tabular-nums text-neutral-500 line-through decoration-neutral-400" id="<?=$itemIds['OLD_PRICE_ID']?>"
												style="display: <?=($showDiscount ? '' : 'none')?>;"
											>
												<span class="price-amount" data-wg-notranslate=""><?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?></span>
											</span>
											<span
												class="inline-block rounded-md px-2 py-0.5 text-sm font-medium tabular-nums"
												id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"
												style="background-color: var(--background-error); color: var(--color-error); display: <?=($showDiscount ? 'inline-block' : 'none')?>;"
											><?=($showDiscount ? -$price['PERCENT'].'%' : '')?></span>
												<?php
											}
											?>
										</p>
										<?php
										break;

									case 'priceRanges':
										if ($arParams['USE_PRICE_COUNT'])
										{
											$showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
											$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
											?>
											<div class="product-item-detail-info-container"
												<?=$showRanges ? '' : 'style="display: none;"'?>
												data-entity="price-ranges-block">
												<div class="product-item-detail-info-container-title">
													<?=$arParams['MESS_PRICE_RANGES_TITLE']?>
													<span data-entity="price-ranges-ratio-header">
														(<?=(Loc::getMessage(
															'CT_BCE_CATALOG_RATIO_PRICE',
															array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
														))?>)
													</span>
												</div>
												<dl class="product-item-detail-properties" data-entity="price-ranges-body">
													<?php
													if ($showRanges)
													{
														foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
														{
															if ($range['HASH'] !== 'ZERO-INF')
															{
																$itemPrice = false;

																foreach ($arResult['ITEM_PRICES'] as $itemPrice)
																{
																	if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
																	{
																		break;
																	}
																}

																if ($itemPrice)
																{
																	?>
																	<dt>
																		<?php
																		echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_FROM',
																				array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			).' ';

																		if (is_infinite($range['SORT_TO']))
																		{
																			echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
																		}
																		else
																		{
																			echo Loc::getMessage(
																				'CT_BCE_CATALOG_RANGE_TO',
																				array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																			);
																		}
																		?>
																	</dt>
																	<dd><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></dd>
																	<?php
																}
															}
														}
													}
													?>
												</dl>
											</div>
											<?php
											unset($showRanges, $useRatio, $itemPrice, $range);
										}

										break;

									case 'quantityLimit':
										if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
										{
											if ($haveOffers)
											{
												?>
												<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
													<div class="product-item-detail-info-container-title">
														<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
														<span class="product-item-quantity" data-entity="quantity-limit-value"></span>
													</div>
												</div>
												<?php
											}
											else
											{
												if (
													$measureRatio
													&& (float)$actualItem['PRODUCT']['QUANTITY'] > 0
													&& $actualItem['CHECK_QUANTITY']
												)
												{
													?>
													<div class="product-item-detail-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
														<div class="product-item-detail-info-container-title">
															<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
															<span class="product-item-quantity" data-entity="quantity-limit-value">
																<?php
																if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
																{
																	if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
																	}
																	else
																	{
																		echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
																	}
																}
																else
																{
																	echo $actualItem['PRODUCT']['QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
																}
																?>
															</span>
														</div>
													</div>
													<?php
												}
											}
										}

										break;

									case 'quantity':
										if ($arParams['USE_PRODUCT_QUANTITY'])
										{
											if ($productCartQtyButtonsMerged)
											{
												?>
												<div data-entity="main-button-container" class="cart product-cart w-full min-w-0 pt-1">
												<div
													class="product-cart__row flex w-full min-w-0 items-stretch gap-3"
													data-quantity-wrapper=""
												>
												<div class="product-item-detail-info-container" data-entity="quantity-block">
												<div
													class="product-cart__qty flex h-12 shrink-0 items-center overflow-hidden rounded-lg border"
													style="background-color: var(--color-2); border-color: var(--color-border);"
												>
													<button
														type="button"
														class="product-cart__qty-btn flex w-9 shrink-0 items-center justify-center border-0 bg-transparent p-0 text-neutral-600 transition-opacity hover:opacity-70"
														data-quantity-changer="decrease"
														id="<?=$itemIds['QUANTITY_DOWN_ID']?>"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
														<svg class="pointer-events-none h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M18 12L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
														</svg>
													</button>
													<input
														class="product-cart__qty-input product-item-amount-field qty text w-9 min-w-0 shrink border-0 bg-transparent text-center text-sm font-medium tabular-nums text-neutral-800 focus:ring-0 focus:outline-none"
														id="<?=$itemIds['QUANTITY_ID']?>"
														type="number"
														name="quantity"
														value="<?=$price['MIN_QUANTITY']?>"
														min="1"
														step="1"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
													<button
														type="button"
														class="product-cart__qty-btn flex w-9 shrink-0 items-center justify-center border-0 bg-transparent p-0 text-neutral-600 transition-opacity hover:opacity-70"
														data-quantity-changer="increase"
														id="<?=$itemIds['QUANTITY_UP_ID']?>"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
														<svg class="pointer-events-none h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M12 6L12 18" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
															<path d="M18 12L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
														</svg>
													</button>
												</div>
												<span class="product-item-amount-description-container sr-only" aria-hidden="true">
													<span id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem['ITEM_MEASURE']['TITLE']?></span>
													<span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
												</span>
												</div>
												<?php
											}
											else
											{
												?>
												<div class="product-item-detail-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>"
													data-entity="quantity-block">
												<div
													class="product-cart__row flex w-full min-w-0 items-stretch gap-3"
													data-quantity-wrapper=""
												>
												<div
													class="product-cart__qty flex h-12 shrink-0 items-stretch overflow-hidden rounded-lg border"
													style="background-color: var(--color-2); border-color: var(--color-border);"
												>
													<button
														type="button"
														class="product-cart__qty-btn flex w-9 shrink-0 items-center justify-center border-0 bg-transparent p-0 text-neutral-600 transition-opacity hover:opacity-70"
														data-quantity-changer="decrease"
														id="<?=$itemIds['QUANTITY_DOWN_ID']?>"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
														<svg class="pointer-events-none h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M18 12L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
														</svg>
													</button>
													<input
														class="product-cart__qty-input product-item-amount-field qty text w-9 min-w-0 shrink border-0 bg-transparent text-center text-sm font-medium tabular-nums text-neutral-800 focus:ring-0 focus:outline-none"
														id="<?=$itemIds['QUANTITY_ID']?>"
														type="number"
														name="quantity"
														value="<?=$price['MIN_QUANTITY']?>"
														min="1"
														step="1"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
													<button
														type="button"
														class="product-cart__qty-btn flex w-9 shrink-0 items-center justify-center border-0 bg-transparent p-0 text-neutral-600 transition-opacity hover:opacity-70"
														data-quantity-changer="increase"
														id="<?=$itemIds['QUANTITY_UP_ID']?>"
														aria-label="<?=htmlspecialcharsbx(Loc::getMessage('CATALOG_QUANTITY'))?>"
													>
														<svg class="pointer-events-none h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M12 6L12 18" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
															<path d="M18 12L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" />
														</svg>
													</button>
												</div>
												<span class="product-item-amount-description-container sr-only" aria-hidden="true">
													<span id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem['ITEM_MEASURE']['TITLE']?></span>
													<span id="<?=$itemIds['PRICE_TOTAL']?>"></span>
												</span>
												</div>
												</div>
												<?php
											}
										}

										break;

									case 'buttons':
										if ($productCartQtyButtonsMerged)
										{
											?>
											<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>" class="flex min-w-0 flex-1 flex-col gap-2" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
												<?php
												if ($showAddBtn)
												{
													?>
												<div class="min-w-0 flex-1">
													<a class="product-cart__submit inline-flex h-12 w-full min-w-0 flex-1 items-center justify-center gap-2 rounded-full border-0 px-4 text-sm font-semibold text-white transition-opacity hover:opacity-80 product-item-detail-buy-button" id="<?=$itemIds['ADD_BASKET_LINK']?>"
														href="javascript:void(0);"
														style="background-color: var(--color-accent);"
													>
														<?=htmlspecialcharsbx($arParams['MESS_BTN_ADD_TO_BASKET'])?>
														<svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M20 12L20.495 11.505L20.9899 12L20.495 12.495L20 12ZM5 12.7C4.6134 12.7 4.3 12.3866 4.3 12C4.3 11.6134 4.6134 11.3 5 11.3V12.7ZM14.495 5.50503L20.495 11.505L19.505 12.495L13.505 6.49497L14.495 5.50503ZM20.495 12.495L14.495 18.495L13.505 17.505L19.505 11.505L20.495 12.495ZM20 12.7H5V11.3H20V12.7Z" fill="currentColor" />
														</svg>
													</a>
												</div>
													<?php
												}

												if ($showBuyBtn)
												{
													?>
												<div class="product-item-detail-info-container min-w-0 flex-1">
													<a class="product-cart__submit inline-flex h-12 w-full min-w-0 flex-1 items-center justify-center gap-2 rounded-full border-0 px-4 text-sm font-semibold text-white transition-opacity hover:opacity-80 btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
														href="javascript:void(0);"
													>
														<span><?=htmlspecialcharsbx($arParams['MESS_BTN_BUY'])?></span>
													</a>
												</div>
													<?php
												}
												?>
											</div>
											</div>
											<?php
											if ($showSubscribe)
											{
												?>
												<div class="product-item-detail-info-container">
													<?php
													$APPLICATION->IncludeComponent(
														'bitrix:catalog.product.subscribe',
														'',
														array(
															'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
															'PRODUCT_ID' => $arResult['ID'],
															'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
															'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
															'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
															'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
														),
														$component,
														array('HIDE_ICONS' => 'Y')
													);
													?>
												</div>
												<?php
											}
											?>
											<div class="product-item-detail-info-container">
												<a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
													href="javascript:void(0)"
													rel="nofollow" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
													<?=$arParams['MESS_NOT_AVAILABLE']?>
												</a>
											</div>
										</div>
											<?php
										}
										else
										{
											?>
										<div data-entity="main-button-container" class="cart product-cart w-full min-w-0 pt-1">
											<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
												<?php
												if ($showAddBtn)
												{
													?>
												<div class="product-item-detail-info-container" style="padding:0;border:0;margin:0;">
													<a class="product-cart__submit inline-flex h-12 w-full min-w-0 items-center justify-center gap-2 rounded-full border-0 px-4 text-sm font-semibold text-white transition-opacity hover:opacity-80 product-item-detail-buy-button" id="<?=$itemIds['ADD_BASKET_LINK']?>"
														href="javascript:void(0);"
														style="background-color: var(--color-accent);"
													>
														<?=htmlspecialcharsbx($arParams['MESS_BTN_ADD_TO_BASKET'])?>
														<svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
															<path d="M20 12L20.495 11.505L20.9899 12L20.495 12.495L20 12ZM5 12.7C4.6134 12.7 4.3 12.3866 4.3 12C4.3 11.6134 4.6134 11.3 5 11.3V12.7ZM14.495 5.50503L20.495 11.505L19.505 12.495L13.505 6.49497L14.495 5.50503ZM20.495 12.495L14.495 18.495L13.505 17.505L19.505 11.505L20.495 12.495ZM20 12.7H5V11.3H20V12.7Z" fill="currentColor" />
														</svg>
													</a>
												</div>
													<?php
												}

												if ($showBuyBtn)
												{
													?>
												<div class="product-item-detail-info-container">
													<a class="product-cart__submit inline-flex h-12 w-full min-w-0 items-center justify-center gap-2 rounded-full border-0 px-4 text-sm font-semibold text-white transition-opacity hover:opacity-80 btn <?=$buyButtonClassName?> product-item-detail-buy-button" id="<?=$itemIds['BUY_LINK']?>"
														href="javascript:void(0);"
													>
														<span><?=htmlspecialcharsbx($arParams['MESS_BTN_BUY'])?></span>
													</a>
												</div>
													<?php
												}
												?>
											</div>
											<?php
											if ($showSubscribe)
											{
												?>
												<div class="product-item-detail-info-container">
													<?php
													$APPLICATION->IncludeComponent(
														'bitrix:catalog.product.subscribe',
														'',
														array(
															'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
															'PRODUCT_ID' => $arResult['ID'],
															'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
															'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
															'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
															'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
														),
														$component,
														array('HIDE_ICONS' => 'Y')
													);
													?>
												</div>
												<?php
											}
											?>
											<div class="product-item-detail-info-container">
												<a class="btn btn-link product-item-detail-buy-button" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>"
													href="javascript:void(0)"
													rel="nofollow" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
													<?=$arParams['MESS_NOT_AVAILABLE']?>
												</a>
											</div>
										</div>
											<?php
										}
										break;
								}
							}

							if ($arParams['DISPLAY_COMPARE'])
							{
								?>
								<div class="product-item-detail-compare-container">
									<div class="product-item-detail-compare">
										<div class="checkbox">
											<label id="<?=$itemIds['COMPARE_LINK']?>">
												<input type="checkbox" data-entity="compare-checkbox">
												<span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
											</label>
										</div>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					<ul class="list-none space-y-2 p-0 pt-2 mt-0 mb-5">
						<li>
							<div class="flex items-center text-xs sm:text-sm uppercase text-neutral-600 font-headings font-semibold tracking-tight">
								<img class="w-6 h-6 lg:w-8 lg:h-8 mr-2 shrink-0" width="32" height="32" src="<?=$templateFolder?>/images/product/delivery-truck.svg" alt="" loading="lazy">
								<span>Бесплатная доставка от 1 500 руб.</span>
							</div>
						</li>
						<li>
							<div class="flex items-center text-xs sm:text-sm uppercase text-neutral-600 font-headings font-semibold tracking-tight">
								<img class="w-6 h-6 lg:w-8 lg:h-8 mr-2 shrink-0" width="32" height="32" src="<?=$templateFolder?>/images/product/24-hours.svg" alt="" loading="lazy">
								<span>Отправка в течение 24 часов</span>
							</div>
						</li>
					</ul>
					<div class="prose my-2 max-w-none">
						<p class="mb-2 text-base">
							<strong>Смотрите также товары в категориях</strong>
						</p>
						<div class="flex flex-wrap gap-2">
							<a class="product-tag inline-block rounded-full border border-secondary-200 bg-secondary-100 px-2 py-1 text-sm text-neutral-800 transition-colors hover:bg-transparent" href="#">Кожа и ногти</a>
							<a class="product-tag inline-block rounded-full border border-secondary-200 bg-secondary-100 px-2 py-1 text-sm text-neutral-800 transition-colors hover:bg-transparent" href="#">Регенерация</a>
							<a class="product-tag inline-block rounded-full border border-secondary-200 bg-secondary-100 px-2 py-1 text-sm text-neutral-800 transition-colors hover:bg-transparent" href="#">Релакс</a>
							<a class="product-tag inline-block rounded-full border border-secondary-200 bg-secondary-100 px-2 py-1 text-sm text-neutral-800 transition-colors hover:bg-transparent" href="#">Время расслабиться</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<meta itemprop="name" content="<?=$name?>" />
	<meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
	<?php
	if ($haveOffers)
	{
		foreach ($arResult['JS_OFFERS'] as $offer)
		{
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE']))
			{
				foreach ($offer['TREE'] as $propName => $skuId)
				{
					$propId = (int)mb_substr($propName, 5);

					foreach ($skuProps as $prop)
					{
						if ($prop['ID'] == $propId)
						{
							foreach ($prop['VALUES'] as $propId => $propValue)
							{
								if ($propId == $skuId)
								{
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
			?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?=htmlspecialcharsbx(implode('/', $currentOffersList))?>" />
				<meta itemprop="price" content="<?=$offerPrice['RATIO_PRICE']?>" />
				<meta itemprop="priceCurrency" content="<?=$offerPrice['CURRENCY']?>" />
				<link itemprop="availability" href="http://schema.org/<?=($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
			</span>
			<?php
		}

		unset($offerPrice, $currentOffersList);
	}
	else
	{
		?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?=$price['RATIO_PRICE']?>" />
			<meta itemprop="priceCurrency" content="<?=$price['CURRENCY']?>" />
			<link itemprop="availability" href="http://schema.org/<?=($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
		</span>
		<?php
	}
	?>
</div>
<?php
if ($haveOffers)
{
	$offerIds = array();
	$offerCodes = array();

	$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

	foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer)
	{
		$offerIds[] = (int)$jsOffer['ID'];
		$offerCodes[] = $jsOffer['CODE'];

		$fullOffer = $arResult['OFFERS'][$ind];
		$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

		$strAllProps = '';
		$strMainProps = '';
		$strPriceRangesRatio = '';
		$strPriceRanges = '';

		if ($arResult['SHOW_OFFERS_PROPS'])
		{
			if (!empty($jsOffer['DISPLAY_PROPERTIES']))
			{
				foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property)
				{
					$current = '<dt>'.$property['NAME'].'</dt><dd>'.(
						is_array($property['VALUE'])
							? implode(' / ', $property['VALUE'])
							: $property['VALUE']
						).'</dd>';
					$strAllProps .= $current;

					if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']]))
					{
						$strMainProps .= $current;
					}
				}

				unset($current);
			}
		}

		if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1)
		{
			$strPriceRangesRatio = '('.Loc::getMessage(
					'CT_BCE_CATALOG_RATIO_PRICE',
					array('#RATIO#' => ($useRatio
							? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
							: '1'
						).' '.$measureName)
				).')';

			foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range)
			{
				if ($range['HASH'] !== 'ZERO-INF')
				{
					$itemPrice = false;

					foreach ($jsOffer['ITEM_PRICES'] as $itemPrice)
					{
						if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
						{
							break;
						}
					}

					if ($itemPrice)
					{
						$strPriceRanges .= '<dt>'.Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_FROM',
								array('#FROM#' => $range['SORT_FROM'].' '.$measureName)
							).' ';

						if (is_infinite($range['SORT_TO']))
						{
							$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
						}
						else
						{
							$strPriceRanges .= Loc::getMessage(
								'CT_BCE_CATALOG_RANGE_TO',
								array('#TO#' => $range['SORT_TO'].' '.$measureName)
							);
						}

						$strPriceRanges .= '</dt><dd>'.($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']).'</dd>';
					}
				}
			}

			unset($range, $itemPrice);
		}

		$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
		$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
		$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
		$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
	}

	$templateData['OFFER_IDS'] = $offerIds;
	$templateData['OFFER_CODES'] = $offerCodes;
	unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => true,
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
			'OFFER_GROUP' => $arResult['OFFER_GROUP'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null,
			'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
			'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
		),
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'VISUAL' => $itemIds,
		'DEFAULT_PICTURE' => array(
			'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
			'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
		),
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'NAME' => $arResult['~NAME'],
			'CATEGORY' => $arResult['CATEGORY_PATH'],
			'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
			'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
			'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
			'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
		),
		'BASKET' => array(
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'BASKET_URL' => $arParams['BASKET_URL'],
			'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		),
		'OFFERS' => $arResult['JS_OFFERS'],
		'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
		'TREE_PROPS' => $skuProps
	);
}
else
{
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
	{
		?>
		<div id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
			<?php
			if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
			{
				foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
				{
					?>
					<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
					<?php
					unset($arResult['PRODUCT_PROPERTIES'][$propId]);
				}
			}

			$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
			if (!$emptyProductProperties)
			{
				?>
				<table>
					<?php
					foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
					{
						?>
						<tr>
							<td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
							<td>
								<?php
								if (
									$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
									&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
								)
								{
									foreach ($propInfo['VALUES'] as $valueId => $value)
									{
										?>
										<label>
											<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
												value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'checked' : '')?>>
											<?=$value?>
										</label>
										<br>
										<?php
									}
								}
								else
								{
									?>
									<select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
										<?php
										foreach ($propInfo['VALUES'] as $valueId => $value)
										{
											?>
											<option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'selected' : '')?>>
												<?=$value?>
											</option>
											<?php
										}
										?>
									</select>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			}
			?>
		</div>
		<?php
	}

	$jsParams = array(
		'CONFIG' => array(
			'USE_CATALOG' => $arResult['CATALOG'],
			'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
			'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
			'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
			'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
			'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
			'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
			'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
			'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
			'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			'USE_STICKERS' => true,
			'USE_SUBSCRIBE' => $showSubscribe,
			'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
			'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
			'ALT' => $alt,
			'TITLE' => $title,
			'MAGNIFIER_ZOOM_PERCENT' => 200,
			'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
			'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
			'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
				? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
				: null
		),
		'VISUAL' => $itemIds,
		'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
		'PRODUCT' => array(
			'ID' => $arResult['ID'],
			'ACTIVE' => $arResult['ACTIVE'],
			'PICT' => reset($arResult['MORE_PHOTO']),
			'NAME' => $arResult['~NAME'],
			'SUBSCRIPTION' => true,
			'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
			'ITEM_PRICES' => $arResult['ITEM_PRICES'],
			'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
			'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
			'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
			'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
			'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
			'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
			'SLIDER' => $arResult['MORE_PHOTO'],
			'CAN_BUY' => $arResult['CAN_BUY'],
			'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
			'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
			'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
			'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
			'CATEGORY' => $arResult['CATEGORY_PATH']
		),
		'BASKET' => array(
			'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
			'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
			'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
			'EMPTY_PROPS' => $emptyProductProperties,
			'BASKET_URL' => $arParams['BASKET_URL'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
		)
	);
	unset($emptyProductProperties);
}

if ($arParams['DISPLAY_COMPARE'])
{
	$jsParams['COMPARE'] = array(
		'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
		'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
		'COMPARE_PATH' => $arParams['COMPARE_PATH']
	);
}

$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
	$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
;

?>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
		TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
		TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
		BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
		BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
		BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
		TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
		COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
		COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
		COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
		PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
		PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
		SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	});

	var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
	window.podexpertCatalogElement = <?=$obName?>;
</script>
<script src="<?=$this->GetFolder()?>/product_hero_gallery.js" defer></script>
<?php
unset($actualItem, $itemIds, $jsParams);
