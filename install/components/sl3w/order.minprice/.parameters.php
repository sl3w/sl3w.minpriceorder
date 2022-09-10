<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    'GROUPS' => [
        'GROUP' => [
            'NAME' => Loc::getMessage('GROUP'),
            'SORT' => '100',
        ],
    ],
    'PARAMETERS' => [
        'MIN_PRICE_TEXT' => [
            'PARENT' => 'GROUP',
            'NAME' => Loc::getMessage('MIN_PRICE_TEXT'),
            'TYPE' => 'STRING',
            'ROWS' => '5',
            'COLS' => '30',
            'DEFAULT' => ''
        ],
    ],
];