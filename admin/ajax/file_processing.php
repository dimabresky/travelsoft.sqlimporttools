<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

set_time_limit(0);

global $USER;

\Bitrix\Main\Loader::includeModule("travelsoft.sqlparsertools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if (
        !$USER->isAdmin() ||
        !check_bitrix_sessid() ||
        !file_exists(travelsoft\sqlparsertools\Config::getAbsUploadSqlFilePath($request->get("file_name"))) ||
        !in_array($request->get("action"), ["upload_file_in_db", "export_db"])
) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die();
}

switch ($request->get("action")) {

    case "upload_file_in_db":

        $connectionPool = \Bitrix\Main\Application::getInstance()->getConnectionPool();

        $connectionPool->setConnectionParameters("hotels_booking", [
            'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
            'host' => 'localhost:31006',
            'database' => 'hotels_booking',
            'login' => 'hotels_b',
            'password' => 'qw23QW@#',
            'options' => 2.0,
        ]);
        
        $result = $connectionPool->getConnection("hotels_booking")->executeSqlBatch(file_get_contents(travelsoft\sqlparsertools\Config::getAbsUploadSqlFilePath($request->get("file_name"))));
        
        sendResponse(empty($result), 50, $result);
        
        break;
    
    case "export_db":
        
        sendResponse(true, 100);
        
        break;
}

/**
 * @param bool $isOk
 * @param int $progress
 * @param array $errors
 */
function sendResponse(bool $isOk = false, int $progress = null, array $errors = []) {
    
    if ($isOk) {
        $message = "Экспорт выгруженных данных в инфоблоки...";
        if ($progress === 100) {
            $message = "Обработка прошла успешно";
        }
        CAdminMessage::ShowMessage(array(
            "MESSAGE" => $message,
            "DETAILS" => "#PROGRESS_BAR#",
            "HTML" => true,
            "TYPE" => "PROGRESS",
            "PROGRESS_TOTAL" => 100,
            "PROGRESS_VALUE" => $progress,
        ));
    } else {
        CAdminMessage::ShowMessage(implode("<br>", $errors));
    }
    die();
}