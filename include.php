<?php

$classes = array(
    
    "\\travelsoft\\sqlparsertools\\Config" => "lib/Config.php",
    "\\travelsoft\\sqlparsertools\\Files" => "lib/Files.php",
    "\\travelsoft\\sqlparsertools\\export\\Exporter" => "lib/export/Exporter.php",
    "\\travelsoft\\sqlparsertools\\export\\Exporter" => "lib/export/Exporter.php",
);


CModule::AddAutoloadClasses("travelsoft.sqlparsertools", $classes);

\Bitrix\Main\Loader::includeModule("iblock");
