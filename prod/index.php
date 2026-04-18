<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Интернет Магазин');

$IBLOCK_ID = 11;
$SECTION_ID = 2;
require $_SERVER['DOCUMENT_ROOT'] . '/include/home/products_slider.php';

// Второй слайдер: укажите ID инфоблока и раздела
$IBLOCK_ID = 11;
$SECTION_ID = 3;
require $_SERVER['DOCUMENT_ROOT'] . '/include/home/products_slider.php';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
