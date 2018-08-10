<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Config;
use travelsoft\sqlimporttools\Tools;
use travelsoft\sqlimporttools\Files;

/**
 * Класс экспорта отелей
 *
 * @author dimabresky
 */
class Hotels extends Exporter {

    /**
     * @return boolean
     */
    public function startExport() {
        return self::_startExport("hotels", $this, function ($context) {

                    $existsIblockHotels = $context->_getIblockHotelsId();
                    $existsIblockCountries = $context->_getIblockCountriesId();
                    $existsIblockCities = $context->_getIblockCitiesId();

                    $files = new Files;

                    foreach ($context->_db_rows as $arRow) {

                        $arProperties = [
                            "DESC" => ["VALUE" => ["TYPE" => "HTML", "TEXT" => str_replace("\\n", "<br>", $arRow["description"])]],
                            "HOTELKEY" => $arRow["mthotelId"],
                            "TOWN" => isset($existsIblockCities[$arRow["cityId"]]) ? $existsIblockCities[$arRow["cityId"]] : 0,
                            "COUNTRY" => isset($existsIblockCountries[$arRow["countryId"]]) ? $existsIblockCountries[$arRow["countryId"]] : 0,
                            "STARS" => $arRow["starsCount"],
                            "ADDRESS" => $arRow["hotelAddress"],
                            "MAP" => [$arRow["hotelLatitude"], $arRow["hotelLongitude"]],
                            "CHECKIN_FROM" => $arRow["checkinFrom"],
                            "CHECKIN_UNTIL" => $arRow["checkinUntil"],
                            "CHECKOUT_FROM" => $arRow["checkoutFrom"],
                            "CHECKOUT_UNTIL" => $arRow["checkoutUntil"],
                            "POLICY" => $context->_getPoliciesByXML_ID(Tools::extractStringLikeArray($arRow["policyId"])),
                            "SERVICES" => $context->_getFacilitiesByXML_ID(Tools::extractStringLikeArray($arRow["policyId"])),
                            "XML_URL" => $arRow["hotelUrl"]
                        ];

                        $arImages = $files->downloadHotelImages(Tools::extractStringLikeArray($arRow["hotelUrlsList"]), $existsIblockHotels[$arRow["hotelId"]]);
                        $arImages2Save = [];
                        if (!empty($arImages)) {

                            foreach ($arImages as $k => $path) {
                                $arImages2Save["n$k"] = $files->getFileUploadArray($path);
                            }
                        }

                        if (isset($existsIblockHotels[$arRow["hotelId"]])) {

                            // try update
                            $is_updated = $context->Update($existsIblockHotels[$arRow["hotelId"]], [
                                "NAME" => $arRow["hotelName"],
                                "XML_ID" => $arRow["hotelId"],
                                "CODE" => Tools::translit($arRow["hotelName"])
                            ]);

                            if ($is_updated) {

                                foreach ($arProperties as $code => $value) {
                                    $context->_iblock_element_object->SetPropertyValuesEx($existsIblockHotels[$arRow["hotelId"]], Config::HOTELS_IBLOCK_ID, [$code => $value]);
                                }

                                // попатка перезалить фото
                                while ($arPicture = $context->_iblock_element_object->GetProperty(Config::HOTELS_IBLOCK_ID, $existsIblockHotels[$arRow["hotelId"]], 'ID', 'DESC', array('CODE' => "PICTIRES"))->Fetch()) {
                                    $context->_iblock_element_object->SetPropertyValueCode($existsIblockHotels[$arRow["hotelId"]], "PICTURES", array($arPicture["PROPERTY_VALUE_ID"] => array('del' => 'Y')));
                                }
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try update " . $arRow["hotelName"] . "]";
                            }

                            $ID = $existsIblockHotels[$arRow["hotelId"]];
                        } else {

                            // try add
                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::HOTELS_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "NAME" => $arRow["hotelName"],
                                "XML_ID" => $arRow["hotelId"],
                                "CODE" => Tools::translit($arRow["hotelName"]),
                                "PROPERTY_VALUES" => $arProperties
                            ]);

                            if ($ID > 0) {
                                $existsIblockHotels[$arRow["hotelId"]] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["hotelName"] . "]";
                            }
                        }

                        if ($ID > 0) {
                            foreach ($arImages2Save as $arImage2Save) {
                                $context->_iblock_element_object->SetPropertyValueCode($existsIblockHotels[$arRow["hotelId"]], "PICTURES", $arImage2Save);
                            }
                        }
                    }
                });
    }

    /**
     * @staticvar array $arPolicies
     * @param array $XML_IDs
     * @return array
     */
    protected function _getPoliciesByXML_ID(array $XML_IDs) {

        static $arPolicies = null;

        $arIblockPolicies = [];

        if (!$arPolicies) {
            $arPolicies = $this->_getIblockPoliciesId();
        }

        foreach ($XML_IDs as $XML_ID) {
            if (isset($arPolicies[$XML_ID])) {
                $arIblockPolicies[] = $arPolicies[$XML_ID];
            }
        }

        return $arIblockPolicies;
    }

    /**
     * @staticvar array $arFacilities
     * @param array $XML_IDs
     * @return array
     */
    protected function _getFacilitiesByXML_ID(array $XML_IDs) {

        static $arFacilities = null;

        $arIblockFacilities = [];

        if (!$arFacilities) {
            $arFacilities = $this->_getIblockFacilitiesId();
        }

        foreach ($XML_IDs as $XML_ID) {
            if (isset($arFacilities[$XML_ID])) {
                $arIblockFacilities[] = $arFacilities[$XML_ID];
            }
        }

        return $arIblockFacilities;
    }

}
