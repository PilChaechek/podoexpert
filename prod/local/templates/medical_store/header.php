<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/styles.css');

$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$logoPath = __DIR__ . '/images/logo.svg';
$logoSvg = '';
if (is_file($logoPath)) {
    $logoSvg = (string) file_get_contents($logoPath);
    $logoSvg = preg_replace('/^<\?xml[^>]*>\s*/', '', $logoSvg);
}

$topNavItems = [
    ['href' => '#', 'text' => 'О компании'],
    ['href' => '#', 'text' => 'Акции'],
    ['href' => '#', 'text' => 'Мастерам'],
    ['href' => '#', 'text' => 'Доставка и оплата'],
    ['href' => '#', 'text' => 'Статьи'],
    ['href' => '/contacts/', 'text' => 'Контакты'],
];

$catalogMenuItems = [
    ['href' => '/katalog/', 'text' => 'Все'],
    ['href' => '/professionalnaya-kosmetika-arkada/', 'text' => 'Косметика Arkada'],
    ['href' => '/instrumenty-arkada/', 'text' => 'Инструменты Arkada'],
    ['href' => '/lechebnaya-kosmetika/', 'text' => 'Косметика SUDA'],
    ['href' => '/domashnij-ukhod/', 'text' => 'Домашний уход и профилактика'],
    ['href' => '/instrumenty/', 'text' => 'Инструменты'],
    ['href' => '/fiksiruyushchie-materialy/', 'text' => 'Фиксирующие материалы'],
    ['href' => '/brace/', 'text' => "Arkada's Brace-M"],
    ['href' => '/arkadascube/', 'text' => "Arkada's Cube"],
    ['href' => '/nail-insert-system/', 'text' => 'Nail Insert System'],
    ['href' => '/prof-oborudovanie/', 'text' => 'Профессиональное оборудование'],
];

$pc = $GLOBALS['PODEXPERT_CATALOG'] ?? [];
$mainNavItems = [];
if (!empty($pc['MENU_SECTIONS']) && is_array($pc['MENU_SECTIONS'])) {
    foreach ($pc['MENU_SECTIONS'] as $row) {
        $code = isset($row['code']) ? trim((string) $row['code']) : '';
        $title = isset($row['title']) ? (string) $row['title'] : '';
        if ($title === '') {
            continue;
        }
        $href = $code !== '' ? '/katalog/' . rawurlencode($code) . '/' : '/katalog/';
        $mainNavItems[] = ['href' => $href, 'text' => $title];
    }
}
if ($mainNavItems === []) {
    $mainNavItems = [
        ['href' => '/katalog/', 'text' => 'Профессиональная косметика'],
        ['href' => '/katalog/', 'text' => 'Домашний уход и профилактика'],
        ['href' => '/katalog/', 'text' => 'Косметика SUDA'],
        ['href' => '/katalog/', 'text' => 'Инструменты'],
        ['href' => '/katalog/', 'text' => 'Эксклюзивные'],
    ];
}
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="<?= LANG_CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $APPLICATION->ShowHead(); ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>

    <link rel="icon" href="/favicons/favicon.ico" sizes="any" />
    <link rel="icon" href="/favicons/icon.svg" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="/favicons/apple-touch-icon.png" />
    <link rel="manifest" href="/favicons/manifest.webmanifest" />
</head>
<div id="panel"><?php $APPLICATION->ShowPanel(); ?></div>
<body>
<div class="wrapper">

