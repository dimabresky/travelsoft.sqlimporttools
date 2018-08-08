<?php

namespace travelsoft\sqlparsertools\export;

/**
 * Абстрактный класс экспорта
 *
 * @author dimabresky
 */
abstract class Exporter {
    abstract public static function export();
}
