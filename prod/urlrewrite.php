<?php
$arUrlRewrite=array (
  1 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/info/([^/]+)/#',
    'RULE' => 'CODE=$1',
    'ID' => '',
    'PATH' => '/info/detail.php',
    'SORT' => 50,
  ),
  6 =>
  array (
    'CONDITION' => '#^/katalog/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/katalog/index.php',
    'SORT' => 100,
  ),
  7 =>
  array (
    'CONDITION' => '#^/personal/cart/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/personal/cart/index.php',
    'SORT' => 100,
  ),
  8 =>
  array (
    'CONDITION' => '#^/personal/order/make/payment.php#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/personal/order/make/payment.php',
    'SORT' => 90,
  ),
  9 =>
  array (
    'CONDITION' => '#^/personal/order/make/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/personal/order/make/index.php',
    'SORT' => 100,
  ),
);
