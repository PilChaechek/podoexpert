<?php

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

header('Content-Type: application/json; charset=UTF-8');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

if (!check_bitrix_sessid()) {
    echo json_encode(['ok' => false, 'error' => 'sessid'], JSON_UNESCAPED_UNICODE);
    die();
}

$request = Application::getInstance()->getContext()->getRequest();
$productId = (int) $request->getPost('productId');

if ($productId <= 0) {
    echo json_encode(['ok' => false, 'error' => 'product'], JSON_UNESCAPED_UNICODE);
    die();
}

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog')) {
    echo json_encode(['ok' => false, 'error' => 'module'], JSON_UNESCAPED_UNICODE);
    die();
}

$result = \Bitrix\Catalog\Product\Basket::addProduct([
    'PRODUCT_ID' => $productId,
    'QUANTITY' => 1,
]);

if (!$result->isSuccess()) {
    echo json_encode(
        ['ok' => false, 'error' => 'add', 'messages' => $result->getErrorMessages()],
        JSON_UNESCAPED_UNICODE
    );
    die();
}

$count = function_exists('podexpert_basket_total_quantity') ? podexpert_basket_total_quantity() : 0;
echo json_encode(
    [
        'ok' => true,
        'count' => $count,
        'countBadge' => $count > 99 ? '99+' : (string) $count,
    ],
    JSON_UNESCAPED_UNICODE
);
