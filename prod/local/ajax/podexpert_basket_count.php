<?php

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

header('Content-Type: application/json; charset=UTF-8');

if (!\Bitrix\Main\Loader::includeModule('sale')) {
    echo json_encode(['count' => 0, 'countBadge' => '0'], JSON_UNESCAPED_UNICODE);
    die();
}

$count = function_exists('podexpert_basket_total_quantity') ? podexpert_basket_total_quantity() : 0;
echo json_encode(
    [
        'count' => $count,
        'countBadge' => $count > 99 ? '99+' : (string) $count,
    ],
    JSON_UNESCAPED_UNICODE
);
