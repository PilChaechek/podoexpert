<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Корзина');

$podexpertOrderIdParam = trim((string) ($_REQUEST['ORDER_ID'] ?? ''));
if ($podexpertOrderIdParam !== '') {
    $APPLICATION->SetTitle('Заказ оформлен');
}

?>
<div class="container">
<?php
$podexpertCartHasItems = function_exists('podexpert_basket_total_quantity')
    && podexpert_basket_total_quantity() > 0;
$podexpertShowOrderAjax = $podexpertCartHasItems || $podexpertOrderIdParam !== '';

if ($podexpertOrderIdParam === '') {
    $APPLICATION->IncludeComponent(
        'bitrix:sale.basket.basket',
        '.default',
        [
            'PATH_TO_ORDER' => '/cart/#checkout',
            'SET_TITLE' => 'N',
            'HIDE_COUPON' => 'N',
            'AUTO_CALCULATION' => 'Y',
            'ACTION_VARIABLE' => 'basketAction',
        ],
        false
    );
}

if ($podexpertShowOrderAjax) {
    ?>
<div id="checkout">
<?php
    $APPLICATION->IncludeComponent(
        'bitrix:sale.order.ajax',
        '.default',
        [
            'ALLOW_AUTO_REGISTER' => 'Y',
            'ALLOW_APPEND_ORDER' => 'Y',
            'SEND_NEW_USER_NOTIFY' => 'Y',
            'PATH_TO_BASKET' => '/cart/',
            'PATH_TO_PERSONAL' => '/personal/',
            'PATH_TO_PAYMENT' => '/personal/order/make/payment.php',
            'PATH_TO_AUTH' => '/auth/',
            'PAY_FROM_ACCOUNT' => 'N',
            'SHOW_MENU' => 'N',
            // Иначе при пустой корзине редирект на PATH_TO_BASKET (/cart/) — бесконечный цикл для гостя.
            'DISABLE_BASKET_REDIRECT' => 'Y',
            'EMPTY_BASKET_HINT_PATH' => '/catalog/',
            'USE_PHONE_NORMALIZATION' => 'Y',
            'DELIVERY_NO_AJAX' => 'N',
            'SHOW_NOT_CALCULATED_DELIVERIES' => 'L',
            'SPOT_LOCATION_BY_GEOIP' => 'N',
            'DELIVERY_TO_PAYSYSTEM' => 'N',
            'SHOW_VAT_PRICE' => 'Y',
            'USE_PREPAYMENT' => 'N',
            'SET_TITLE' => 'N',
            'COMPATIBLE_MODE' => 'N',
            'USE_PRELOAD' => 'Y',
            'ACTION_VARIABLE' => 'soa-action',
            'USER_CONSENT' => 'N',
            'USER_CONSENT_ID' => '0',
            'USER_CONSENT_IS_CHECKED' => 'Y',
            'USER_CONSENT_IS_LOADED' => 'N',
        ],
        false
    );
    ?>
</div>
    <?php
}
?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
