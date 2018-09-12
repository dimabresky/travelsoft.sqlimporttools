<?php

namespace travelsoft\sqlimporttools\ajax;

use travelsoft\sqlimporttools\Config;
use Bitrix\Main\Localization\Loc;
use travelsoft\sqlimporttools\Tools;
use travelsoft\sqlimporttools\Logger;
use \travelsoft\sqlimporttools\export\Exporter;
use travelsoft\sqlimporttools\export\Cities as citiesExporter;
use travelsoft\sqlimporttools\export\Policies as policiesExporter;
use travelsoft\sqlimporttools\export\Facilities as facilitiesExporter;
use travelsoft\sqlimporttools\export\Hotels as hotelsExporter;
use travelsoft\sqlimporttools\export\Rooms as roomsExporter;

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
        $connection = Tools::getConnection();

        $result = null;
        $file = \fopen(Config::getAbsUploadSqlFilePath($parameters["sql_file_name"]), 'r');
        if ($file) {

            $char = $sql_batch = '';
            while (($char = \fgetc($file)) !== false) {
                $sql_batch .= $char;
                $PHP_EOL = \fgetc($file);
                $sql_batch .= $PHP_EOL;
                if ($char === Config::SQL_BATCH_DELEMITER && $PHP_EOL === \PHP_EOL) {
                    
                    $result = $connection->executeSqlBatch($sql_batch);
                    if (!empty($result)) {
                        $logMessage .= "\r\n";
                        $logMessage .= "some problems: \r\n";
                        $logMessage .= implode("\r\n", $result);
                    }
                    $sql_batch = '';
                }
                if ($PHP_EOL === false) {
                    break;
                }
            }

            $logMessage = "Import of sql file " . $parameters["sql_file_name"] . " is finished." . $logMessage;
        } else {

            $logMessage = "Sql file " . $parameters["sql_file_name"] . " not must be read.";
        }

        self::_totalFinish($action, true, $logMessage);
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function citiesExport(array $parameters, string $action) {

        $citiesExporter = new citiesExporter();

        $done = $citiesExporter->startExport();

        self::_totalFinish($action, $done, self::_totalLogMessage($citiesExporter, $done));
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function policiesExport(array $parameters, string $action) {

        $policiesExporter = new policiesExporter();

        $done = $policiesExporter->startExport();

        self::_totalFinish($action, $done, self::_totalLogMessage($policiesExporter, $done));
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function hotelsExport(array $parameters, string $action) {

        $hotelsExporter = new hotelsExporter();

        $done = $hotelsExporter->startExport();

        self::_totalFinish($action, $done, self::_totalLogMessage($hotelsExporter, $done));
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function roomsExport(array $parameters, string $action) {

        $roomsExporter = new roomsExporter();

        $done = $roomsExporter->startExport();

        self::_totalFinish($action, $done, self::_totalLogMessage($roomsExporter, $done));
    }

    /**
     * @param array $parameters
     * @param string $action
     */
    public static function facilitiesExport(array $parameters, string $action) {

        $facilitiesExporter = new facilitiesExporter();

        $done = $facilitiesExporter->startExport();

        self::_totalFinish($action, $done, self::_totalLogMessage($facilitiesExporter, $done));
    }
    
    public static function finishExport (array $parameters, string $action) {
        self::_totalFinish($action, true, "Script work is finished.");
    }
    
    /**
     * @param callable $callback
     * @param string $logMessage
     */
    protected static function _finish(callable $callback = null, string $logMessage = "") {

        (new Logger(Config::getAbsLogFilePath()))->write($logMessage);
        Tools::sendResponse(200, $callback);
    }

    /**
     * @param string $action
     * @param bool $done
     * @param string $logMessage
     */
    protected static function _totalFinish(string $action = "", bool $done = true, string $logMessage = "") {

        self::_finish(function () use ($action, $done) {

            $next_action = $done ? Controller::getNextAction($action) : $action;

            if ($next_action !== "") {
                $message = Loc::getMessage("travelsoft_sqlparser_tools_" . $next_action);
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
     * @param Exporter $exporter
     * @param bool $done
     * @return string
     */
    protected function _totalLogMessage(Exporter $exporter, bool $done = true) {

        $logMessage = "";
        if (!$done) {

            $logMessage = $exporter->short_export_name . " export step is finished (count: " . $exporter->selected_count_elements . ")." . $exporter->getLogErrors();
        } else {

            $logMessage = $exporter->short_export_name . " export is finished." . $exporter->getLogErrors();
        }

        return $logMessage;
    }

}
