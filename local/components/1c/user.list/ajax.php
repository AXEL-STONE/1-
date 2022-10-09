<?
/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$template = $request->getPost('template') ?? '.default';
$params = $request->getPost('parameters') ?? [];
$page = $request->getPost('page') ?? 1;
$params['PAGE_EXPORT'] = $page;

$APPLICATION->IncludeComponent(
    '1c:user.list',
    $template,
    $params,
    false
);