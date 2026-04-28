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
$this->setFrameMode(true);
$this->IncludeLangFile();

$basketUrlForJs = (string)($arParams['BASKET_URL'] ?? '/cart/');
?>

<div class="catalog-section">
    <?if($arParams["DISPLAY_TOP_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?><br />
    <?endif;?>
    <?
    $lineCount = (int)($arParams['LINE_ELEMENT_COUNT'] ?? 3);
    if ($lineCount < 1) {
        $lineCount = 3;
    }
    if ($lineCount > 4) {
        $lineCount = 4;
    }
    $gridColsClass = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    ][$lineCount] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
    ?>
    <div class="catalog-section__grid grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3 xl:gap-2 <?= $gridColsClass ?>">
        <?foreach($arResult['ITEMS'] as $arElement):?>
            <?
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

            $img = null;
            $galleryProp = $arElement['PROPERTIES']['GALLERY'] ?? null;
            $galleryFirstId = 0;
            if (is_array($galleryProp) && !empty($galleryProp['VALUE'])) {
                $gVal = $galleryProp['VALUE'];
                if (is_array($gVal)) {
                    foreach ($gVal as $fid) {
                        $fid = (int) $fid;
                        if ($fid > 0) {
                            $galleryFirstId = $fid;
                            break;
                        }
                    }
                } else {
                    $galleryFirstId = (int) $gVal;
                }
            }
            if ($galleryFirstId > 0) {
                $img = CFile::GetFileArray($galleryFirstId);
                if (!is_array($img) || empty($img['SRC'])) {
                    $img = null;
                }
            }
            if ($img === null && !empty($arElement['MORE_PHOTO']) && is_array($arElement['MORE_PHOTO'])) {
                $firstMore = reset($arElement['MORE_PHOTO']);
                if (is_array($firstMore) && !empty($firstMore['SRC'])) {
                    $img = $firstMore;
                }
            }
            if ($img === null) {
                if (is_array($arElement['PREVIEW_PICTURE'])) {
                    $img = $arElement['PREVIEW_PICTURE'];
                } elseif (is_array($arElement['DETAIL_PICTURE'])) {
                    $img = $arElement['DETAIL_PICTURE'];
                }
            }

            $discPercent = 0;
            foreach ($arElement['PRICES'] as $arPrice) {
                if ($arPrice['CAN_ACCESS'] && (float)$arPrice['VALUE'] > 0 && (float)$arPrice['DISCOUNT_VALUE'] < (float)$arPrice['VALUE']) {
                    $discPercent = (int)round(100 * (1 - (float)$arPrice['DISCOUNT_VALUE'] / (float)$arPrice['VALUE']));
                    break;
                }
            }

            $pubDate = '';
            if (!empty($arElement['ACTIVE_FROM'])) {
                $pubDate = FormatDate($GLOBALS['DB']->DateFormatToPhp(CSite::GetDateFormat('FULL')), MakeTimeStamp($arElement['ACTIVE_FROM']));
            } elseif (!empty($arElement['DATE_CREATE'])) {
                $pubDate = FormatDate($GLOBALS['DB']->DateFormatToPhp(CSite::GetDateFormat('FULL')), MakeTimeStamp($arElement['DATE_CREATE']));
            }

            $previewPlain = trim(html_entity_decode(strip_tags((string)($arElement['PREVIEW_TEXT'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $canAccessPriceCount = 0;
            foreach ($arElement['PRICES'] as $arP) {
                if (!empty($arP['CAN_ACCESS'])) {
                    $canAccessPriceCount++;
                }
            }
            ?>
            <div
                class="product-card relative h-full flex flex-col"
                id="<?= $this->GetEditAreaId($arElement['ID']) ?>"
            >
                <?if($discPercent > 0):?>
                    <p class="product-card__badge">-<?= $discPercent ?>%</p>
                <?endif?>
                <div class="product-card__media relative">
                    <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="link block">
                        <?if($img):?>
                            <img
                                class="product-card__image"
                                src="<?= $img['SRC'] ?>"
                                width="<?= (int)($img['WIDTH'] ?? 0) ?>"
                                height="<?= (int)($img['HEIGHT'] ?? 0) ?>"
                                alt="<?= htmlspecialchars($img['ALT'] ?: $arElement['NAME'], ENT_COMPAT, false) ?>"
                                title="<?= htmlspecialchars($img['TITLE'] ?: $arElement['NAME'], ENT_COMPAT, false) ?>"
                                loading="lazy"
                            />
                        <?endif?>
                    </a>
                </div>

                <div class="product-card__content">
                    <h3 class="product-card__title">
                        <a class="product-card__title-link" href="<?= $arElement['DETAIL_PAGE_URL'] ?>"><?= $arElement['NAME'] ?></a>
                    </h3>
                    
                    <?if(!empty($arElement['DISPLAY_PROPERTIES'])):?>
                    <ul class="product-card__props">
                        <?foreach($arElement['DISPLAY_PROPERTIES'] as $arProperty):?>
                            <li>
                                <span class="product-card__prop-name"><?= htmlspecialchars($arProperty['NAME'] ?? '', ENT_COMPAT, false) ?>:</span>
                                <?php
                                if (is_array($arProperty['DISPLAY_VALUE'])) {
                                    echo ' ' . implode(' / ', $arProperty['DISPLAY_VALUE']);
                                } else {
                                    echo ' ' . $arProperty['DISPLAY_VALUE'];
                                }
                                ?>
                            </li>
                        <?endforeach?>
                    </ul>
                <?endif?>

                <?if($previewPlain !== ''):?>
                    <p class="product-card__description"><?= htmlspecialchars($previewPlain, ENT_COMPAT, false) ?></p>
                <?endif?>

                    <div class="product-card__footer">
                        <div class="product-card__prices">
                            <?foreach($arElement['PRICES'] as $code => $arPrice):?>
                                <?if($arPrice['CAN_ACCESS']):?>
                                    <div class="product-card__price-row">
                                        <?if($canAccessPriceCount > 1):?>
                                            <span class="product-card__price-label"><?= htmlspecialchars($arResult['PRICES'][$code]['TITLE'] ?? '', ENT_COMPAT, false) ?></span>
                                        <?endif?>
                                        <p class="product-card__price">
                                            <?if($arPrice['DISCOUNT_VALUE'] < $arPrice['VALUE']):?>
                                                <span class="product-card__price-old"><?= $arPrice['PRINT_VALUE'] ?></span>
                                                <span class="product-card__price-current catalog-price"><?= $arPrice['PRINT_DISCOUNT_VALUE'] ?></span>
                                            <?else:?>
                                                <span class="product-card__price-current catalog-price"><?= $arPrice['PRINT_VALUE'] ?></span>
                                            <?endif;?>
                                        </p>
                                    </div>
                                <?endif;?>
                            <?endforeach;?>
                        </div>

                        <?if ($arElement['CAN_BUY']):?>
                            <?php
                            $buyBtnMess = trim((string) ($arParams['~MESS_BTN_BUY'] ?? '')) !== ''
                                ? $arParams['~MESS_BTN_BUY']
                                : (trim((string) ($arParams['~MESS_BTN_ADD_TO_BASKET'] ?? '')) !== '' ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : GetMessage('CATALOG_BUY'));
                            $cardImgSrc = (is_array($img) && !empty($img['SRC'])) ? $img['SRC'] : '';
                            $addUrlAjax = isset($arElement['~ADD_URL']) && $arElement['~ADD_URL'] !== ''
                                ? $arElement['~ADD_URL']
                                : ($arElement['ADD_URL'] ?? '');
                            ?>
                            <noindex>
                                <div class="product-card__actions mt-3 w-full min-w-0">
                                    <div class="product-card__basket-fields hidden" aria-hidden="true">
                                        <?php if (($arParams['USE_PRODUCT_QUANTITY'] ?? '') === 'Y'): ?>
                                            <input type="hidden" name="<?= htmlspecialcharsbx((string) $arParams['PRODUCT_QUANTITY_VARIABLE']) ?>" value="1">
                                        <?php endif ?>
                                        <?php foreach ($arElement['PRODUCT_PROPERTIES'] as $pid => $product_property): ?>
                                            <input type="hidden" name="<?= htmlspecialcharsbx((string) $arParams['PRODUCT_PROPS_VARIABLE']) ?>[<?= htmlspecialcharsbx((string) $pid) ?>]" value="<?= htmlspecialcharsbx((string) ($product_property['SELECTED'] ?? '')) ?>">
                                        <?php endforeach ?>
                                    </div>
                                    <button type="button" class="product-cart__submit product-card__buy-ajax btn btn-outline"
                                        data-add-url="<?= htmlspecialcharsbx((string) $addUrlAjax) ?>"
                                        data-image="<?= htmlspecialcharsbx((string) $cardImgSrc) ?>"
                                        data-product-name="<?= htmlspecialcharsbx((string) $arElement['NAME']) ?>"
                                    >
                                        <?= htmlspecialcharsbx((string) $buyBtnMess) ?>
                                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 12L20.495 11.505L20.9899 12L20.495 12.495L20 12ZM5 12.7C4.6134 12.7 4.3 12.3866 4.3 12C4.3 11.6134 4.6134 11.3 5 11.3V12.7ZM14.495 5.50503L20.495 11.505L19.505 12.495L13.505 6.49497L14.495 5.50503ZM20.495 12.495L14.495 18.495L13.505 17.505L19.505 11.505L20.495 12.495ZM20 12.7H5V11.3H20V12.7Z" fill="currentColor"/></svg>
                                    </button>
                                </div>
                            </noindex>
                        <?endif?>
                    </div>

                    <?if(is_array($arElement['PRICE_MATRIX']) && $arElement['PRICE_MATRIX']):?>
                        <div class="overflow-x-auto text-xs text-slate-600">
                            <table class="min-w-full border-collapse text-left" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <thead>
                                <tr>
                                    <?if(count($arElement['PRICE_MATRIX']['ROWS']) >= 1 && ($arElement['PRICE_MATRIX']['ROWS'][0]['QUANTITY_FROM'] > 0 || $arElement['PRICE_MATRIX']['ROWS'][0]['QUANTITY_TO'] > 0)):?>
                                        <th class="whitespace-nowrap border border-slate-200 px-1 py-0.5 font-medium"><?= GetMessage('CATALOG_QUANTITY') ?></th>
                                    <?endif?>
                                    <?foreach($arElement['PRICE_MATRIX']['COLS'] as $typeID => $arType):?>
                                        <th class="whitespace-nowrap border border-slate-200 px-1 py-0.5 font-medium"><?= $arType['NAME_LANG'] ?></th>
                                    <?endforeach?>
                                </tr>
                                </thead>
                                <?foreach ($arElement['PRICE_MATRIX']['ROWS'] as $ind => $arQuantity):?>
                                    <tr>
                                        <?if(count($arElement['PRICE_MATRIX']['ROWS']) > 1 || (count($arElement['PRICE_MATRIX']['ROWS']) == 1 && ($arElement['PRICE_MATRIX']['ROWS'][0]['QUANTITY_FROM'] > 0 || $arElement['PRICE_MATRIX']['ROWS'][0]['QUANTITY_TO'] > 0))):?>
                                            <th class="whitespace-nowrap border border-slate-200 px-1 py-0.5 font-normal"><?php
                                            if (intval($arQuantity['QUANTITY_FROM']) > 0 && intval($arQuantity['QUANTITY_TO']) > 0) {
                                                echo str_replace('#FROM#', $arQuantity['QUANTITY_FROM'], str_replace('#TO#', $arQuantity['QUANTITY_TO'], GetMessage('CATALOG_QUANTITY_FROM_TO')));
                                            } elseif (intval($arQuantity['QUANTITY_FROM']) > 0) {
                                                echo str_replace('#FROM#', $arQuantity['QUANTITY_FROM'], GetMessage('CATALOG_QUANTITY_FROM'));
                                            } elseif (intval($arQuantity['QUANTITY_TO']) > 0) {
                                                echo str_replace('#TO#', $arQuantity['QUANTITY_TO'], GetMessage('CATALOG_QUANTITY_TO'));
                                            }
                                            ?></th>
                                        <?endif?>
                                        <?foreach($arElement['PRICE_MATRIX']['COLS'] as $typeID => $arType):?>
                                            <td class="whitespace-nowrap border border-slate-200 px-1 py-0.5"><?php
                                            if($arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['DISCOUNT_PRICE'] < $arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['PRICE']):?>
                                                <s class="text-slate-400"><?=FormatCurrency($arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['PRICE'], $arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['CURRENCY'])?></s>
                                                <span class="catalog-price"><?=FormatCurrency($arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['DISCOUNT_PRICE'], $arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['CURRENCY'])?></span>
                                            <?else:?>
                                                <span class="catalog-price"><?=FormatCurrency($arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['PRICE'], $arElement['PRICE_MATRIX']['MATRIX'][$typeID][$ind]['CURRENCY'])?></span>
                                            <?endif?>&nbsp;
                                            </td>
                                        <?endforeach?>
                                    </tr>
                                <?endforeach?>
                            </table>
                        </div>
                    <?endif?>

                    <?if($arParams['DISPLAY_COMPARE']):?>
                        <noindex>
                            <a class="text-sm text-slate-500 underline hover:text-slate-800" href="<?echo $arElement['COMPARE_URL']?>" rel="nofollow"><?echo GetMessage('CATALOG_COMPARE')?></a>
                        </noindex>
                    <?endif?>

                    <?if(!$arElement['CAN_BUY'] && ((count($arResult['PRICES']) > 0) || is_array($arElement['PRICE_MATRIX']))):?>
                            <p class="text-sm text-slate-600"><?= GetMessage('CATALOG_NOT_AVAILABLE') ?></p>
                            <?$APPLICATION->IncludeComponent('bitrix:sale.notice.product', '.default', array(
                                'NOTIFY_ID' => $arElement['ID'],
                                'NOTIFY_URL' => htmlspecialcharsback($arElement['SUBSCRIBE_URL']),
                                'NOTIFY_USE_CAPTHA' => 'N',
                            ),
                                $component
                            );?>
                    <?endif?>
                </div>
            </div>
        <?endforeach;?>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <br /><?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>
<script>
(function(){
	if (typeof BX === 'undefined') return;
	BX.ready(function(){
		BX.message({
			BASKET_UNKNOWN_ERROR: <?= \CUtil::PhpToJSObject(GetMessage('PODEXPERT_SECTION_BASKET_ERR'), false, true) ?>,
			TITLE_SUCCESSFUL: <?= \CUtil::PhpToJSObject(GetMessage('PODEXPERT_SECTION_BASKET_OK'), false, true) ?>,
			BTN_MESSAGE_DETAIL_BASKET_REDIRECT: <?= \CUtil::PhpToJSObject(GetMessage('PODEXPERT_SECTION_BASKET_LINK'), false, true) ?>,
			BASKET_URL: <?= \CUtil::PhpToJSObject($basketUrlForJs, false, true) ?>
		});
		if (window.podexpertCatalogSectionBasketInit) return;
		window.podexpertCatalogSectionBasketInit = true;
		BX.bind(document.body, 'click', function(e){
			var t = e.target;
			var btn = t.closest ? t.closest('.product-card__buy-ajax') : null;
			if (!btn && t.parentNode) btn = BX.findParent(t, { className: 'product-card__buy-ajax' });
			if (!btn) return;
			e.preventDefault();
			var url = btn.getAttribute('data-add-url');
			if (!url) return;
			var card = btn.closest ? btn.closest('.product-card') : BX.findParent(btn, { className: 'product-card' });
			var fields = card ? card.querySelector('.product-card__basket-fields') : null;
			var data = { ajax_basket: 'Y' };
			if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
				data.sessid = BX.bitrix_sessid();
			}
			if (fields) {
				var inputs = fields.querySelectorAll('input[name]');
				for (var i = 0; i < inputs.length; i++) {
					data[inputs[i].name] = inputs[i].value;
				}
			}
			btn.disabled = true;
			BX.ajax({
				method: 'POST',
				dataType: 'json',
				url: url,
				data: data,
				onsuccess: function(result){
					btn.disabled = false;
					if (!BX.type.isPlainObject(result)) return;
					if (result.STATUS !== 'OK') {
						if (window.podexpertToastifySimple) {
							window.podexpertToastifySimple(
								result.MESSAGE ? result.MESSAGE : BX.message('BASKET_UNKNOWN_ERROR'),
								'error'
							);
						}
						return;
					}
					BX.onCustomEvent('OnBasketChange');
					if (window.podexpertToastifyCart) {
						window.podexpertToastifyCart({
							imageSrc: btn.getAttribute('data-image') || '',
							title: BX.message('TITLE_SUCCESSFUL'),
							productName: btn.getAttribute('data-product-name') || '',
							basketUrl: BX.message('BASKET_URL'),
							cartLinkText: BX.message('BTN_MESSAGE_DETAIL_BASKET_REDIRECT')
						});
					}
				},
				onfailure: function(){
					btn.disabled = false;
					if (window.podexpertToastifySimple) {
						window.podexpertToastifySimple(BX.message('BASKET_UNKNOWN_ERROR'), 'error');
					}
				}
			});
		});
	});
})();
</script>
