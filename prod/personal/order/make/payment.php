<?php

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Оплата заказа');

?>
<div class="container">
<?php
$APPLICATION->IncludeComponent(
    'bitrix:sale.order.payment',
    '',
    [],
    false
);
?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
