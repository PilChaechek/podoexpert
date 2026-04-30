<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
$this->setFrameMode(true);

$INPUT_ID = trim($arParams['~INPUT_ID']);
if ($INPUT_ID === '') {
    $INPUT_ID = 'title-search-input';
}
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams['~CONTAINER_ID']);
if ($CONTAINER_ID === '') {
    $CONTAINER_ID = 'title-search';
}
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);
?>
<div id="<?= $CONTAINER_ID ?>" class="header-search">
    <form class="header-search__form" action="<?= $arResult['FORM_ACTION'] ?>">
        <label class="hidden" for="<?= $INPUT_ID ?>">Поиск по каталогу</label>
        <input
            id="<?= $INPUT_ID ?>"
            class="header-search__input"
            type="text"
            name="q"
            value=""
            maxlength="50"
            placeholder="Поиск..."
            autocomplete="off"
        >
        <button class="header-search__btn btn-reset" type="submit" aria-label="Найти">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                <rect width="24" height="24" fill="none"/>
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-4.343-4.343m0 0A8 8 0 1 0 5.343 5.343a8 8 0 0 0 11.314 11.314" stroke-width="1"/>
            </svg>
        </button>
    </form>
</div>
<script>
    BX.ready(function () {
        new JCTitleSearch({
            'AJAX_PAGE': '<?= CUtil::JSEscape(POST_FORM_ACTION_URI) ?>',
            'CONTAINER_ID': '<?= $CONTAINER_ID ?>',
            'INPUT_ID': '<?= $INPUT_ID ?>',
            'MIN_QUERY_LEN': 2
        });
    });
</script>
