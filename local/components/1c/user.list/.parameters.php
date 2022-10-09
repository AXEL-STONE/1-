<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * @var array $templateProperties
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

use Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\GroupTable;

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

$arGroups = [];
$resGroup = GroupTable::getList([
    'select' => ['NAME', 'ID'],
]);
while ($group = $resGroup->fetch()) {
    $arGroups[$group['ID']] = $group['NAME'];
}
if(!isset($arCurrentValues['CHECK_GROUP_ID'])) {
    $arCurrentValues['CHECK_GROUP_ID'] = [
        0 => 6,
    ];
}
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
        'CHECK_GROUP_REG' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('CHECK_GROUP_REG'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
        ],
        'CHECK_GROUP_ID' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('CHECK_GROUP_ID'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arGroups,
            'ADDITIONAL_VALUES' => 'Y',
            'HIDDEN' => (isset($arCurrentValues['CHECK_GROUP_REG']) && $arCurrentValues['CHECK_GROUP_REG'] === 'N' ? 'Y' : 'N')
        ],
    ],
];