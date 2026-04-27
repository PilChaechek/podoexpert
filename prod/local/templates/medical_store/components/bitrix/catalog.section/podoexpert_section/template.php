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
?>
<div class="catalog-section">
    <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "tree", Array(
            "IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
            "IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
            "SECTION_ID"	=>	"0",
            "COUNT_ELEMENTS"	=>	"Y",
            "TOP_DEPTH"	=>	"2",
            "SECTION_URL"	=>	$arParams["SECTION_URL"],
            "CACHE_TYPE"	=>	"N",
            "CACHE_TIME"	=>	$arParams["CACHE_TIME"],
            "DISPLAY_PANEL"	=>	"N",
            "ADD_SECTIONS_CHAIN"	=>	$arParams["ADD_SECTIONS_CHAIN"],
            "SECTION_USER_FIELDS"	=>	$arParams["SECTION_USER_FIELDS"],
    ),
            $component
    );?>
</div>
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
    <div class="catalog-section__grid grid gap-4 md:gap-6 <?= $gridColsClass ?>">
        <?foreach($arResult['ITEMS'] as $arElement):?>
            <?
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

            $img = null;
            if (is_array($arElement['PREVIEW_PICTURE'])) {
                $img = $arElement['PREVIEW_PICTURE'];
            } elseif (is_array($arElement['DETAIL_PICTURE'])) {
                $img = $arElement['DETAIL_PICTURE'];
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

            $previewPlain = trim(strip_tags((string)($arElement['PREVIEW_TEXT'] ?? '')));
            $canAccessPriceCount = 0;
            foreach ($arElement['PRICES'] as $arP) {
                if (!empty($arP['CAN_ACCESS'])) {
                    $canAccessPriceCount++;
                }
            }
            ?>
            <article
                class="catalog-card group flex h-full min-w-0 flex-col"
                id="<?= $this->GetEditAreaId($arElement['ID']) ?>"
            >
                <a href="<?= $arElement['DETAIL_PAGE_URL'] ?>" class="relative mb-3 block aspect-square w-full flex-shrink-0 overflow-hidden bg-slate-50">
                    <?if($discPercent > 0):?>
                        <span class="absolute top-0 left-0 z-10 bg-red-50 px-2 py-1 text-xs font-bold text-red-600">-<?= $discPercent ?>%</span>
                    <?endif?>
                    <?if($img):?>
                        <img
                            class="h-full w-full object-contain p-2"
                            src="<?= $img['SRC'] ?>"
                            width="<?= (int)($img['WIDTH'] ?? 0) ?>"
                            height="<?= (int)($img['HEIGHT'] ?? 0) ?>"
                            alt="<?= htmlspecialchars($img['ALT'] ?: $arElement['NAME'], ENT_COMPAT, false) ?>"
                            title="<?= htmlspecialchars($img['TITLE'] ?: $arElement['NAME'], ENT_COMPAT, false) ?>"
                            loading="lazy"
                        />
                    <?endif?>
                </a>

                <h3 class="mb-1 text-base font-bold leading-snug text-slate-900">
                    <a class="text-inherit hover:text-blue-600" href="<?= $arElement['DETAIL_PAGE_URL'] ?>"><?= $arElement['NAME'] ?></a>
                </h3>

                <?if($pubDate):?>
                    <p class="mb-2 text-xs text-slate-500"><?= GetMessage('PUB_DATE') ?> <?= htmlspecialchars($pubDate, ENT_COMPAT, false) ?></p>
                <?endif?>

                <?if(!empty($arElement['DISPLAY_PROPERTIES'])):?>
                    <ul class="mb-2 list-none space-y-1 p-0 text-sm font-semibold text-slate-800">
                        <?foreach($arElement['DISPLAY_PROPERTIES'] as $arProperty):?>
                            <li>
                                <span class="text-slate-800"><?= htmlspecialchars($arProperty['NAME'] ?? '', ENT_COMPAT, false) ?>:</span>
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
                    <p class="mb-4 line-clamp-3 text-sm text-slate-500"><?= htmlspecialchars($previewPlain, ENT_COMPAT, false) ?></p>
                <?endif?>

                <div class="mt-auto space-y-3">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div class="min-w-0">
                            <?foreach($arElement['PRICES'] as $code => $arPrice):?>
                                <?if($arPrice['CAN_ACCESS']):?>
                                    <div class="text-sm text-slate-600">
                                        <?if($canAccessPriceCount > 1):?>
                                            <span class="block text-xs font-medium text-slate-500"><?= htmlspecialchars($arResult['PRICES'][$code]['TITLE'] ?? '', ENT_COMPAT, false) ?></span>
                                        <?endif?>
                                        <?if($arPrice['DISCOUNT_VALUE'] < $arPrice['VALUE']):?>
                                            <span class="text-slate-400 line-through"><?= $arPrice['PRINT_VALUE'] ?></span>
                                            <span class="catalog-price ml-1 text-lg font-bold text-red-600"><?= $arPrice['PRINT_DISCOUNT_VALUE'] ?></span>
                                        <?else:?>
                                            <span class="catalog-price text-lg font-bold text-slate-900"><?= $arPrice['PRINT_VALUE'] ?></span>
                                        <?endif;?>
                                    </div>
                                <?endif;?>
                            <?endforeach;?>
                        </div>

                        <div class="flex shrink-0 flex-wrap items-center justify-start gap-2 sm:justify-end">
                            <?if($arElement['CAN_BUY'] && !($arParams['USE_PRODUCT_QUANTITY'] || count($arElement['PRODUCT_PROPERTIES']))):?>
                                <noindex>
                                    <a
                                        class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-800 transition hover:bg-slate-50"
                                        href="<?=$arElement['BUY_URL']?>"
                                        rel="nofollow"
                                    ><?= GetMessage('CATALOG_BUY') ?> <span aria-hidden="true" class="text-base leading-none">&rarr;</span></a>
                                    <a
                                        class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                                        href="<?=$arElement['ADD_URL']?>"
                                        rel="nofollow"
                                    ><?= GetMessage('CATALOG_ADD') ?></a>
                                </noindex>
                            <?endif?>
                        </div>
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

                    <?if($arElement['CAN_BUY'] && ($arParams['USE_PRODUCT_QUANTITY'] || count($arElement['PRODUCT_PROPERTIES']))):?>
                        <form class="space-y-2" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
                            <?if($arParams['USE_PRODUCT_QUANTITY']):?>
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    <label for="q_<?= (int)$arElement['ID'] ?>"><?= GetMessage('CT_BCS_QUANTITY') ?>:</label>
                                    <input
                                        class="w-20 rounded border border-slate-300 px-2 py-1"
                                        id="q_<?= (int)$arElement['ID'] ?>"
                                        type="text"
                                        name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>"
                                        value="1"
                                        size="5"
                                    >
                                </div>
                            <?endif;?>
                            <?foreach($arElement['PRODUCT_PROPERTIES'] as $pid => $product_property):?>
                                <div class="text-sm">
                                    <span class="block font-medium text-slate-700"><?= $arElement['PROPERTIES'][$pid]['NAME'] ?>:</span>
                                    <?if(
                                        $arElement['PROPERTIES'][$pid]['PROPERTY_TYPE'] == 'L'
                                        && $arElement['PROPERTIES'][$pid]['LIST_TYPE'] == 'C'
                                    ):?>
                                        <div class="mt-1 flex flex-wrap gap-2">
                                            <?foreach($product_property['VALUES'] as $k => $v):?>
                                                <label class="inline-flex items-center gap-1"><input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$pid?>]" value="<?=$k?>"<?=($k == $product_property['SELECTED'] ? ' checked' : '')?>><?= $v ?></label>
                                            <?endforeach;?>
                                        </div>
                                    <?else:?>
                                        <select class="mt-1 w-full max-w-xs rounded border border-slate-300 px-2 py-1" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$pid?>]">
                                            <?foreach($product_property['VALUES'] as $k => $v):?>
                                                <option value="<?=$k?>"<?=($k == $product_property['SELECTED'] ? ' selected' : '')?>><?= $v ?></option>
                                            <?endforeach;?>
                                        </select>
                                    <?endif;?>
                                </div>
                            <?endforeach;?>
                            <input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="BUY">
                            <input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" value="<?=$arElement['ID']?>">
                            <div class="flex flex-wrap gap-2">
                                <input class="cursor-pointer rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50" type="submit" name="<?=$arParams['ACTION_VARIABLE'].'BUY'?>" value="<?= GetMessage('CATALOG_BUY') ?>">
                                <input class="cursor-pointer rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50" type="submit" name="<?=$arParams['ACTION_VARIABLE'].'ADD2BASKET'?>" value="<?= GetMessage('CATALOG_ADD') ?>">
                            </div>
                        </form>
                    <?elseif((count($arResult['PRICES']) > 0) || is_array($arElement['PRICE_MATRIX'])):?>
                        <?if(!$arElement['CAN_BUY']):?>
                            <p class="text-sm text-slate-600"><?= GetMessage('CATALOG_NOT_AVAILABLE') ?></p>
                            <?$APPLICATION->IncludeComponent('bitrix:sale.notice.product', '.default', array(
                                'NOTIFY_ID' => $arElement['ID'],
                                'NOTIFY_URL' => htmlspecialcharsback($arElement['SUBSCRIBE_URL']),
                                'NOTIFY_USE_CAPTHA' => 'N',
                            ),
                                $component
                            );?>
                        <?endif?>
                    <?endif?>
                </div>
            </article>
        <?endforeach;?>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <br /><?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>
