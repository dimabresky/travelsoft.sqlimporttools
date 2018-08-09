<?php

$classes = array(
    
    "\\travelsoft\\sqlparsertools\\Config" => "lib/Config.php",
    "\\travelsoft\\sqlparsertools\\Files" => "lib/Files.php",
    "\\travelsoft\\sqlparsertools\\Tools" => "lib/Tools.php",
    "\\travelsoft\\sqlparsertools\\Logger" => "lib/Logger.php",
    "\\travelsoft\\sqlparsertools\\export\\Exporter" => "lib/export/Exporter.php",
    "\\travelsoft\\sqlparsertools\\export\\Cities" => "lib/export/Cities.php",
    "\\travelsoft\\sqlparsertools\\export\\Hotels" => "lib/export/Hotels.php",
    "\\travelsoft\\sqlparsertools\\export\\Facilities" => "lib/export/Facilities.php",
    "\\travelsoft\\sqlparsertools\\export\\Policies" => "lib/export/Policies.php",
    "\\travelsoft\\sqlparsertools\\export\\Rooms" => "lib/export/Rooms.php",
    "\\travelsoft\\sqlparsertools\\ajax\\Controller" => "lib/ajax/Controller.php",
    "\\travelsoft\\sqlparsertools\\ajax\\Actions" => "lib/ajax/Actions.php",
);


CModule::AddAutoloadClasses("travelsoft.sqlparsertools", $classes);

\Bitrix\Main\Loader::includeModule("iblock");
