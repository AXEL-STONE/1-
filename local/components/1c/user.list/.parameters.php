<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc as Loc;

$arSorts = ['ASC' => Loc::getMessage('ORDER_ASC'), 'DESC' => Loc::getMessage('ORDER_DESC')];

$arSortFields = [
    'ID' => Loc::getMessage('SORT_FIELD_BY_ID'),
    'NAME' => Loc::getMessage('SORT_FIELD_BY_NAME'),
    'LOGIN' => Loc::getMessage('SORT_FIELD_BY_LOGIN'),
    'EMAIL' => Loc::getMessage('SORT_FIELD_BY_EMAIL'),
    'TIMESTAMP_X' => Loc::getMessage('SORT_FIELD_BY_TIMESTAMP_X'),
];

$arFieldsCode = [
    'ID' => Loc::getMessage('SORT_FIELD_BY_ID'),
    'NAME' => Loc::getMessage('SORT_FIELD_BY_NAME'),
    'LOGIN' => Loc::getMessage('SORT_FIELD_BY_LOGIN'),
    'EMAIL' => Loc::getMessage('SORT_FIELD_BY_EMAIL'),
    'TIMESTAMP_X' => Loc::getMessage('SORT_FIELD_BY_TIMESTAMP_X'),
];

$arComponentParameters = [
    'GROUPS' => [
    ],
    'PARAMETERS' => [
        'AJAX_MODE' => [],
        'CACHE_TIME' => ['DEFAULT' => 3600],
        'CACHE_GROUPS' => [
            'PARENT' => 'CACHE_SETTINGS',
            'NAME' => Loc::getMessage('CACHE_GROUPS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ],
        'SORT_BY' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SORT_BY'),
            'TYPE' => 'LIST',
            'DEFAULT' => 'ID',
            'VALUES' => $arSortFields,
            'ADDITIONAL_VALUES' => 'Y',
        ],
        'SORT_ORDER' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SORT_ORDER'),
            'TYPE' => 'LIST',
            'DEFAULT' => 'ASC',
            'VALUES' => $arSorts,
        ],
        'PAGE_ELEMENT_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('PAGE_ELEMENT_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '20',
        ],
        'EXPORT_ELEMENT_COUNT' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('EXPORT_ELEMENT_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '1000',
        ],
        'FIELD_CODE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('FIELD_CODE'),
            'TYPE' => 'LIST',
            'VALUES' => $arFieldsCode,
            'MULTIPLE' => 'Y',
            "ADDITIONAL_VALUES" => "Y",
        ],
    ],
];