<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Тестовое задание для 1С");
?>
Проверка AJAX: echo time(); = <?php echo time();?>
<br />

<?php
$APPLICATION->IncludeComponent(
	"1c:user.list", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SORT_BY" => "ID",
		"SORT_ORDER" => "ASC",
		"PAGE_ELEMENT_COUNT" => "5",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "LOGIN",
			3 => "EMAIL",
			4 => "TIMESTAMP_X",
			5 => "",
		),
		"EXPORT_ELEMENT_COUNT" => "1000",
		"CHECK_GROUP_REG" => "Y",
		"CHECK_GROUP_ID" => array(
			0 => "6",
			1 => "",
		)
	),
	false
);
?>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>