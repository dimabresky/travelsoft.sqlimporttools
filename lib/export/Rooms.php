<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Tools;
use travelsoft\sqlimporttools\Config;
use travelsoft\sqlimporttools\Files;
/**
 * Класс экспорта номеров
 *
 * @author dimabresky
 */
class Rooms extends Exporter {
    
    /**
     * @var string
     */
    public $short_export_name = "rooms";
    
    public function startExport () {
        return self::_startExport("hotelRooms", $this, function ($context) {

                    $existsIblockHotels = $context->_getIblockHotelsId();
                    
                    $existsIblockRooms = $context->_getIblockRoomsId();
                    
                    $files = new Files;

                    foreach ($context->_db_rows as $arRow) {

                        $arProperties = [
                            "DESCRIPTION" => ["VALUE" => ["TYPE" => "HTML", "TEXT" => str_replace("\\n", "<br>", $arRow["blockText"])]],
                            "SQUARE" => $arRow["roomSurfaceM2"],
                            "FACILITIES" => Tools::extractStringLikeArray($arRow["facilities"]),
                            "HOTEL" => isset($existsIblockHotels[$arRow["hotelId"]]) ? $existsIblockHotels[$arRow["hotelId"]] : 0
                        ];

//                        $arImages = $files->downloadHotelImages(Tools::extractStringLikeArray($arRow["hotelUrlsList"]), $arRow["hotelId"]);
//                        $arImages2Save = [];
//                        if (!empty($arImages)) {
//
//                            foreach ($arImages as $k => $path) {
//                                $arImages2Save["n$k"] = $files->getFileUploadArray($path);
//                            }
//                        }

                        if (isset($existsIblockRooms[$arRow["roomId"]])) {

                            // try update
                            $is_updated = $context->_iblock_element_object->Update($existsIblockRooms[$arRow["roomId"]], [
                                "NAME" => $arRow["roomName"],
                                "XML_ID" => $arRow["roomId"],
                                "CODE" => Tools::translit($arRow["roomName"]) . "_" . $arRow["roomId"]
                            ]);

                            if ($is_updated) {

                                foreach ($arProperties as $code => $value) {
                                    $context->_iblock_element_object->SetPropertyValuesEx($existsIblockRooms[$arRow["roomId"]], Config::ROOMS_IBLOCK_ID, [$code => $value]);
                                }

                                // удаление фото
//                                while ($arPicture = $context->_iblock_element_object->GetProperty(Config::ROOMS_IBLOCK_ID, $existsIblockRooms[$arRow["roomId"]], 'ID', 'DESC', array('CODE' => "PICTIRES"))->Fetch()) {
//                                    $context->_iblock_element_object->SetPropertyValueCode($existsIblockRooms[$arRow["roomId"]], "PICTURES", array($arPicture["PROPERTY_VALUE_ID"] => array('del' => 'Y')));
//                                }
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try update " . $arRow["roomName"] . "]";
                            }

                            $ID = $existsIblockRooms[$arRow["roomId"]];
                        } else {

                            // try add
                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::ROOMS_IBLOCK_ID,
                                "ACTIVE" => "Y",
                                "NAME" => $arRow["roomName"],
                                "XML_ID" => $arRow["roomId"],
                                "CODE" => Tools::translit($arRow["roomName"]) . "_" . $arRow["roomId"],
                                "PROPERTY_VALUES" => $arProperties
                            ]);

                            if ($ID > 0) {
                                $existsIblockRooms[$arRow["roomId"]] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["roomName"] . "]";
                            }
                        }

//                        if ($ID > 0) {
//                            foreach ($arImages2Save as $arImage2Save) {
//                                $context->_iblock_element_object->SetPropertyValueCode($existsIblockHotels[$arRow["hotelId"]], "PICTURES", $arImage2Save);
//                            }
//                        }
                    }
                });
    }
    
    /**
     * @return array
     */
    protected function _getDbRoomsId() {
        return \array_values(\array_map(function ($arRow) {
                    return $arRow["roomId"];
                }, $this->_db_rows));
    }
    
    /**
     * @return array
     */
    protected function _getIblockRoomsId() {

        $arIblockRoomsId = [];

        $iblockRooms = $this->_iblock_element_object->GetList(false, ["XML_ID" => $this->_getDbRoomsId(), "IBLOCK_ID" => Config::ROOMS_IBLOCK_ID], false, false, ["ID", "XML_ID"]);

        while ($arIblockRoom = $iblockRooms->Fetch()) {
            $arIblockRoomsId[$arIblockRoom["XML_ID"]] = $arIblockRoom["ID"];
        }

        return $arIblockRoomsId;
    }
}
