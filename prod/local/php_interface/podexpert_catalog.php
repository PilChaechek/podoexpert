<?php

/**
 * Настройки витрины каталога (подставьте значения с боевого сайта из админки).
 *
 * CATALOG_IBLOCK_ID — ID инфоблока товаров (Контент → Инфоблоки → ваш каталог).
 * IBLOCK_TYPE — символьный код типа инфоблока (часто catalog, offers и т.д.).
 * Главная полоса навигации (рядом с «Каталог») — тип меню main, файл /.main.menu.php.
 */
return [
    // Обязательно: ID инфоблока каталога (Контент → Инфоблоки). Пока 0 — витрина покажет подсказку.
    'CATALOG_IBLOCK_ID' => 11,
    // ЧПУ витрины (как SEF_FOLDER у bitrix:catalog). Для запасной ссылки, если DETAIL_PAGE_URL пустой.
    'CATALOG_SEF_FOLDER' => '/catalog/',
    // Символьный код типа инфоблока — как в настройках ИБ (например catalog, 1c_catalog).
    'IBLOCK_TYPE' => 'catalog',
    // Коды типов цен в модуле «Торговый каталог» — по умолчанию BASE; при другом имени замените и в catalog/index.php.
    'PRICE_CODE' => ['BASE'],
    // Глубина дерева разделов для bitrix:menu.sections (тип меню catalog, файл /.catalog.menu.php).
    'CATALOG_MENU_MAX_DEPTH' => 4,
];
