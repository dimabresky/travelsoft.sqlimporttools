<?php

$classes = array(
    
    "\\travelsoft\\sqlimporttools\\Config" => "lib/Config.php",
    "\\travelsoft\\sqlimporttools\\Files" => "lib/Files.php",
    "\\travelsoft\\sqlimporttools\\Tools" => "lib/Tools.php",
    "\\travelsoft\\sqlimporttools\\Logger" => "lib/Logger.php",
    "\\travelsoft\\sqlimporttools\\export\\Exporter" => "lib/export/Exporter.php",
    "\\travelsoft\\sqlimporttools\\export\\Cities" => "lib/export/Cities.php",
    "\\travelsoft\\sqlimporttools\\export\\Hotels" => "lib/export/Hotels.php",
    "\\travelsoft\\sqlimporttools\\export\\Facilities" => "lib/export/Facilities.php",
    "\\travelsoft\\sqlimporttools\\export\\Policies" => "lib/export/Policies.php",
    "\\travelsoft\\sqlimporttools\\export\\Rooms" => "lib/export/Rooms.php",
    "\\travelsoft\\sqlimporttools\\ajax\\Controller" => "lib/ajax/Controller.php",
    "\\travelsoft\\sqlimporttools\\ajax\\Actions" => "lib/ajax/Actions.php",
);


CModule::AddAutoloadClasses("travelsoft.sqlimporttools", $classes);

\Bitrix\Main\Loader::includeModule("iblock");
