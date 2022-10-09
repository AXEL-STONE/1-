<?php

use Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Application,
    Bitrix\Main\UI\PageNavigation,
    Bitrix\Main\SystemException,
    Bitrix\Main\GroupTable,
    Bitrix\Main\UserTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class TestTaskComponent extends \CBitrixComponent
{
    protected $cacheKeys = [];
    protected $cacheAddon = [];
    protected $availableFields = [];
    protected $returned;
    protected $userGroupID;
    const USER_GROUP_REG = 6;

    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * подготавливаем параметры
     */
    public function onPrepareComponentParams($params)
    {
        $result = [
            'PAGE_ELEMENT_COUNT' => intval($params['PAGE_ELEMENT_COUNT']) > 0 ? intval($params['PAGE_ELEMENT_COUNT']) : 10,
            'EXPORT_ELEMENT_COUNT' => intval($params['EXPORT_ELEMENT_COUNT']) > 0 ? intval($params['EXPORT_ELEMENT_COUNT']) : 1000,
            'PAGE_EXPORT' => intval($params['PAGE_EXPORT']) > 0 ? intval($params['PAGE_EXPORT']) : 1,
            'SORT_BY' => strlen($params['SORT_BY']) ? $params['SORT_BY'] : 'NAME',
            'SORT_ORDER' => $params['SORT_ORDER'] == 'ASC' ? 'ASC' : 'DESC',
            'CACHE_TYPE' => $params['CACHE_TYPE'] == 'Y' ? 'Y' : 'N',
            'EXPORT_DATA' => $params['EXPORT_DATA'] == 'Y',
            'CHECK_GROUP_REG' => $params['CHECK_GROUP_REG'] == 'Y',
            'CACHE_GROUPS' => $params['CACHE_GROUPS'] == 'Y' ? 'Y' : 'N',
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
            'AJAX' => $params['AJAX'] == 'N' ? 'N' : $_REQUEST['AJAX'] == 'Y' ? 'Y' : 'N',
            'FIELD_CODE' => is_array($params['FIELD_CODE']) && count($params['FIELD_CODE']) ? $params['FIELD_CODE'] : [
                'ID',
                'NAME',
                'LOGIN',
                'EMAIL',
                'TIMESTAMP_X',
            ],
            'CHECK_GROUP_ID' => is_array($params['CHECK_GROUP_ID']) && count($params['CHECK_GROUP_ID']) ? $params['CHECK_GROUP_ID'] : [USER_GROUP_REG],
        ];
        trimArr($result['FIELD_CODE']);
        trimArr($result['CHECK_GROUP_ID']);
        $nav = new PageNavigation('nav');
        $nav->allowAllRecords(false)
            ->setPageSize($result['PAGE_ELEMENT_COUNT'])
            ->initFromUri();
        $result['NAV_OBJECT'] = $nav;
        return $result;
    }

    /**
     * получим все доступные поля для вывода
     */
    public function getAllFields()
    {
        $entityFields = UserTable::getEntity()->getFields();
        foreach ($entityFields as $code => $ob) {
            $this->availableFields[$code] = $ob->getTitle();
        }
        unset($this->availableFields['PASSWORD']);
        unset($this->availableFields['CONFIRM_CODE']);
    }

    /**
     * проверка выбранных групп на существование
     */
    public function checkGroupParam()
    {
        if(count($this->arParams['CHECK_GROUP_ID'])) {
            $resGroup = GroupTable::getList([
                'select' => ['ID'],
                'filter' => ['=ID' => $this->arParams['CHECK_GROUP_ID']]
            ]);
            while ($group = $resGroup->fetch()) {
                $this->userGroupID[] = $group['ID'];
            }
            if(count($this->userGroupID) == 0) $this->arParams['CHECK_GROUP_REG'] = false;
        } else {
            $this->arParams['CHECK_GROUP_REG'] = false;
        }
    }

    /**
     * проверка параметров
     */
    public function checkParams()
    {
        if ($this->arParams['PAGE_ELEMENT_COUNT'] <= 0) {
            throw new SystemException(
                Loc::getMessage('PAGE_ELEMENT_COUNT_MIN'),
                500
            );
        }

        if ($this->arParams['PAGE_ELEMENT_COUNT'] > 100) {
            throw new SystemException(
                Loc::getMessage('PAGE_ELEMENT_COUNT_MAX'),
                500
            );
        }

        foreach ($this->arParams['FIELD_CODE'] as $code) {
            if(!isset($this->availableFields[$code])) {
                throw new SystemException(
                    Loc::getMessage('NOT_FIELD', ['#CODE#' => $code]),
                    500
                );
            }
        }

        if($this->arParams['EXPORT_DATA']) $this->exportData();
    }


    /**
     * выполняет действия перед кешированием
     */
    protected function executeProlog()
    {
        if ($this->arParams['PAGE_ELEMENT_COUNT'] > 0) {
            $this->cacheAddon = [$this->arParams['NAV_OBJECT']];
        }
        $this->cacheKeys = ['ITEMS','AVAILABLE_FIELD'];
    }

    /**
     * определяет читать данные из кеша или нет
     */
    protected function readDataFromCache()
    {
        global $USER;
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        if ($this->arParams['CACHE_GROUPS'] == 'Y') {
            if (is_array($this->cacheAddon)) {
                $this->cacheAddon[] = $USER->GetUserGroupArray();
            } else {
                $this->cacheAddon = [$USER->GetUserGroupArray()];
            }
        }
        return !($this->startResultCache(false, $this->cacheAddon, md5(serialize($this->arParams))));
    }

    /**
     * получение результатов
     */
    protected function getResult()
    {
        $filter = ['ACTIVE' => 'Y'];
        if($this->arParams['CHECK_GROUP_REG']) $filter['Bitrix\Main\UserGroupTable:USER.GROUP_ID'] = $this->userGroupID;

        $nav = new PageNavigation('nav');
        $nav->allowAllRecords(false)
            ->setPageSize($this->arParams['PAGE_ELEMENT_COUNT'])
            ->initFromUri();

        $userList = UserTable::getList(array(
            'select' => $this->arParams['FIELD_CODE'],
            'order' => [$this->arParams['SORT_BY'] => $this->arParams['SORT_ORDER']],
            'filter' => $filter,
            'count_total' => true,
            'offset' => $nav->getOffset(),
            'limit' => $nav->getLimit(),
        ));

        $nav->setRecordCount($userList->getCount());

        while($userData = $userList->fetch()) {
            $this->arResult['ITEMS'][] = $userData;
        }

        $this->arResult['NAV_OBJECT'] = $nav;
        $this->arResult['AVAILABLE_FIELD'] = $this->availableFields;
    }

    /**
     * выгрузка данных
     */
    public function exportData()
    {
        $filter = ['ACTIVE' => 'Y'];
        if($this->arParams['CHECK_GROUP_REG']) $filter['Bitrix\Main\UserGroupTable:USER.GROUP_ID'] = $this->userGroupID;

        $items = [];
        $cnt = UserTable::getCount($filter);
        $offset = ($this->arParams['PAGE_EXPORT'] - 1) * $this->arParams['EXPORT_ELEMENT_COUNT'];
        $userList = UserTable::getList(array(
            'select' => $this->arParams['FIELD_CODE'],
            'order' => [$this->arParams['SORT_BY'] => $this->arParams['SORT_ORDER']],
            'filter' => $filter,
            'offset' => $offset,
            'limit' => $this->arParams['EXPORT_ELEMENT_COUNT'],
        ));
        while($userData = $userList->fetch()) {
            $items[] = $userData;
        }

        $filename  = 'user_export.csv';
        $filePath = Application::getDocumentRoot().'/upload/tmp/'.$filename;

        if($this->arParams['PAGE_EXPORT'] == 1) {
            if(file_exists($filePath)) @unlink($filePath);
            $fp = fopen($filePath, 'w+');
            fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            $header = [];
            foreach ($this->arParams['FIELD_CODE'] as $field) {
                $header[] = $this->availableFields[$field];
            }
            fputcsv($fp, $header, ';');
        } else {
            $fp = fopen($filePath, 'a+');
        }

        foreach ($items as $arItem) {
            $line = [];
            foreach ($this->arParams['FIELD_CODE'] as $field) {
                $line[] = $arItem[$field];
            }
            fputcsv($fp, $line, ';');
        }
        fclose($fp);

        $totalCurrent = ($offset - $this->arParams['EXPORT_ELEMENT_COUNT'] +count($items));
        $stop = count($items) ? false : true;

        echo json_encode([
            'page' => $this->arParams['PAGE_EXPORT']+1,
            'sizeFile' => Loc::getMessage('FILE_SIZE_EXPORT', [
                '#CURRENT#' => number_format($totalCurrent,0,'',' '),
                '#TOTAL#' => number_format($cnt,0,'',' '),
                '#SIZE#' => CFile::FormatSize(filesize($filePath), 0),
            ]),
            'stop' => $stop,
            'file' => '/upload/tmp/'.$filename,
            'percent' => round(($offset - $this->arParams['EXPORT_ELEMENT_COUNT'] +count($items))/$cnt * 100),
        ]);
        die();
    }

    /**
     * кеширует ключи массива arResult
     */
    protected function putDataToCache()
    {
        if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0) {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    public function executeComponent()
    {
        global $APPLICATION;

        try {
            $this->getAllFields();
            if ($this->arParams['CHECK_GROUP_REG']) $this->checkGroupParam();
            $this->checkParams();
            $this->executeProlog();

            if ($this->arParams['AJAX'] == 'Y') {
                $APPLICATION->RestartBuffer();
            }

            if (!$this->readDataFromCache()) {
                $this->getResult();
                $this->putDataToCache();
                $this->includeComponentTemplate();
            }

            if ($this->arParams['AJAX'] == 'Y') {
                die();
            }

            return $this->returned;
        } catch (Exception $e) {
            throw new SystemException(
                $e->getMessage(),
                500
            );
        }
    }
}