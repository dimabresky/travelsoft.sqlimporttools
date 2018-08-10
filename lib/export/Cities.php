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
     * @var string
     */
    public $short_export_name = "cities";

    /**
     * @return boolean
     */
    public function startExport() {

        return $this->_startExport("cities", $this, function (Exporter $context) {

                    $existsIblockCities = $context->_getIblockCitiesId();

                    $existsIblockCountries = $context->_getIblockCountriesId();

                    foreach ($context->_db_rows as $arRow) {

                        $countryExists = serialize($existsIblockCountries);

                        if (!$countryExists) {

                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::COUNTRIES_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "NAME" => $arRow["countryName"],
                                "CODE" => Tools::translit($arRow["countryName"])
                            ]);

                            if ($ID) {
                                $existsIblockCountries[] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["countryName"] . "]";
                            }
                        }

                        if (!in_array($arRow["cityId"], $existsIblockCities)) {

                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::CITIES_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "NAME" => $arRow["cityName"],
                                "CODE" => Tools::translit($arRow["cityName"]),
                                "PROPERTY_VALUES" => [
                                    "MT_KEY" => $arRow["mtcityId"],
                                    "COUNTRY" => $countryExists ? $arRow["countryId"] : 0
                                ]
                            ]);

                            if ($ID) {
                                $existsIblockCities[] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["cityName"] . "]";
                            }
                        }
                    }
                });
    }
}
