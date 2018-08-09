<?php

namespace travelsoft\sqlparsertools\ajax;

use travelsoft\sqlparsertools\Config;
use Bitrix\Main\Localization\Loc;
use travelsoft\sqlparsertools\Tools;
use travelsoft\sqlparsertools\Logger;
use travelsoft\sqlparsertools\export\Cities as citiesExporter;

Loc::loadMessages(__FILE__);

/**
 * Actions methods handler class
 *
 * @author dimabresky
 */
class Actions {

    /**
     * @param array $parameters
     * @param string $action
     * @throws \Exception
     */
    public static function importSql(array $parameters, string $action) {

        if (!isset($parameters["sql_file_name"]) || !\file_exists(Config::getAbsUploadSqlFilePath($parameters["sql_file_name"]))) {
            throw new \Exception(Loc::getMessage("travelsoft_sqlparser_tools_FILE_NOT_EXISTS", ["#file#" => $parameters["sql_file_name"]]));
        }

        $result = Tools::getConnection()->executeSqlBatch(
                \file_get_contents(Config::getAbsUploadSqlFilePath($parameters["sql_file_name"])));

        $logMessage = "Import of sql file " . $parameters["sql_file_name"] . " is finished.";

        if (!empty($result)) {
            $logMessage .= "\r\n";
            $logMessage .= "some problems: \r\n";
            $logMessage .= implode("\r\n", $result);
        }
        
        self::_finished(function () use ($action) {
            
            $next_action = Controller::getNextAction($action);
            
            if ($next_action !== "") {
                $message = Loc::getMessage("travelsoft_sqlparser_tools_" . $next_action);
            }
            
            ob_start();
            \CAdminMessage::ShowMessage(array(
                "MESSAGE" => $message,
                "DETAILS" => "#PROGRESS_BAR#",
                "HTML" => true,
                "TYPE" => "PROGRESS",
                "PROGRESS_TOTAL" => Config::PROGRESS_TOTAL,
                "PROGRESS_VALUE" => Controller::getProgressForAction($action),
            ));

            return \json_encode(["html" => ob_get_clean(), "error" => false, "next_action" => $next_action]);
        }, $logMessage);
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function citiesExport(array $parameters, string $action) {

        $citiesExporter = new citiesExporter();

        $done = $citiesExporter->startExport();

        $logMessage = "";
        if (!$done) {

            $logMessage = "Export citites step is finished (count: ".$citiesExporter->selected_count_elements.")." . $citiesExporter->getLogErrors();
        } else {

            $logMessage = "Export citites is finished." . $citiesExporter->getLogErrors();
        }
        
        self::_finished(function () use ($action, $done) {
            
            $next_action = $done ? "" : $action;
            
            if ($next_action !== "") {
                $message = Loc::getMessage("travelsoft_sqlparser_tools_". $next_action);
            } else {
                $message = Loc::getMessage("travelsoft_sqlparser_tools_ajax_process_is_done");
            }
            
            ob_start();
            \CAdminMessage::ShowMessage(array(
                "MESSAGE" => $message,
                "DETAILS" => "#PROGRESS_BAR#",
                "HTML" => true,
                "TYPE" => "PROGRESS",
                "PROGRESS_TOTAL" => Config::PROGRESS_TOTAL,
                "PROGRESS_VALUE" => Controller::getProgressForAction($action),
            ));

            return \json_encode(["html" => ob_get_clean(), "error" => false, "next_action" => $next_action]);
        }, $logMessage);

    }
    
    /**
     * @param \travelsoft\sqlparsertools\ajax\callable $callback
     * @param string $logMessage
     */
    protected static function _finished (callable $callback = null, string $logMessage = "") {
        
        (new Logger(Config::getAbsLogFilePath()))->write($logMessage);
        Tools::sendResponse(200, $callback);
    }
    
}
