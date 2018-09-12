<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;

global $USER;

if (!$USER->isAdmin()) {
    $APPLICATION->AuthForm("Access denided.");
}

$APPLICATION->SetTitle(Loc::getMessage("travelsoft_sqlimporttools_PAGE_TITLE"));

$APPLICATION->AddHeadString("<link rel='stylesheet' href='/local/modules/travelsoft.sqlimporttools/admin/css/travelsoft_sqlimporttools.css?" . randString(7) . "'>");

\Bitrix\Main\Loader::includeModule("travelsoft.sqlimporttools");

$files = new travelsoft\sqlimporttools\Files;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (strlen($request->get("delete_file")) > 0 && file_exists(travelsoft\sqlimporttools\Config::getAbsUploadSqlFilePath($request->get("delete_file")))) {

    unlink(travelsoft\sqlimporttools\Config::getAbsUploadSqlFilePath($request->get("delete_file")));
    reload();
}

$arFiles = $files->getList();

if (!empty($arFiles)) {
    ?>
    <h2 class="table-title"><?= Loc::getMessage("travelsoft_sqlimporttools_TABLE_TITLE") ?></h2>
    <table id="files-list">
        <tbody>
            <? foreach ($arFiles as $file_name): ?>
                <tr>
                    <td><a title="<?= Loc::getMessage("travelsoft_sqlimporttools_DELETE_FILE_TITLE") ?>" class="cross" href="<?= $APPLICATION->GetCurPageParam("delete_file=" . $file_name . "&lang=" . LANGUAGE_ID, ["lang", "delete_file"]); ?>">&times;</a></td>
                    <td><?= $file_name ?></td>
                    <td><input onclick="window.location = '<?= $APPLICATION->GetCurDir() ?>/travelsoft_sqlimporttools_process.php?file_name=<?= $file_name ?>&lang=<?= LANGUAGE_ID ?>'" type="button" name="start" value="<?= Loc::getMessage("travelsoft_sqlimporttools_START_TITLE") ?>" title="<?= Loc::getMessage("travelsoft_sqlimporttools_START_TITLE") ?>" class="adm-btn-save"></td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
    <?
}

function reload () {
    global $APPLICATION;
    LocalRedirect($APPLICATION->GetCurPageParam("lang=" . LANGUAGE_ID, ["lang", "delete_file"]));
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

