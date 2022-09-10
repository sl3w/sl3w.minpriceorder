<?php if (!check_bitrix_sessid()) return;

/** @global CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($errorException = $APPLICATION->GetException()) {
    CAdminMessage::ShowMessage($errorException->GetString());
} else {
    CAdminMessage::ShowNote(sprintf('%s "%s" %s',
        Loc::getMessage('SL3W_MINPRICE_ORDER_UNSTEP_BEFORE'),
        Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_NAME'),
        Loc::getMessage('SL3W_MINPRICE_ORDER_UNSTEP_AFTER')
    ));
}
?>

<form action='<?= $APPLICATION->GetCurPage(); ?>'>
    <input type='hidden' name='lang' value='<?= LANG; ?>'>
    <input type='submit' name='' value='<?= Loc::getMessage('SL3W_MINPRICE_ORDER_UNSTEP_BACK'); ?>'>
<form>