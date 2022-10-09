<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('USER_LIST'),
    'DESCRIPTION' => Loc::getMessage('USER_LIST_DESCRIPTION'),
    'ICON' => '/images/icon.gif',
    'CACHE_PATH' => 'Y',
    'PATH' => array(
        'ID' => 'utility',
        'CHILD' => array(
            'ID' => 'user',
            'NAME' => Loc::getMessage('GROUP_NAME')
        ),
    ),
];