<header class="page-header">
    <div class="page-header__top bg-gray-800 text-gray-200 text-md">
        <div class="container">
            <div class="page-header__top-inner">
                <div class="page-header__top-text text-sm">Официальный магазин профессиональной косметики AArkada</div>
                <nav class="page-header__nav top-nav" aria-label="Верхнее меню">
                    <ul class="top-nav__list flex gap-1 list">
                        <?php foreach ($topNavItems as $item) { ?>
                            <li class="top-nav__item">
                                <a href="<?= $h($item['href']) ?>" class="top-nav__link link"><?= $h($item['text']) ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-header__main py-2 lg:py-4">
        <div class="container">
            <div class="page-header__main-inner">
                <a href="/" class="logo-company page-header__logo link">
                    <span class="logo-company__img"><?= $logoSvg ?></span>
                    <span class="logo-company__text font-semibold">Podoexpert.ru</span>
                </a>

                <div class="page-header__mobile-menu">
                    <button type="button" class="btn-reset mobile-menu-open" aria-label="Открыть меню" aria-expanded="false" aria-controls="mobile-menu" onclick="document.body.classList.add('show-mobile-nav')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                </div>

                <div class="page-header__search">
                    <form class="page-header__search-form flex w-full items-center relative" action="/search/" method="get" role="search">
                        <label class="hidden" for="header-search-q">Поиск по сайту</label>
                        <input id="header-search-q" class="input" type="search" name="q" placeholder="Поиск..." autocomplete="off">
                        <button class="page-header__btn-search btn-reset" type="submit" aria-label="Найти">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><rect width="24" height="24" fill="none"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m21 21l-4.343-4.343m0 0A8 8 0 1 0 5.343 5.343a8 8 0 0 0 11.314 11.314" stroke-width="1"/></svg>
                        </button>
                    </form>
                </div>

                <div class="page-header__contacts">
                    <div class="page-header__social">
                        <div class="social-links">
                            <div class="social-links__items">
                                <a class="social-links__item" href="https://max.ru/u/f9LHodD0cOJ5StNOKRUYS3zdDTboBcV62ZouzHtIhD-8GcK2a8lprlFPsAY" target="_blank" rel="noopener noreferrer" title="Написать в MAX">
                                    <span class="social-links__icon icon-max">
                                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.3405 23.9342C9.97568 23.9342 8.87728 23.5899 6.97252 22.2125C5.76041 23.762 1.94518 24.9672 1.77774 22.9012C1.77774 21.3535 1.42788 20.0492 1.04269 18.6132C0.570922 16.8544 0.0461426 14.898 0.0461426 12.0546C0.0461426 5.27426 5.6424 0.175079 12.2777 0.175079C18.913 0.175079 24.1153 5.52322 24.1153 12.1205C24.1153 18.7178 18.7474 23.9342 12.3405 23.9342ZM12.4368 6.03673C9.20791 5.86848 6.68817 8.0948 6.13253 11.5794C5.6724 14.465 6.48821 17.9812 7.18602 18.1582C7.51488 18.2416 8.35763 17.564 8.87711 17.0475C9.73154 17.5981 10.712 18.0245 11.8019 18.0813C15.1168 18.254 18.0544 15.6761 18.228 12.382C18.4016 9.08792 15.7517 6.20946 12.4368 6.03673Z" fill="currentColor"></path></svg>
                                    </span>
                                </a>
                                <a class="social-links__item" href="https://t.me/SergeyHermle" target="_blank" rel="noopener noreferrer" title="Написать в Telegram">
                                    <span class="social-links__icon icon-telegram">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 50 50" aria-hidden="true"><path d="M46.137,6.552c-0.75-0.636-1.928-0.727-3.146-0.238l-0.002,0C41.708,6.828,6.728,21.832,5.304,22.445 c-0.259,0.09-2.521,0.934-2.288,2.814c0.208,1.695,2.026,2.397,2.248,2.478l8.893,3.045c0.59,1.964,2.765,9.21,3.246,10.758 c0.3,0.965,0.789,2.233,1.646,2.494c0.752,0.29,1.5,0.025,1.984-0.355l5.437-5.043l8.777,6.845l0.209,0.125 c0.596,0.264,1.167,0.396,1.712,0.396c0.421,0,0.825-0.079,1.211-0.237c1.315-0.54,1.841-1.793,1.896-1.935l6.556-34.077C47.231,7.933,46.675,7.007,46.137,6.552z M22,32l-3,8l-3-10l23-17L22,32z"></path></svg>
                                    </span>
                                </a>
                                <a class="social-links__item" href="https://vk.com/podoexpert" target="_blank" rel="noopener noreferrer" title="ВКонтакте">
                                    <span class="social-links__icon icon-vk">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" fill="none"></rect><path fill="currentColor" fill-rule="evenodd" d="M23.45 5.948c.166-.546 0-.948-.795-.948H20.03c-.668 0-.976.347-1.143.73c0 0-1.335 3.196-3.226 5.272c-.612.602-.89.793-1.224.793c-.167 0-.418-.191-.418-.738V5.948c0-.656-.184-.948-.74-.948H9.151c-.417 0-.668.304-.668.593c0 .621.946.765 1.043 2.513v3.798c0 .833-.153.984-.487.984c-.89 0-3.055-3.211-4.34-6.885C4.45 5.288 4.198 5 3.527 5H.9c-.75 0-.9.347-.9.73c0 .682.89 4.07 4.145 8.551C6.315 17.341 9.37 19 12.153 19c1.669 0 1.875-.368 1.875-1.003v-2.313c0-.737.158-.884.687-.884c.39 0 1.057.192 2.615 1.667C19.11 18.216 19.403 19 20.405 19h2.625c.75 0 1.126-.368.91-1.096c-.238-.724-1.088-1.775-2.215-3.022c-.612-.71-1.53-1.475-1.809-1.858c-.389-.491-.278-.71 0-1.147c0 0 3.2-4.426 3.533-5.929" clip-rule="evenodd"></path></svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="page-header__phone">
                        <a class="link" href="tel:79932066368">+7 (993) 206-63-68</a>
                    </div>
                </div>

                <div class="page-header__actions">
                    <div class="page-header__cart minicart">
                        <a href="/personal/cart/" class="minicart__link link">
                            <span class="minicart__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                                    <rect width="24" height="24" fill="none"/>
                                    <path fill="currentColor" fill-rule="evenodd" d="M12 2.75A2.25 2.25 0 0 0 9.75 5v.26c.557-.01 1.168-.01 1.84-.01h.821c.67 0 1.282 0 1.84.01V5A2.25 2.25 0 0 0 12 2.75m3.75 2.578V5a3.75 3.75 0 1 0-7.5 0v.328q-.214.018-.414.043c-1.01.125-1.842.387-2.55.974S4.168 7.702 3.86 8.672c-.3.94-.526 2.147-.81 3.666l-.021.11c-.402 2.143-.718 3.832-.777 5.163c-.06 1.365.144 2.495.914 3.422c.77.928 1.843 1.336 3.195 1.529c1.32.188 3.037.188 5.218.188h.845c2.18 0 3.898 0 5.217-.188c1.352-.193 2.426-.601 3.196-1.529s.972-2.057.913-3.422c-.058-1.331-.375-3.02-.777-5.163l-.02-.11c-.285-1.519-.512-2.727-.81-3.666c-.31-.97-.72-1.74-1.428-2.327c-.707-.587-1.54-.85-2.55-.974a11 11 0 0 0-.414-.043M8.02 6.86c-.855.105-1.372.304-1.776.64c-.403.334-.694.805-.956 1.627c-.267.84-.478 1.958-.774 3.537c-.416 2.217-.711 3.8-.764 5.013c-.052 1.19.14 1.88.569 2.399c.43.517 1.073.832 2.253 1c1.2.172 2.812.174 5.068.174h.72c2.257 0 3.867-.002 5.068-.173c1.18-.169 1.823-.484 2.253-1.001c.43-.518.621-1.208.57-2.4c-.054-1.211-.349-2.795-.765-5.012c-.296-1.58-.506-2.696-.774-3.537c-.262-.822-.552-1.293-.956-1.628s-.92-.534-1.776-.64c-.876-.108-2.013-.109-3.62-.109h-.72c-1.607 0-2.744.001-3.62.11" clip-rule="evenodd"/>
                                </svg>
                                <span class="minicart__total">
                                    <span class="minicart__badge js-header-cart-count">0</span>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header__bottom">
        <div class="container">
            <nav class="page-header__nav main-nav" aria-label="Основное меню">
                <ul class="main-nav__items list">
                    <li class="main-nav__item main-nav__item--catalog">
                        <a href="/katalog/" class="main-nav__catalog-trigger main-nav__link link" aria-label="Каталог">
                            Каталог
                            <svg class="icon-chevron-down" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" aria-hidden="true"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 7.5 5 5 5-5"></path></svg>
                        </a>
                        <div class="main-nav__catalog-dropdown">
                            <ul class="main-nav__catalog-list list" role="menu" aria-label="Каталог">
                                <?php foreach ($catalogMenuItems as $item) { ?>
                                    <li class="main-nav__catalog-list-item" role="none">
                                        <a href="<?= $h($item['href']) ?>" class="main-nav__catalog-link link" role="menuitem"><?= $h($item['text']) ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </li>
                    <?php foreach ($mainNavItems as $item) { ?>
                        <li class="main-nav__item">
                            <a href="<?= $h($item['href']) ?>" class="main-nav__link link"><?= $h($item['text']) ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu__inner p-4">
        <button type="button" class="btn-reset mobile-menu__close" onclick="document.body.classList.remove('show-mobile-nav')" aria-label="Закрыть меню">
            <svg class="icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>
        <nav class="mobile-menu__nav mobile-nav" aria-label="Меню (моб.)">
            <ul class="mobile-nav__menu list font-medium flex flex-col gap-2">
                <?php foreach ($topNavItems as $item) { ?>
                    <li class="mobile-nav__item">
                        <a href="<?= $h($item['href']) ?>" class="mobile-nav__link link uppercase"><?= $h($item['text']) ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>

        <nav class="mobile-menu__nav mobile-nav mb-6" aria-label="Каталог (моб.)">
            <ul class="mobile-nav__menu list font-medium flex flex-col gap-2">
                <li class="mobile-nav__item mobile-nav__item--catalog">
                    <details class="mobile-nav__catalog-accordion">
                        <summary class="mobile-nav__catalog-summary link uppercase">
                            <span>Каталог</span>
                            <span class="mobile-nav__catalog-arrow" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                        </summary>
                        <ul class="mobile-nav__catalog-list list">
                            <?php foreach ($catalogMenuItems as $item) { ?>
                                <li class="mobile-nav__catalog-item">
                                    <a href="<?= $h($item['href']) ?>" class="mobile-nav__catalog-link link"><?= $h($item['text']) ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </details>
                </li>
                <?php foreach ($mainNavItems as $item) { ?>
                    <li class="mobile-nav__item">
                        <a href="<?= $h($item['href']) ?>" class="mobile-nav__link link uppercase"><?= $h($item['text']) ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>

<main class="page-content">
