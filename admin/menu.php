<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_services",
    "section" => "travelsoft_sqlparsertools",
    "sort" => 10000,
    "text" => GetMessage("travelsoft_sqlparsertools_MENU_TITLE"),
    "title" => GetMessage("travelsoft_sqlparsertools_MENU_TITLE"),
    "icon" => "travelsoft_sqlparsertoolsdb-20x20",
    "page_icon" => "travelsoft_sqlparsertoolsdb",
    "items_id" => "menu_travelsoft_sqlparsertools",
    "url" => "travelsoft_sqlparsertools.php?lang=" . LANGUAGE_ID,
    "more_url" => ["travelsoft_sqlparsertools_process.php?lang=" . LANGUAGE_ID]
);

return $aMenu;
?>