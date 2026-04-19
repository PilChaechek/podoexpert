<?php

/**
 * Раздел «Новости» (/blog/).
 *
 * NEWS_IBLOCK_ID — ID инфоблока с новостями (Контент → Инфоблоки).
 * NEWS_IBLOCK_TYPE — символьный код типа инфоблока (как в настройках ИБ).
 * NEWS_SECTION_ID — ID раздела внутри ИБ; 0 = все элементы инфоблока (без ограничения разделом).
 * NEWS_PAGE_SIZE — элементов на страницу (пагинация компонента).
 */
return [
    'NEWS_IBLOCK_ID' => 12,
    'NEWS_IBLOCK_TYPE' => 'news_store',
    'NEWS_SECTION_ID' => 0,
    'NEWS_PAGE_SIZE' => 6,
];
