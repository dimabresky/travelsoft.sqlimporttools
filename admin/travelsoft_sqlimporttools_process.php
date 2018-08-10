<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;

global $USER;

if (!$USER->isAdmin()) {
    $APPLICATION->AuthForm("Access denided.");
}

$APPLICATION->SetTitle(Loc::getMessage("travelsoft_sqlimporttools_PAGE_TITLE"));

$APPLICATION->AddHeadString("<script src='/local/modules/travelsoft.sqlimporttools/admin/js/travelsoft_sqlimporttools_process.js?".randString(7)."'></script>");

\Bitrix\Main\Loader::includeModule("travelsoft.sqlimporttools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$error = "";
if ($request->get("file_name") === "") {
    $error = "";
} elseif (!file_exists(travelsoft\sqlimporttools\Config::getAbsUploadSqlFilePath($request->get("file_name")))) {
    $error = "";
}

if ($error === "") {
    ?>

    <div id="progress-area">
        <?
        CAdminMessage::ShowMessage(array(
            "MESSAGE" => "Импорт файла в базу данных...",
            "DETAILS" => "#PROGRESS_BAR#",
            "HTML" => true,
            "TYPE" => "PROGRESS",
            "PROGRESS_TOTAL" => travelsoft\sqlimporttools\Config::PROGRESS_TOTAL,
            "PROGRESS_VALUE" => 0,
        ));
        ?>
    </div>

    <script>
        phpVars.sql_file_name = "<?= $request->get("file_name")?>";
    </script>
    <?
} else {
    
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

