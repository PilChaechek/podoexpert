<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;

if (!function_exists('podexpert_medical_store_site_contacts')) {
    /** Данные для подвала: первый активный элемент инфоблока 13. */
    function podexpert_medical_store_site_contacts(): array
    {
        $base = array_fill_keys(
            ['phone', 'phone2', 'email', 'address', 'mode', 'max', 'telegram', 'vk', 'yandex_map_link', 'logo_text', 'text_after_logo', 'copyright'],
            ''
        );

        if (!Loader::includeModule('iblock')) {
            return podexpert_site_contacts_pack($base);
        }

        $cache = Cache::createInstance();
        $cacheId = 'site_contacts_v15';
        $cacheDir = '/podexpert/site_contacts/';
        if ($cache->initCache(3600, $cacheId, $cacheDir)) {
            $vars = $cache->getVars();

            return is_array($vars) ? $vars : podexpert_site_contacts_pack($base);
        }
        if (!$cache->startDataCache()) {
            return podexpert_site_contacts_pack($base);
        }

        $rs = CIBlockElement::GetList(
            ['ID' => 'ASC'],
            ['IBLOCK_ID' => 13, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N'],
            false,
            ['nTopCount' => 1],
            ['ID']
        );
        if ($ob = $rs->GetNextElement()) {
            $elId = (int) $ob->GetFields()['ID'];
            $map = [
                'PHONE' => 'phone',
                'PHONE2' => 'phone2',
                'EMAIL' => 'email',
                'ADDRESS' => 'address',
                'MODE' => 'mode',
                'MAX' => 'max',
                'TELEGRAM' => 'telegram',
                'VKONTAKTE' => 'vk',
                'YANDEX_MAP_LINK' => 'yandex_map_link',
                'LOGO_TEXT' => 'logo_text',
                'TEXT_AFTER_LOGO' => 'text_after_logo',
                'COPYRIGHT' => 'copyright',
            ];
            $db = CIBlockElement::GetProperty(13, $elId, ['sort' => 'asc'], []);
            while ($row = $db->Fetch()) {
                $code = (string) ($row['CODE'] ?? '');
                if ($code === '' || !isset($map[$code])) {
                    continue;
                }
                $v = $row['~VALUE'] ?? $row['VALUE'] ?? '';
                if (is_array($v)) {
                    $v = (string) ($v['TEXT'] ?? '');
                } else {
                    $v = (string) ($v ?? '');
                }
                $v = trim($v);
                if ($v !== '') {
                    $base[$map[$code]] = $v;
                }
            }
        }

        $out = podexpert_site_contacts_pack($base);
        $cache->endDataCache($out);

        return $out;
    }
}

if (!function_exists('podexpert_site_contacts_pack')) {
    function podexpert_site_contacts_pack(array $b): array
    {
        $phones = [];
        $p1 = trim((string) ($b['phone'] ?? ''));
        if ($p1 !== '') {
            $phones[] = ['href' => podexpert_phone_tel_href($p1), 'label' => $p1];
        }
        $p2 = trim((string) ($b['phone2'] ?? ''));
        if ($p2 !== '') {
            $phones[] = ['href' => podexpert_phone_tel_href($p2), 'label' => $p2];
        }

        $email = trim((string) ($b['email'] ?? ''));

        return [
            'phones' => $phones,
            'email' => $email,
            'email_href' => $email !== '' ? 'mailto:' . $email : '',
            'address' => trim((string) ($b['address'] ?? '')),
            'maps_href' => trim((string) ($b['yandex_map_link'] ?? '')),
            'logo_text' => trim((string) ($b['logo_text'] ?? '')),
            'text_after_logo' => trim((string) ($b['text_after_logo'] ?? '')),
            'mode' => trim((string) ($b['mode'] ?? '')),
            'max' => trim((string) ($b['max'] ?? '')),
            'telegram' => trim((string) ($b['telegram'] ?? '')),
            'vk' => trim((string) ($b['vk'] ?? '')),
            'copyright' => trim((string) ($b['copyright'] ?? '')),
        ];
    }
}

if (!function_exists('podexpert_phone_tel_href')) {
    function podexpert_phone_tel_href(string $display): string
    {
        $digits = preg_replace('/\D+/u', '', $display);
        if ($digits === '') {
            return '';
        }
        $n = strlen($digits);
        if ($n === 10) {
            return 'tel:+7' . $digits;
        }
        if ($n === 11 && $digits[0] === '8') {
            return 'tel:+7' . substr($digits, 1);
        }

        return 'tel:+' . $digits;
    }
}
