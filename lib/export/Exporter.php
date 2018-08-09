<?php

namespace travelsoft\sqlparsertools\export;

\Bitrix\Main\Loader::includeModule("iblock");

/**
 * Абстрактный класс экспорта
 *
 * @author dimabresky
 */
abstract class Exporter {
    
    /**
     * @var array
     */
    public $errors = [];
    
    /**
     * @var \CIBlockElement
     */
    protected $_iblock_element_object = NULL;
    
    /**
     * @var array
     */
    protected $_db_rows = [];
    
    /**
     * @var int
     */
    public $selected_count_elements = NULL;
    /**
     * @var int 
     */
    public $limit = 200;
    
    /**
     * @var int
     */
    public $offset = 0;
    
    abstract public function startExport();
    
    public function __construct() {
        if (!isset($_SESSION["travelosft_sqlparsertools_offset_db_query"])) {
            $_SESSION["travelosft_sqlparsertools_offset_db_query"] = 0;
        }
        if (!isset($_SESSION["travelosft_sqlparsertools_selected_count_elements_db_query"])) {
            $_SESSION["travelosft_sqlparsertools_offset_db_query"] = 0;
        }
        $this->offset = &$_SESSION["travelosft_sqlparsertools_offset_db_query"];
        $this->selected_count_elements = &$_SESSION["travelosft_sqlparsertools_selected_count_elements_db_query"];
    }
    
    protected function _finishExport () {
        $this->offset = 0;
        $this->selected_count_elements = 0;
    }
}
