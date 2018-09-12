<?php

@set_time_limit(0);
@ini_set('session.gc_maxlifetime', 2400);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_CRONTAB", true);
define('BX_WITH_ON_AFTER_EPILOG', true);
define('BX_NO_ACCELERATOR_RESET', true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule("travelsoft.sqlimporttools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$controller = new \travelsoft\sqlimporttools\ajax\Controller($request);

$controller->dispatch();