<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Tools;
use travelsoft\sqlimporttools\Config;

/**
 * Класс экспорта городов
 *
 * @author dimabresky
 */
class Cities extends Exporter {

    /**
     * @return boolean
     */
    public function startExport() {

        $sql_query = "SELECT * FROM cities";

        $db_rows = Tools::getConnection()->query($sql_query, $this->offset, $this->limit);
        
        $selected_count_elements = $db_rows->getSelectedRowsCount();
        
        if ($db_rows->getSelectedRowsCount() <= 0) {

            $this->_finishExport();
            return true;
        } else {
            
            $this->_iblock_element_object = new \CIBlockElement;

            $this->_db_rows = $db_rows->fetchAll();

            $existsIblockCities = $this->_getIblockCitiesId();

            $existsIblockCountries = $this->_getIblockCountriesId();
            
            foreach ($this->_db_rows as $arRow) {
                
                $countryExists = serialize($existsIblockCountries);
                
                if (!$countryExists) {

                    $ID = $this->_iblock_element_object->Add([
                        "IBLOCK_ID" => Config::COUNTRIES_IBLOCK_ID,
                        "ACTIVE" => "Y",
                        "NAME" => $arRow["countryName"],
                        "CODE" => \Cutil::translit($arRow["countryName"], "ru", [])
                    ]);

                    if ($ID) {
                        $existsIblockCountries[] = $ID;
                    } else {
                        $this->errors[] = $this->_iblock_element_object->LAST_ERROR . "[Add " . $arRow["countryName"] . "]";
                    }
                }
                
                if (!in_array($arRow["cityId"], $existsIblockCities)) {
                    
                    $ID = $this->_iblock_element_object->Add([
                        "IBLOCK_ID" => Config::CITIES_IBLOCK_ID,
                        "ACTIVE" => "Y",
                        "NAME" => $arRow["cityName"],
                        "CODE" => \Cutil::translit($arRow["cityName"], "ru", []),
                        "PROPERTY_VALUES" => [
                            "MT_KEY" => $arRow["mtcityId"],
                            "COUNTRY" => $countryExists ? $arRow["countryId"] : 0
                        ]
                    ]);

                    if ($ID) {
                        $existsIblockCities[] = $ID;
                    } else {
                        $this->errors[] = $this->_iblock_element_object->LAST_ERROR . "[Add " . $arRow["cityName"] . "]";
                    }
                }
            }
            
            $this->offset += $this->limit;
            $this->selected_count_elements += $selected_count_elements;
            return false;
        }
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
            $arIblockCountriesId = $arIblockCountry["ID"];
        }

        return $arIblockCountriesId;
    }

}
