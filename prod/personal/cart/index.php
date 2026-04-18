<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Корзина');

?>
<div class="container">
<?php
$APPLICATION->IncludeComponent(
    'bitrix:sale.basket.basket',
    '.default',
    [
        'PATH_TO_ORDER' => '/personal/order/make/',
        'SET_TITLE' => 'Y',
        'HIDE_COUPON' => 'N',
        'AUTO_CALCULATION' => 'Y',
        'ACTION_VARIABLE' => 'basketAction',
    ],
    false
);
?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
