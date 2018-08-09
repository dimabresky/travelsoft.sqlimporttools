<?php

namespace travelsoft\sqlparsertools;

/**
 * Класс "инструментов"
 *
 * @author dimabresky
 */
class Tools {

    /**
     * Возвращает подключение к БД
     * @staticvar \Bitrix\Main\Data\Connection $connection
     * @return \Bitrix\Main\Data\Connection
     */
    public static function getConnection() {

        static $connection = NULL;

        if ($connection === NULL) {

            $connectionPool = \Bitrix\Main\Application::getInstance()->getConnectionPool();

            $connectionPool->setConnectionParameters(Config::DB_NAME, [
                'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
                'host' => Config::DB_HOST,
                'database' => Config::DB_NAME,
                'login' => Config::DB_LOGIN,
                'password' => Config::DB_PASSWORD,
                'options' => 2.0,
            ]);

            $connection = $connectionPool->getConnection(Config::DB_NAME);
        }

        return $connection;
    }

    /**
     * Ответ обработки запроса
     * @param int $http_code
     * @param callable $callback_for_get_response_body должен возвращать json строку
     */
    public static function sendResponse(int $http_code = null, callable $callback_for_get_response_body = null) {

        $protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');

        switch ($http_code) {

            case 200:
                header('Content-Type: application/json; charset=' . \SITE_CHARSET);
                if ($callback_for_get_response_body) {
                    echo $callback_for_get_response_body();
                }
                die;
            case 500:
                header($protocol . ' 500 Internal Server Error', true, 500);
                die;
            case 400:
            default:
                header($protocol . " 404 Not Found");
                die;
        }
    }

}
