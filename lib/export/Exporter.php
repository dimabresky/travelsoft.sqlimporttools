<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Config;
use travelsoft\sqlimporttools\Tools;

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
    public $limit = 0;

    /**
     * @var int
     */
    public $offset = 0;

    abstract public function startExport();

    public function __construct() {
        if (!isset($_SESSION["travelosft_sqlimporttools_offset_db_query"])) {
            $_SESSION["travelosft_sqlimporttools_offset_db_query"] = 0;
        }
        if (!isset($_SESSION["travelosft_sqlimporttools_selected_count_elements_db_query"])) {
            $_SESSION["travelosft_sqlimporttools_offset_db_query"] = 0;
        }
        $this->limit = Config::ROWS_LIMIT_DB_QUERY;
        $this->offset = &$_SESSION["travelosft_sqlimporttools_offset_db_query"];
        $this->selected_count_elements = &$_SESSION["travelosft_sqlimporttools_selected_count_elements_db_query"];
        $this->_iblock_element_object = new \CIBlockElement;
    }

    public function getLogErrors() {

        $log_errors = "";
        if (!empty($this->errors)) {

            $log_errors .= "\r\nSome problems:\r\n";
            $log_errors .= implode("\r\n", \array_values(\array_map(function ($error) {
                                return "Iblock error: " . $error;
                            }, $this->errors)));
        }

        return $log_errors;
    }
        
    /**
     * 
     * @param string $table_name
     * @param callable $call
     * @return boolean
     */
    public function _startExport(string $table_name, Exporter $context, callable $call) {
        
        $sql_query = "SELECT * FROM " . $table_name;

        $db_rows = Tools::getConnection()->query($sql_query, $this->offset, $this->limit);

        $selected_count_elements = $db_rows->getSelectedRowsCount();

        if ($db_rows->getSelectedRowsCount() <= 0) {

            $context->_finishExport();
            return true;
        } else {
            
            $context->_db_rows = $db_rows->fetchAll();
            
            $call($context);
            
            $context->offset += $this->limit;
            $context->selected_count_elements += $selected_count_elements;
            return false;
        }
    }
    
    /**
     * @return array
     */
    protected function _getDbPoliciesId() {
        return \array_values(\array_map(function ($arRow) {
                    return $arRow["policyId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getIblockPoliciesId(array $XML_IDs = []) {

        $arIblockPliciesId = [];

        $iblockPolicies = $this->_iblock_element_object->GetList(false, ["XML_ID" => empty($XML_IDs) ? $this->_getDbPoliciesId() : $XML_IDs, "IBLOCK_ID" => Config::POLICIES_IBLOCK_ID], false, false, ["ID", "XML_ID"]);

        while ($arIblockPolicy = $iblockPolicies->Fetch()) {
            $arIblockPliciesId[$arIblockPolicy["XML_ID"]] = $arIblockPolicy["ID"];
        }

        return $arIblockPliciesId;
    }
    
    

    /**
     * @return array
     */
    protected function _getDbCitiesId() {

        return \array_values(\array_map(function ($arRow) {
                    return $arRow["cityId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getDbCountriesId() {

        return \array_values(\array_map(function ($arRow) {
                    return $arRow["countryId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getIblockCitiesId() {

        $arIblockCitiesId = [];

        $iblockCitites = $this->_iblock_element_object->GetList(false, ["ID" => $this->_getDbCitiesId(), "IBLOCK_ID" => Config::CITIES_IBLOCK_ID], false, false, ["ID"]);

        while ($arIblockCity = $iblockCitites->Fetch()) {
            $arIblockCitiesId[] = $arIblockCity["ID"];
        }

        return $arIblockCitiesId;
    }

    /**
     * @return array
     */
    protected function _getIblockCountriesId() {

        $arIblockCountriesId = [];

        $iblockCountries = $this->_iblock_element_object->GetList(false, ["ID" => $this->_getDbCountriesId(), "IBLOCK_ID" => Config::COUNTRIES_IBLOCK_ID], false, false, ["ID"]);

        while ($arIblockCountry = $iblockCountries->Fetch()) {
            $arIblockCountriesId[] = $arIblockCountry["ID"];
        }

        return $arIblockCountriesId;
    }
    
    /**
     * @return array
     */
    protected function _getDbFacilitiesId() {
        return \array_values(\array_map(function ($arRow) {
                    return $arRow["facilityId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getDbFacilitiesTypeId() {
        return \array_values(\array_map(function ($arRow) {
                    return $arRow["hotelFacilityTypeId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getIblockFacilitiesId(array $XML_IDs = []) {

        $arIblockFacilitiesId = [];

        $iblockFacilities = $this->_iblock_element_object->GetList(false, ["XML_ID" => empty($XML_IDs) ? $this->_getDbFacilitiesId(): $XML_IDs, "IBLOCK_ID" => Config::FACILITIES_IBLOCK_ID], false, false, ["ID", "XML_ID"]);

        while ($arIblockFacility = $iblockFacilities->Fetch()) {
            $arIblockFacilitiesId[$arIblockFacility["XML_ID"]] = $arIblockFacility["ID"];
        }

        return $arIblockFacilitiesId;
    }

    /**
     * @return array
     */
    protected function _getIblockFacilitiesTypeId() {

        $arIblockFacilitiesTypeId = [];

        $iblockFacilitiesType = $this->_iblock_element_object->GetList(false, ["XML_ID" => $this->_getDbFacilitiesTypeId(), "IBLOCK_ID" => Config::FACILITIES_TYPE_IBLOCK_ID], false, false, ["ID", "XML_ID"]);

        while ($arIblockFacilityType = $iblockFacilitiesType->Fetch()) {
            $arIblockFacilitiesTypeId[$arIblockFacilityType["XML_ID"]] = $arIblockFacilityType["ID"];
        }

        return $arIblockFacilitiesTypeId;
    }
    
    
    /**
     * @return array
     */
    protected function _getDbHotelsId() {
        return \array_values(\array_map(function ($arRow) {
                    return $arRow["hotelId"];
                }, $this->_db_rows));
    }

    /**
     * @return array
     */
    protected function _getIblockHotelsId() {

        $arIblockHotelsId = [];

        $iblockHotels = $this->_iblock_element_object->GetList(false, ["XML_ID" => $this->_getDbHotelsId(), "IBLOCK_ID" => Config::HOTELS_IBLOCK_ID], false, false, ["ID", "XML_ID"]);

        while ($arIblockHotel = $iblockHotels->Fetch()) {
            $arIblockHotelsId[$arIblockHotel["XML_ID"]] = $arIblockHotel["ID"];
        }

        return $arIblockHotelsId;
    }
    
    protected function _finishExport () {
        $this->offset = 0;
        $this->selected_count_elements = 0;
    }
}
