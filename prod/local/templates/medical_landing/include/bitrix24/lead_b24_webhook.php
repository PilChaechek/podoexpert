<?php

/**
 * Входящий вебхук Bitrix24 (в правах вебхука должен быть включён CRM).
 *
 * Укажите в incoming_webhook_base полный URL вебхука со слэшем в конце, как в поле «URL»
 * после создания вебхука (без имени метода в конце — метод crm.lead.add подставит код).
 *
 * Пример формата:
 *   https://podoexpert.bitrix24.ru/rest/1572/xxxxxxxxxxxxxxxx/
 *
 * Пока значение пустое — запросы в B24 не уходят (почта и ответ формы не меняются).
 */
return [
    'incoming_webhook_base' => 'https://podoexpert.bitrix24.ru/rest/1572/gs7wd0pcyxaz7wlt/',
];
