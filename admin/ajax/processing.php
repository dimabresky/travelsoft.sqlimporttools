<?php

set_time_limit(0);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Loader::includeModule("travelsoft.sqlimporttools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$controller = new \travelsoft\sqlimporttools\ajax\Controller($request);

$controller->dispatch();