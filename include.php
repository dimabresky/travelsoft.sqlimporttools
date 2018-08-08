<?php

$classes = array(
    
    "\\travelsoft\\sqlparsertools\\Config" => "lib/Config.php",
    "\\travelsoft\\sqlparsertools\\Files" => "lib/Files.php",
    
);


CModule::AddAutoloadClasses("travelsoft.sqlparsertools", $classes);

\Bitrix\Main\Loader::includeModule("iblock");
