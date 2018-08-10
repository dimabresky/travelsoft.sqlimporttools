<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Config;
use travelsoft\sqlimporttools\Tools;
/**
 * Класс экспорта удобств в отелях
 *
 * @author dimabresky
 */
class Facilities extends Exporter {

    /**
     * @var string
     */
    public $short_export_name = "facilities";

    /**
     * @return boolean
     */
    public function startExport() {

        return self::_startExport("hotelsFacilities", $this, function ($context) {

                    $existsIblockFacilities = $context->_getIblockFacilitiesId();
                    $existsIblockFacilitiesType = $context->_getIblockFacilitiesTypeId();

                    foreach ($context->_db_rows as $arRow) {

                        $facilityTypeExists = isset($existsIblockFacilitiesType[$arRow["hotelFacilityTypeId"]]);

                        if ($facilityTypeExists) {

                            // try update
                            $is_updated = $context->_iblock_element_object->Update($existsIblockFacilitiesType[$arRow["hotelFacilityTypeId"]], [
                                "NAME" => $arRow["facilityTypeName"],
                                "CODE" => Tools::translit($arRow["facilityTypeName"])
                            ]);
                            
                            if (!$is_updated) {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try update NAME field " . $arRow["facilityTypeName"] . "]";
                            }
                            
                        } else {

                            // try add
                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::FACILITIES_TYPE_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "CODE" => Tools::translit($arRow["facilityTypeName"]),
                                "NAME" => $arRow["facilityTypeName"],
                                "XML_ID" => $arRow["hotelFacilityTypeId"]
                            ]);
                            
                            if ($ID > 0) {
                                $existsIblockFacilitiesType[$arRow["hotelFacilityTypeId"]] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["facilityTypeName"] . "]";
                            }
                        }

                        if (isset($existsIblockFacilities[$arRow["facilityId"]])) {
                            
                            // try update
                            $is_updated = $context->_iblock_element_object->Update($existsIblockFacilities[$arRow["facilityId"]], [
                                "NAME" => $arRow["facilityName"],
                                "CODE" => Tools::translit($arRow["facilityName"]),
                                "XML_ID" => $arRow["facilityId"],
                            ]);
                            
                            if (!$is_updated) {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try update NAME field " . $arRow["facilityName"] . "]";
                            }
                            
                            $context->_iblock_element_object->SetPropertyValuesEx($existsIblockFacilities[$arRow["facilityId"]], Config::FACILITIES_IBLOCK_ID, ["SERVICE_TYPE" => $facilityTypeExists ? $existsIblockFacilitiesType[$arRow["hotelFacilityTypeId"]] : ""]);
                            
                        } else {

                            // try add
                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::FACILITIES_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "CODE" => Tools::translit($arRow["facilityName"]),
                                "NAME" => $arRow["facilityName"],
                                "XML_ID" => $arRow["facilityId"],
                                "PROPERTY_VALUES" => [
                                    "SERVICE_TYPE" => $facilityTypeExists ? $existsIblockFacilitiesType[$arRow["hotelFacilityTypeId"]] : ""
                                ]
                            ]);
                            
                            if ($ID > 0) {
                                $existsIblockFacilities[$arRow["facilityId"]] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["facilityName"] . "]";
                            }
                        }
                    }
                });
    }
}
