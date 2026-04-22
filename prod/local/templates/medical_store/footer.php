<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
$footerLogoLinkToMain = ($APPLICATION->GetCurDir() !== SITE_DIR);

$h = static function (string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$cartQty = function_exists('podexpert_basket_total_quantity')
    ? podexpert_basket_total_quantity()
    : 0;
$cartCountBadge = function_exists('podexpert_basket_count_badge_text')
    ? podexpert_basket_count_badge_text()
    : '0';

$contacts = podexpert_medical_store_site_contacts();

$footerNavColumn1 = [
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

$medicalStoreBottomInfoMenuBase = [
    'ROOT_MENU_TYPE' => 'bottom_info',
    'MAX_LEVEL' => '1',
    'CHILD_MENU_TYPE' => 'bottom_info',
    'USE_EXT' => 'Y',
    'ALLOW_MULTI_SELECT' => 'N',
    'MENU_CACHE_TYPE' => 'A',
    'MENU_CACHE_TIME' => '36000000',
    'MENU_CACHE_USE_GROUPS' => 'Y',
    'CACHE_SELECTED_ITEMS' => 'N',
];

$medicalStoreBottomPdnMenuBase = [
    'ROOT_MENU_TYPE' => 'bottom_pdn',
    'MAX_LEVEL' => '1',
    'CHILD_MENU_TYPE' => 'bottom_pdn',
    'USE_EXT' => 'Y',
    'ALLOW_MULTI_SELECT' => 'N',
    'MENU_CACHE_TYPE' => 'A',
    'MENU_CACHE_TIME' => '36000000',
    'MENU_CACHE_USE_GROUPS' => 'Y',
    'CACHE_SELECTED_ITEMS' => 'N',
];

$iconHome = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10.5L12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>';
$iconCatalog = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="4" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="4" y="14" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="14" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>';
$iconCart = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" fill="none"/><path fill="currentColor" fill-rule="evenodd" d="M12 2.75A2.25 2.25 0 0 0 9.75 5v.26c.557-.01 1.168-.01 1.84-.01h.821c.67 0 1.282 0 1.84.01V5A2.25 2.25 0 0 0 12 2.75m3.75 2.578V5a3.75 3.75 0 1 0-7.5 0v.328q-.214.018-.414.043c-1.01.125-1.842.387-2.55.974S4.168 7.702 3.86 8.672c-.3.94-.526 2.147-.81 3.666l-.021.11c-.402 2.143-.718 3.832-.777 5.163c-.06 1.365.144 2.495.914 3.422c.77.928 1.843 1.336 3.195 1.529c1.32.188 3.037.188 5.218.188h.845c2.18 0 3.898 0 5.217-.188c1.352-.193 2.426-.601 3.196-1.529s.972-2.057.913-3.422c-.058-1.331-.375-3.02-.777-5.163l-.02-.11c-.285-1.519-.512-2.727-.81-3.666c-.31-.97-.72-1.74-1.428-2.327c-.707-.587-1.54-.85-2.55-.974a11 11 0 0 0-.414-.043M8.02 6.86c-.855.105-1.372.304-1.776.64c-.403.334-.694.805-.956 1.627c-.267.84-.478 1.958-.774 3.537c-.416 2.217-.711 3.8-.764 5.013c-.052 1.19.14 1.88.569 2.399c.43.517 1.073.832 2.253 1c1.2.172 2.812.174 5.068.174h.72c2.257 0 3.867-.002 5.068-.173c1.18-.169 1.823-.484 2.253-1.001c.43-.518.621-1.208.57-2.4c-.054-1.211-.349-2.795-.765-5.012c-.296-1.58-.506-2.696-.774-3.537c-.262-.822-.552-1.293-.956-1.628s-.92-.534-1.776-.64c-.876-.108-2.013-.109-3.62-.109h-.72c-1.607 0-2.744.001-3.62.11" clip-rule="evenodd"/></svg>';
$iconMenu = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
?>
</main>
<footer class="page-footer bg-gray-800 text-gray-200 py-8 md:py-16">
    <div class="wrapper container mx-auto">
        <div class="grid grid-cols-2 xl:grid-cols-[4fr_7.6fr] gap-4 lg:gap-10 xl:gap-x-[clamp(32px,6vw,75px)]">
            <div class="col-span-2 lg:col-span-1">
                <figure class="mb-2 lg:mb-4 flex justify-center lg:justify-start">
                    <?php if ($footerLogoLinkToMain) { ?>
                    <a href="<?= $h(SITE_DIR) ?>" class="logo-company page-footer__logo link">
                        <span class="logo-company__img"><img src="<?= $h(SITE_TEMPLATE_PATH . '/images/logo.svg') ?>" width="226" height="226" alt="" decoding="async"></span>
                        <?php if (($contacts['logo_text'] ?? '') !== '') { ?>
                        <span class="logo-company__text font-semibold"><?= $h((string) $contacts['logo_text']) ?></span>
                        <?php } ?>
                    </a>
                    <?php } else { ?>
                    <div class="logo-company page-footer__logo">
                        <span class="logo-company__img"><img src="<?= $h(SITE_TEMPLATE_PATH . '/images/logo.svg') ?>" width="226" height="226" alt="" decoding="async"></span>
                        <?php if (($contacts['logo_text'] ?? '') !== '') { ?>
                        <span class="logo-company__text font-semibold"><?= $h((string) $contacts['logo_text']) ?></span>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </figure>
                <?php if (($contacts['text_after_logo'] ?? '') !== '') { ?>
                <div class="text-base text-center lg:text-left text-balance mb-4">
                    <?= $contacts['text_after_logo'] ?>
                </div>
                <?php } ?>
                <?php if (($contacts['company_name'] ?? '') !== '' || ($contacts['inn'] ?? '') !== '' || ($contacts['ogrn'] ?? '') !== '') { ?>
                <div class="flex flex-col gap-1">
                    <?php if (($contacts['company_name'] ?? '') !== '') { ?>
                    <div class=""><?= $h($contacts['company_name']) ?></div>
                    <?php } ?>
                    <?php if (($contacts['inn'] ?? '') !== '') { ?>
                    <div class="">ИНН: <?= $h($contacts['inn']) ?></div>
                    <?php } ?>
                    <?php if (($contacts['ogrn'] ?? '') !== '') { ?>
                    <div class="">ОГРН: <?= $h($contacts['ogrn']) ?></div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <div class="col-span-2 lg:col-span-1">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[minmax(360px,1.8fr)_max-content_max-content] gap-8 lg:gap-x-14">
                    <div class="md:col-span-2 lg:col-span-1">
                        <div class="text-left text-base leading-relaxed page-footer__address">
                            <p class="font-bold mb-2 text-gray-300">
                                Магазин «Podoexpert»
                            </p>
                            <?php if ($contacts['address'] !== '') { ?>
                            <p>
                                <?php if ($contacts['maps_href'] !== '') { ?>
                                <a href="<?= $h($contacts['maps_href']) ?>" target="_blank" rel="noopener noreferrer" class="link footer-nav__link">
                                    <?= $h($contacts['address']) ?>
                                </a>
                                <?php } else { ?>
                                    <?= $h($contacts['address']) ?>
                                <?php } ?>
                            </p>
                            <?php } ?>
                            <?php if ($contacts['mode'] !== '') { ?>
                                <div class=""><?= $h($contacts['mode']) ?></div>
                            <?php } ?>
                        </div>
                        <div class="flex flex-col justify-start items-stretch lg:items-start gap-2 xs:gap-4 mt-2 lg:mt-6">
                            <?php foreach ($contacts['phones'] as $fp) { ?>
                            <a href="<?= $h($fp['href']) ?>" class="flex gap-2">
                                <svg class="w-6 h-auto" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.67962 3.32038L7.29289 2.70711C7.68342 2.31658 8.31658 2.31658 8.70711 2.70711L11.2929 5.29289C11.6834 5.68342 11.6834 6.31658 11.2929 6.70711L9.50048 8.49952C9.2016 8.7984 9.1275 9.255 9.31653 9.63307C10.4093 11.8186 12.1814 13.5907 14.3669 14.6835C14.745 14.8725 15.2016 14.7984 15.5005 14.4995L17.2929 12.7071C17.6834 12.3166 18.3166 12.3166 18.7071 12.7071L21.2929 15.2929C21.6834 15.6834 21.6834 16.3166 21.2929 16.7071L20.6796 17.3204C18.5683 19.4317 15.2257 19.6693 12.837 17.8777L11.6286 16.9714C9.88504 15.6638 8.33622 14.115 7.02857 12.3714L6.12226 11.163C4.33072 8.7743 4.56827 5.43173 6.67962 3.32038Z" fill="currentColor"></path>
                                </svg>
                                <?= $h($fp['label']) ?>
                            </a>
                            <?php } ?>

                            <?php if ($contacts['email'] !== '') { ?>
                            <a href="<?= $h($contacts['email_href']) ?>" class="flex gap-2 mb-2">
                                <svg class="w-6 h-auto" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.87868 6.37868C3 7.25736 3 8.67157 3 11.5V13.5C3 16.3284 3 17.7426 3.87868 18.6213C4.75736 19.5 6.17157 19.5 9 19.5H15C17.8284 19.5 19.2426 19.5 20.1213 18.6213C21 17.7426 21 16.3284 21 13.5V11.5C21 8.67157 21 7.25736 20.1213 6.37868C19.2426 5.5 17.8284 5.5 15 5.5H9C6.17157 5.5 4.75736 5.5 3.87868 6.37868ZM6.5547 8.66795C6.09517 8.3616 5.4743 8.48577 5.16795 8.9453C4.8616 9.40483 4.98577 10.0257 5.4453 10.3321L10.8906 13.9622C11.5624 14.4101 12.4376 14.4101 13.1094 13.9622L18.5547 10.3321C19.0142 10.0257 19.1384 9.40483 18.8321 8.9453C18.5257 8.48577 17.9048 8.3616 17.4453 8.66795L12 12.2982L6.5547 8.66795Z" fill="currentColor"></path>
                                </svg>
                                <?= $h($contacts['email']) ?>
                            </a>
                            <?php } ?>
                            <div class="social-links page-footer__social">
                                <div class="social-links__items">
                                    <?php if ($contacts['max'] !== '') { ?>
                                    <a class="social-links__item" href="<?= $h($contacts['max']) ?>" target="_blank" rel="noopener noreferrer" title="Написать в MAX">
                                        <span class="social-links__icon icon-max">
                                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.3405 23.9342C9.97568 23.9342 8.87728 23.5899 6.97252 22.2125C5.76041 23.762 1.94518 24.9672 1.77774 22.9012C1.77774 21.3535 1.42788 20.0492 1.04269 18.6132C0.570922 16.8544 0.0461426 14.898 0.0461426 12.0546C0.0461426 5.27426 5.6424 0.175079 12.2777 0.175079C18.913 0.175079 24.1153 5.52322 24.1153 12.1205C24.1153 18.7178 18.7474 23.9342 12.3405 23.9342ZM12.4368 6.03673C9.20791 5.86848 6.68817 8.0948 6.13253 11.5794C5.6724 14.465 6.48821 17.9812 7.18602 18.1582C7.51488 18.2416 8.35763 17.564 8.87711 17.0475C9.73154 17.5981 10.712 18.0245 11.8019 18.0813C15.1168 18.254 18.0544 15.6761 18.228 12.382C18.4016 9.08792 15.7517 6.20946 12.4368 6.03673Z" fill="currentColor"></path></svg>
                                        </span>
                                    </a>
                                    <?php } ?>
                                    <?php if ($contacts['telegram'] !== '') { ?>
                                    <a class="social-links__item" href="<?= $h($contacts['telegram']) ?>" target="_blank" rel="noopener noreferrer" title="Написать в Telegram">
                                        <span class="social-links__icon icon-telegram">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 50 50" aria-hidden="true"><path d="M46.137,6.552c-0.75-0.636-1.928-0.727-3.146-0.238l-0.002,0C41.708,6.828,6.728,21.832,5.304,22.445 c-0.259,0.09-2.521,0.934-2.288,2.814c0.208,1.695,2.026,2.397,2.248,2.478l8.893,3.045c0.59,1.964,2.765,9.21,3.246,10.758 c0.3,0.965,0.789,2.233,1.646,2.494c0.752,0.29,1.5,0.025,1.984-0.355l5.437-5.043l8.777,6.845l0.209,0.125 c0.596,0.264,1.167,0.396,1.712,0.396c0.421,0,0.825-0.079,1.211-0.237c1.315-0.54,1.841-1.793,1.896-1.935l6.556-34.077C47.231,7.933,46.675,7.007,46.137,6.552z M22,32l-3,8l-3-10l23-17L22,32z"></path></svg>
                                        </span>
                                    </a>
                                    <?php } ?>
                                    <?php if ($contacts['vk'] !== '') { ?>
                                    <a class="social-links__item" href="<?= $h($contacts['vk']) ?>" target="_blank" rel="noopener noreferrer" title="ВКонтакте">
                                        <span class="social-links__icon icon-vk">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><rect width="24" height="24" fill="none"></rect><path fill="currentColor" fill-rule="evenodd" d="M23.45 5.948c.166-.546 0-.948-.795-.948H20.03c-.668 0-.976.347-1.143.73c0 0-1.335 3.196-3.226 5.272c-.612.602-.89.793-1.224.793c-.167 0-.418-.191-.418-.738V5.948c0-.656-.184-.948-.74-.948H9.151c-.417 0-.668.304-.668.593c0 .621.946.765 1.043 2.513v3.798c0 .833-.153.984-.487.984c-.89 0-3.055-3.211-4.34-6.885C4.45 5.288 4.198 5 3.527 5H.9c-.75 0-.9.347-.9.73c0 .682.89 4.07 4.145 8.551C6.315 17.341 9.37 19 12.153 19c1.669 0 1.875-.368 1.875-1.003v-2.313c0-.737.158-.884.687-.884c.39 0 1.057.192 2.615 1.667C19.11 18.216 19.403 19 20.405 19h2.625c.75 0 1.126-.368.91-1.096c-.238-.724-1.088-1.775-2.215-3.022c-.612-.71-1.53-1.475-1.809-1.858c-.389-.491-.278-.71 0-1.147c0 0 3.2-4.426 3.533-5.929" clip-rule="evenodd"></path></svg>
                                        </span>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="page-footer__menu md:col-span-1 lg:col-span-1">
                        <p class="font-bold mb-2 text-gray-300">
                            Каталог
                        </p>
                        <nav class="col-span-2 lg:col-span-1" aria-label="Каталог в подвале">
                            <ul class="footer-nav__list list flex flex-col gap-2">
                                <?php foreach ($footerNavColumn1 as $item) { ?>
                                    <li class="footer-nav__item">
                                        <a href="<?= $h($item['href']) ?>" class="footer-nav__link link"><?= $h($item['text']) ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                    <div class="page-footer__menu md:col-span-1 lg:col-span-1">
                        <p class="font-bold mb-2 text-gray-300">
                            Информация
                        </p>
                        <nav class="col-span-2 lg:col-span-1" aria-label="Информация в подвале">
                            <?php
                            $APPLICATION->IncludeComponent(
                                'bitrix:menu',
                                'medical_store_top',
                                array_merge($medicalStoreBottomInfoMenuBase, [
                                    'MENU_UL_CLASS' => 'footer-nav__list list flex flex-col gap-2',
                                    'MENU_LI_CLASS' => 'footer-nav__item',
                                    'MENU_LINK_CLASS' => 'footer-nav__link link',
                                ])
                            );
                            ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <hr class="col-span-2 border-gray-600 block my-4 lg:my-10">

        <div class="flex gap-4 justify-between flex-wrap">
            <nav class="page-footer__agreement" aria-label="Документы и согласия">
                <?php
                $APPLICATION->IncludeComponent(
                    'bitrix:menu',
                    'medical_store_top',
                    array_merge($medicalStoreBottomPdnMenuBase, [
                        'MENU_UL_CLASS' => 'footer-nav__list list flex flex-wrap gap-2 lg:gap-4',
                        'MENU_LI_CLASS' => 'footer-nav__item',
                        'MENU_LINK_CLASS' => 'footer-nav__link link',
                    ])
                );
                ?>
            </nav>
            <p class="page-footer__copyright">© 2017–<?= (int) date('Y') ?><?php if (($contacts['copyright'] ?? '') !== '') { ?> <?= $h($contacts['copyright']) ?><?php } ?></p>
        </div>

    </div>
</footer>

<div class="mobile-footer-nav bg-gray-800">
    <div class="mobile-footer-nav__inner">
        <div class="mobile-footer-nav__item">
            <a href="/" class="mobile-footer-nav__item-link link">
                <span class="mobile-footer-nav__item-icon"><?= $iconHome ?></span>
                <span class="mobile-footer-nav__item-text text-sm">Главная</span>
            </a>
        </div>
        <div class="mobile-footer-nav__item">
            <a href="/catalog/" class="mobile-footer-nav__item-link link">
                <span class="mobile-footer-nav__item-icon"><?= $iconCatalog ?></span>
                <span class="mobile-footer-nav__item-text text-sm">Каталог</span>
            </a>
        </div>
        <div class="mobile-footer-nav__item">
            <a href="/cart/" class="mobile-footer-nav__item-link link">
                <span class="mobile-footer-nav__item-icon"><?= $iconCart ?></span>
                <span class="mobile-footer-nav__total-count js-total-count-minicart<?= $cartQty > 0 ? '' : ' hidden' ?>"><?= $h($cartCountBadge) ?></span>
                <span class="mobile-footer-nav__item-text text-sm">Корзина</span>
            </a>
        </div>
        <div class="mobile-footer-nav__item">
            <button type="button" class="mobile-footer-nav__item-link link btn-reset" onclick="document.body.classList.toggle('show-mobile-nav')">
                <span class="mobile-footer-nav__item-icon"><?= $iconMenu ?></span>
                <span class="mobile-footer-nav__item-text text-sm">Меню</span>
            </button>
        </div>
    </div>
</div>
</div>
</body>
</html>
