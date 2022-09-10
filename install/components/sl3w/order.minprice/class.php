<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Sl3w\MinPriceOrder\Settings;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('sl3w.minpriceorder')) {
    ShowError(Loc::getMessage('SL3W_MINPRICE_ORDER_MODULE_NOT_INSTALLED'));
    return;
}

class OrderMinPriceComponent extends \CBitrixComponent
{
    private $cacheId;

    public function onPrepareComponentParams($params)
    {
        $this->cacheId = 'sl3w_min_price_order_' . md5(json_encode($params));

        return $params;
    }

    public function cacheTime()
    {
        return $this->arParams['CACHE_TIME'];
    }

    public function getText()
    {
        $minPrice = Settings::get('min_price');
        $minPriceText = $this->arParams['MIN_PRICE_TEXT'] ?: Settings::get('min_price_text');

        $minPriceText = str_replace('#MIN_PRICE#', $minPrice, $minPriceText);

        $this->arResult['TEXT'] = $minPriceText;
    }

    public function executeComponent()
    {
        if (Settings::get('switch_on') != 'Y') {
            return false;
        }

        $cache = new CPHPCache();
        $cache->InitCache($this->cacheTime(), $this->cacheId);
        $this->arResult = $cache->GetVars();

        if (!$this->arResult) {
            $this->getText();

            $cache->StartDataCache($this->cacheTime(), $this->cacheId);
            $cache->EndDataCache($this->arResult);
        }

        if ($this->arResult) {
            $this->includeComponentTemplate();
        }
    }
}