<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;

global $USER;

if (!$USER->isAdmin()) {
    $APPLICATION->AuthForm("Access denided.");
}

$APPLICATION->SetTitle(Loc::getMessage("travelsoft_sqlparsertools_PAGE_TITLE"));

\Bitrix\Main\Loader::includeModule("travelsoft.sqlparsertools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$error = "";
if ($request->get("file_name") === "") {
    $error = "";
} elseif (!file_exists(travelsoft\sqlparsertools\Config::getAbsUploadSqlFilePath($request->get("file_name")))) {
    $error = "";
}

if ($error === "") {
    ?>

    <div id="progress-area">
        <?
        CAdminMessage::ShowMessage(array(
            "MESSAGE" => "Выгрузка файла в базу данных...",
            "DETAILS" => "#PROGRESS_BAR#",
            "HTML" => true,
            "TYPE" => "PROGRESS",
            "PROGRESS_TOTAL" => 100,
            "PROGRESS_VALUE" => 25,
        ));
        ?>
    </div>

    <script>
        BX.ready(function () {

            "use strict";

            function sendRequest(data, onsuccess, onfailure) {
                BX.showWait();
                data.sessid = BX.bitrix_sessid();
                data.file_name = "<?= $request->get("file_name") ?>";
                BX.ajax({
                    url: '/local/modules/travelsoft.sqlparsertools/admin/ajax/file_processing.php',
                    data: data,
                    method: 'POST',
                    dataType: 'html',
                    timeout: 99999999,
                    async: true,
                    processData: false,
                    scriptsRunFirst: false,
                    emulateOnload: false,
                    start: true,
                    cache: false,
                    onsuccess: onsuccess,
                    onfailure: onfailure
                });

            }

            function triggerError() {
                alert("Ooops. Server problem. Please, try again later");
                window.location = "/bitrix/admin/travelsoft_sqlparsertools.php?lang=<?= LANGUAGE_ID ?>";
            }

            sendRequest({
                action: "upload_file_in_db"
            }, function (html) {

                if (typeof html !== "") {
                    BX("progress-area").innerHTML = html;
                    BX.closeWait();
                    sendRequest({
                        action: "export_db"
                    }, function (html) {

                        if (typeof html !== "") {
                            BX("progress-area").innerHTML = html;
                            BX.closeWait();
                        } else {
                            triggerError();
                        }

                    }, triggerError);
                } else {
                    triggerError();
                }

            }, triggerError);

        });
    </script>
    <?
} else {
    
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

