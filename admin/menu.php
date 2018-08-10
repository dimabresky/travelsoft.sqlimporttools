<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_services",
    "section" => "travelsoft_sqlimporttools",
    "sort" => 10000,
    "text" => GetMessage("travelsoft_sqlimporttools_MENU_TITLE"),
    "title" => GetMessage("travelsoft_sqlimporttools_MENU_TITLE"),
    "icon" => "travelsoft_sqlimporttoolsdb-20x20",
    "page_icon" => "travelsoft_sqlimporttoolsdb",
    "items_id" => "menu_travelsoft_sqlimporttools",
    "url" => "travelsoft_sqlimporttools.php?lang=" . LANGUAGE_ID,
    "more_url" => ["travelsoft_sqlimporttools_process.php?lang=" . LANGUAGE_ID]
);

return $aMenu;
?>