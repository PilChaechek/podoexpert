<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
$query = htmlspecialchars($arResult["REQUEST"]["QUERY"] ?? '');
?>
<div class="catalog-search">
    <form class="catalog-search__form"
          id="catalog-search-form"
          action="/search/"
          method="get"
          role="search"
          autocomplete="off">

        <label class="visually-hidden" for="catalog-search-input">Поиск по каталогу</label>

        <input
            id="catalog-search-input"
            class="catalog-search__input"
            type="search"
            name="q"
            value="<?= $query ?>"
            placeholder="Поиск..."
            autocomplete="off"
        >

        <button class="catalog-search__btn" type="submit" aria-label="Найти">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="m21 21-4.343-4.343m0 0A8 8 0 1 0 5.343 5.343a8 8 0 0 0 11.314 11.314"/>
            </svg>
        </button>
    </form>

    <?php if (isset($arResult["REQUEST"]["ORIGINAL_QUERY"])): ?>
        <p class="catalog-search__language-hint">
            <?= GetMessage("CT_BSP_KEYBOARD_WARNING", [
                "#query#" => '<a href="' . $arResult["ORIGINAL_QUERY_URL"] . '">' . $arResult["REQUEST"]["ORIGINAL_QUERY"] . '</a>',
            ]) ?>
        </p>
    <?php endif; ?>

    <div class="catalog-search__results-area" id="catalog-search-results-area"></div>
</div>

<script>
(function () {
    var form  = document.getElementById('catalog-search-form');
    var input = document.getElementById('catalog-search-input');
    if (!form || !input) return;

    var timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var q = input.value.trim();
        if (q.length < 2) return;

        timer = setTimeout(function () {
            form.submit();
        }, 600);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            clearTimeout(timer);
        }
    });
})();
</script>
