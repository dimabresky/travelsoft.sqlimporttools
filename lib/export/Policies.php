<?php

namespace travelsoft\sqlimporttools\export;

use travelsoft\sqlimporttools\Config;
use travelsoft\sqlimporttools\Tools;

/**
 * Класс экспорта условий проживания
 *
 * @author dimabresky
 */
class Policies extends Exporter {

    /**
     * @var string
     */
    public $short_export_name = "policies";

    public function startExport() {

        return $this->_startExport("hotelspolicies", $this, function (Exporter $context) {

                    $existsIblockPolicies = $context->_getIblockPoliciesId();

                    foreach ($context->_db_rows as $arRow) {

                        if (isset($existsIblockPolicies[$arRow["policyId"]])) {

                            // try update
                            $is_updated = $context->_iblock_element_object->Update($existsIblockPolicies[$arRow["policyId"]], [
                                "NAME" => $arRow["policyName"],
                                "CODE" => Tools::translit($arRow["policyName"])
                            ]);

                            if (!$is_updated) {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try update NAME field " . $arRow["policyName"] . "]";
                            }

                            $context->_iblock_element_object->SetPropertyValuesEx($existsIblockPolicies[$arRow["policyId"]], Config::POLICIES_IBLOCK_ID, ["DESC" => ["VALUE" => ["TYPE" => "HTML", "TEXT" => str_replace("\\n", "<br>", $arRow["policyText"])]]]);
                            $context->_iblock_element_object->SetPropertyValuesEx($existsIblockPolicies[$arRow["policyId"]], Config::POLICIES_IBLOCK_ID, ["TYPE" => $arRow["policyType"]]);
                        } else {

                            // try add
                            $ID = $context->_iblock_element_object->Add([
                                "IBLOCK_ID" => Config::POLICIES_IBLOCK_ID,
                                "XML_ID" => $arRow["policyId"],
                                "CODE" => Tools::translit($arRow["policyName"]),
                                "ACTIVE" => "Y",
                                "NAME" => $arRow["policyName"],
                                "PROPERTY_VALUES" => [
                                    "TYPE" => $arRow["policyType"],
                                    "DESC" => ["TYPE" => "HTML", "TEXT" => str_replace("\\n", "<br>", $arRow["policyText"])]
                                ]
                            ]);

                            if ($ID > 0) {
                                $existsIblockPolicies[$arRow["policyId"]] = $ID;
                            } else {
                                $context->errors[] = Tools::prepare2Log($context->_iblock_element_object->LAST_ERROR) . "[try add " . $arRow["policyName"] . "]";
                            }
                        }
                    }
                });
    }
}
