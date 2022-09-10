<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class sl3w_minpriceorder extends CModule
{
    var $MODULE_ID = 'sl3w.minpriceorder';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_DIR;

    public function __construct()
    {
        if (file_exists(__DIR__ . '/version.php')) {

            $arModuleVersion = [];

            include(__DIR__ . '/version.php');

            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

            $this->MODULE_NAME = Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_DESC');

            $this->PARTNER_NAME = Loc::getMessage('SL3W_MINPRICE_ORDER_PARTNER_NAME');
            $this->PARTNER_URI = Loc::getMessage('SL3W_MINPRICE_ORDER_PARTNER_URI');

            $this->MODULE_DIR = dirname(__FILE__) . '/../';
        }
    }

    public function DoInstall()
    {
        global $APPLICATION;

        RegisterModule($this->MODULE_ID);

        $this->InstallEvents();
        $this->InstallFiles();
        $this->SetOptions();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('SL3W_MINPRICE_ORDER_INSTALL_TITLE') . ' "' . Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_NAME') . '"',
            __DIR__ . '/step.php'
        );
    }

    public function DoUninstall()
    {
        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->ClearOptions();

        UnRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('SL3W_MINPRICE_ORDER_UNINSTALL_TITLE') . ' "' . Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_NAME') . '"',
            __DIR__ . '/unstep.php'
        );
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/components',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/assets/scripts',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . $this->MODULE_ID . '/',
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . '/assets/styles',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/' . $this->MODULE_ID . '/',
            true,
            true
        );

        return false;
    }

    public function UnInstallFiles()
    {
        DeleteDirFilesEx('/bitrix/components/sl3w/order.minprice');

        DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID);

        DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID);

        return false;
    }

    public function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            'sale',
            'OnSaleOrderBeforeSaved',
            $this->MODULE_ID,
            'Sl3w\MinPriceOrder\Events',
            'OnOrderSaveCheckMinPrice'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            $this->MODULE_ID,
            'Sl3w\MinPriceOrder\Events',
            'appendScriptsToPage'
        );

        return true;
    }

    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'sale',
            'OnSaleOrderBeforeSaved',
            $this->MODULE_ID,
            'Sl3w\MinPriceOrder\Events',
            'OnOrderSaveCheckMinPrice'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            $this->MODULE_ID,
            'Sl3w\MinPriceOrder\Events',
            'appendScriptsToPage'
        );

        return true;
    }

    private function SetOptions()
    {
        Option::set($this->MODULE_ID, 'min_price', 5000);
        Option::set($this->MODULE_ID, 'min_price_text', Loc::getMessage('SL3W_MINPRICE_ORDER_DEFAULT_MIN_PRICE_TEXT'));
    }

    private function ClearOptions()
    {
        Option::delete($this->MODULE_ID);
    }

    private static function ShowAdminError($errorText)
    {
        CAdminMessage::ShowMessage([
            'TYPE' => 'ERROR',
            'MESSAGE' => $errorText,
            'DETAILS' => '',
            'HTML' => true
        ]);
    }
}