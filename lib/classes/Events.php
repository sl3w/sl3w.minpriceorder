<?php

namespace Sl3w\MinPriceOrder;

use Sl3w\MinPriceOrder\Settings;
use Bitrix\Main\Page\Asset;
use CUser;

class Events
{
    public static function OnOrderSaveCheckMinPrice(\Bitrix\Main\Event $event)
    {
        $result = new \Bitrix\Main\Entity\EventResult(\Bitrix\Main\EventResult::SUCCESS);

        if (!defined('ADMIN_SECTION')) {
            $minPrice = Settings::get('min_price', 0);
            $minPriceText = Settings::get('min_price_text');

            if (!Settings::yes('switch_on') || !$minPrice || !$minPriceText) {
                return $result;
            }

            $order = $event->getParameter('ENTITY');

            $settingUserGroupStr = Settings::get('user_groups');
            $settingUserGroups = $settingUserGroupStr ? explode(',', $settingUserGroupStr) : [];

            $userInGroup = false;

            if (!empty($settingUserGroups)) {
                $userGroups = CUser::GetUserGroup($order->getUserId());

                foreach ($settingUserGroups as $settingUserGroup) {
                    if (in_array($settingUserGroup, $userGroups)) {
                        $userInGroup = true;
                        break;
                    }
                }
            } else {
                $userInGroup = true;
            }

            if (!$userInGroup) {
                return $result;
            }

            if ($order instanceof \Bitrix\Sale\Order) {

                $deliveriesToCheckStr = Settings::get('delivery_types');
                $deliveriesToCheck = $deliveriesToCheckStr ? explode(',', $deliveriesToCheckStr) : [];

                $deliveriesChecked = false;

                if (!empty($deliveriesToCheck)) {
                    $orderDeliveries = $order->getDeliveryIdList();

                    foreach ($orderDeliveries as $orderDelivery) {
                        if (in_array($orderDelivery, $deliveriesToCheck)) {
                            $deliveriesChecked = true;
                            break;
                        }
                    }
                } else {
                    $deliveriesChecked = true;
                }

                if (!$deliveriesChecked) {
                    return $result;
                }

                $price = $order->getPrice();

                if (Settings::yes('plus_discount')) {
                    $price += self::getDiscount($order);
                }

                if (Settings::yes('minus_delivery')) {
                    $price -= $order->getDeliveryPrice();
                }

                if ($price < $minPrice) {
                    $minPriceText = str_replace('#MIN_PRICE#', $minPrice, $minPriceText);
                    $minPriceText = str_replace('#PRICE#', $price, $minPriceText);
                    $minPriceText = str_replace('#DIFF_PRICE#', $minPrice - $price, $minPriceText);

                    $minPriceText = '<div id="sl3w_minpriceorder__text">' . $minPriceText . '</div>';

                    $result = new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError($minPriceText, 'code'), 'sale');
                }
            }
        }

        return $result;
    }

    private static function getDiscount($order)
    {
        $discount = 0;
        $basket = $order->getBasket();

        foreach ($basket as $item) {
            $discount += ($item->getBasePrice() - $item->getPrice()) * $item->getQuantity();
        }

        return $discount;
    }

    public static function appendScriptsToPage()
    {
        if (!defined('ADMIN_SECTION')) {
            if (Settings::yes('switch_on') && Settings::yes('show_popup_order_page')
                && Settings::get('min_price', 0) && Settings::get('min_price_text')) {

                Asset::getInstance()->addJs('/bitrix/js/' . Settings::getModuleId() . '/script.min.js');
                Asset::getInstance()->addCss('/bitrix/css/' . Settings::getModuleId() . '/style.min.css');
            }
        }
    }
}