<?php

namespace Sl3w\MinPriceOrder;

use Bitrix\Main\Config\Option;

class Settings
{
    private static $module_id = 'sl3w.minpriceorder';

    public static function get($name, $default = '')
    {
        return Option::get(self::$module_id, $name, $default);
    }

    public static function set($name, $value)
    {
        Option::set(self::$module_id, $name, $value);
    }

    public static function yes($name)
    {
        return self::get($name) == 'Y';
    }

    public static function deleteAll()
    {
        Option::delete(self::$module_id);
    }

    public static function getModuleId()
    {
        return self::$module_id;
    }
}