<?php

namespace travelsoft\sqlimporttools\ajax;

use travelsoft\sqlimporttools\Tools;
use travelsoft\sqlimporttools\Config;

/**
 * Контроллер ajax запросов
 *
 * @author dimabresky
 */
class Controller {

    /**
     * @var \Bitrix\Main\Request
     */
    protected $_request = NULL;
    protected static $_actions = [
        "import_sql" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::importSql",
        "cities_export" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::citiesExport",
        "policies_export" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::policiesExport",
        "facilities_export" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::facilitiesExport",
        "hotels_export" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::hotelsExport",
        "rooms_export" => "\\travelsoft\\sqlimporttools\\ajax\\Actions::roomsExport",
    ];

    public function __construct(\Bitrix\Main\Request $request) {
        $this->_request = $request;
    }

    /**
     * @global \CUser $USER
     */
    public function dispatch() {

        global $USER;

        try {

            $action = (string) $this->_request->get("action");

            if (
                    !$USER->isAdmin() ||
                    !\check_bitrix_sessid() ||
                    !$this->_actionExists($action) ||
                    !$this->_request->isAjaxRequest()
            ) {

                throw new \Exception("Ooops, comrads. Some error. Please, try again later.");
            }

            $this->_runAction($action, (array) $this->_request->get("parameters"));
        } catch (\Exception $ex) {

            Tools::sendResponse(200, function () use ($ex) {

                ob_start();

                \CAdminMessage::ShowMessage($ex->getMessage());

                return \json_encode(["html" => ob_get_clean(), "error" => true]);
            });
        }
    }

    /**
     * Запускает обработчик действия
     * @param string $action
     * @param array $parameters
     */
    protected function _runAction(string $action, array $parameters = []) {

        \call_user_func_array(\str_replace("\\\\", "\\", self::$_actions[$action]), [$parameters, $action]);
    }

    /**
     * Проверка существования обработчика
     * @param string $action
     * @return boolean
     */
    protected function _actionExists(string $action) {
        return isset(self::$_actions[$action]);
    }

    /**
     * @param string $action
     * @return int
     */
    public static function getProgressForAction(string $action) {

        $actions = \array_keys(self::$_actions);

        $progress = (ceil(Config::PROGRESS_TOTAL / count($actions))) * (\array_search($action, $actions) + 1);

        return $progress > Config::PROGRESS_TOTAL ? Config::PROGRESS_TOTAL : $progress;
    }

    /**
     * @param string $action
     * @return string
     */
    public static function getNextAction(string $action) {

        $actions = \array_keys(self::$_actions);

        $next_action = $actions[\array_search($action, $actions) + 1];

        return $next_action ?: "";
    }

}
