<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);

$arUserGroups = [];
$dbGroups = CGroup::GetList($b = 'ID', $o = 'ASC', ['ACTIVE' => 'Y']);

while ($arGroup = $dbGroups->GetNext()) {
    $arUserGroups[$arGroup['ID']] = '[' . $arGroup['ID'] . '] ' . $arGroup['NAME'];
}

$aTabs = [
    [
        'DIV' => 'edit',
        'TAB' => Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_TAB_NAME'),
        'TITLE' => Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_TAB_NAME'),
        'OPTIONS' => [
            [
                'switch_on',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTION_SWITCH_ON'),
                'N',
                ['checkbox']
            ],
            [
                'min_price',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_MIN_PRICE'),
                5000,
                ['text', 10]
            ],
            [
                'plus_discount',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_PLUS_DISCOUNT'),
                'N',
                ['checkbox']
            ],
            [
                'minus_delivery',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_MINUS_DELIVERY'),
                'N',
                ['checkbox']
            ],
            [
                'min_price_text',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_MIN_PRICE_TEXT'),
                Loc::getMessage('SL3W_MINPRICE_ORDER_DEFAULT_MIN_PRICE_TEXT'),
                ['textarea', 5, 50]
            ],
            ['note' => Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_MIN_PRICE_TEXT_NOTE')],
            [
                'show_popup_order_page',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_SHOW_POPUP_ORDER_PAGE'),
                'N',
                ['checkbox']
            ],
            [
                'user_groups',
                Loc::getMessage('SL3W_MINPRICE_ORDER_OPTIONS_USER_GROUPS'),
                '',
                ['multiselectbox', $arUserGroups]
            ],
        ]
    ]
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();
?>

    <form action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= $module_id ?>&lang=<?= LANG ?>"
          method="post">

        <?php
        foreach ($aTabs as $aTab) {

            if ($aTab['OPTIONS']) {

                $tabControl->BeginNextTab();

                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="<?= Loc::GetMessage('SL3W_MINPRICE_ORDER_BUTTON_APPLY') ?>"
               class="adm-btn-save"/>
        <input type="submit" name="default" value="<?= Loc::GetMessage('SL3W_MINPRICE_ORDER_BUTTON_DEFAULT') ?>"/>

        <?= bitrix_sessid_post() ?>

    </form>

<?php
$tabControl->End();

if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {

        foreach ($aTab['OPTIONS'] as $arOption) {

            if (!is_array($arOption) || $arOption['note']) {
                continue;
            }

            if ($request['apply']) {

                $optionValue = $request->getPost($arOption[0]);

                if ($arOption[3][0] == 'checkbox' && $optionValue == '') {
                    $optionValue = 'N';
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);

            } elseif ($request['default']) {

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . $module_id . '&lang=' . LANG . '&mid_menu=1');
}