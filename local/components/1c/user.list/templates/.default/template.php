<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$jsParams = [
    'componentPath' => $componentPath,
    'parameters' => [
        'SORT_BY' => $arParams['SORT_BY'],
        'SORT_ORDER' => $arParams['SORT_ORDER'],
        'FIELD_CODE' => $arParams['FIELD_CODE'],
        'EXPORT_DATA' => 'Y',
        'AJAX' => 'Y',
    ],
    'template' => $templateName,
];

?>
Проверка кэшировани: echo time(); = <?php echo time();?>
<br />
<main>
    <div class="row">
        <h4 class="mb-3"><?=Loc::getMessage('TITLE_TEMPLATE')?></h4>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="user-list-tab" data-bs-toggle="tab" data-bs-target="#user-list-tab-pane" type="button" role="tab" aria-controls="user-list-tab-pane" aria-selected="true"><?=Loc::getMessage('TAB_NAME_LIST')?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#export-user-tab-pane" type="button" role="tab" aria-controls="export-user-tab-pane" aria-selected="false"><?=Loc::getMessage('TAB_NAME_EXPORT')?></button>
                <a class="nav-link" href="#"></a>
            </li>
        </ul>
        <div class="tab-content" id="userTabContent">
            <div class="tab-pane fade show active" id="user-list-tab-pane" role="tabpanel" aria-labelledby="user-list-tab" tabindex="0">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <?php foreach ($arParams['FIELD_CODE'] as $field):?>
                            <th scope="col"><?=$arResult['AVAILABLE_FIELD'][$field]?></th>
                        <?php endforeach;?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($arResult["ITEMS"] as $arItem):?>
                        <tr>
                            <?php foreach ($arParams['FIELD_CODE'] as $field):?>
                                <td><?=$arItem[$field]?></td>
                            <?php endforeach;?>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <?php
                $APPLICATION->IncludeComponent(
                    "bitrix:main.pagenavigation",
                    "modern",
                    array(
                        "NAV_OBJECT" => $arResult['NAV_OBJECT'],
                        "SEF_MODE" => "N",
                    ),
                    false
                );
                ?>
            </div>
            <div class="tab-pane fade" id="export-user-tab-pane" role="tabpanel" aria-labelledby="export-user-tab" tabindex="0">
                <br />
                <button type="button" class="btn btn-primary" id="start-export"><?=Loc::getMessage('START_EXPORT')?></button>
                <button type="button" class="btn btn-danger" id="stop-export"  style="display:none;"><?=Loc::getMessage('STOP_EXPORT')?></button>
                <br />
                <br />
                <div class="alert alert-success" id="success-export" role="alert" style="display:none;">
                    <?=Loc::getMessage('STOP_EXPORT')?>
                </div>
                <div class="progress" id="progress-export" style="display:none;height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-export-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                <br />
                <div id="file-size-container" style="display:none;"></div>
            </div>
        </div>
    </div>
</main>
<script>
    new JCUserList(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
</script>
<?php unset($actualItem, $itemIds, $jsParams);?>