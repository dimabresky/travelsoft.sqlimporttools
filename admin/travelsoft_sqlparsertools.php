<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;

global $USER;

if (!$USER->isAdmin()) {
    $APPLICATION->AuthForm("Access denided.");
}

$APPLICATION->SetTitle(Loc::getMessage("travelsoft_sqlparsertools_PAGE_TITLE"));

$APPLICATION->AddHeadString("<link rel='stylesheet' href='/local/modules/travelsoft.sqlparsertools/admin/css/travelsoft_sqlparsertools.css?" . randString(7) . "'>");

\Bitrix\Main\Loader::includeModule("travelsoft.sqlparsertools");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$error = "";

$files = new travelsoft\sqlparsertools\Files;

try {
    if ($request->isPost() && check_bitrix_sessid()) {



        if ($files->upload($request->getFile("SQL"))) {

            $_SESSION["travelsoft_sqlparsertools_SUCCESS_UPLOAD_FILE"] = true;
            reload();
        }
    }
    if (strlen($request->get("delete_file")) > 0 && file_exists(travelsoft\sqlparsertools\Config::getAbsUploadSqlFilePath($request->get("delete_file")))) {
        
        unlink(travelsoft\sqlparsertools\Config::getAbsUploadSqlFilePath($request->get("delete_file")));
        reload();
    }
} catch (Exception $ex) {
    $error = $ex->getMessage();
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if (isset($_SESSION["travelsoft_sqlparsertools_SUCCESS_UPLOAD_FILE"]) && $_SESSION["travelsoft_sqlparsertools_SUCCESS_UPLOAD_FILE"]) {
    CAdminMessage::ShowNote(Loc::getMessage("travelsoft_sqlparsertools_SUCCESS_UPLOAD_FILE"));
    unset($_SESSION["travelsoft_sqlparsertools_SUCCESS_UPLOAD_FILE"]);
}

if ($error !== "") {
    CAdminMessage::ShowMessage($error);
}

$o_tab = new CAdminTabControl("travelsoft_sqlparsertools_tab_control", array(
    array(
        "DIV" => "travelsoft_sqlparsertools",
        "TAB" => Loc::getMessage("travelsoft_sqlparsertools_TAB_TITLE"),
        "ICON" => "",
        "TITLE" => Loc::getMessage("travelsoft_sqlparsertools_TAB_SUBTITLE")
    )
        ));
?>
<form action="<? $APPLICATION->GetCurPage() ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" value="<?= LANGUAGE_ID ?>" name="lang">
    <?
    echo bitrix_sessid_post();
    $o_tab->Begin();
    $o_tab->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><label><?= Loc::getMessage("travelsoft_sqlparsertools_SQL_FILE_FIELD_TITLE") ?>:</label></td>
        <td width="60%">
            <input type="file" name="SQL">
        </td>
    </tr>
    <? $o_tab->Buttons();
    ?>
    <input type="submit" name="next" value="<?= Loc::getMessage("travelsoft_sqlparsertools_SAVE_BTN_TITLE") ?>" title="<?= Loc::getMessage("dki_VKEXPORTER_NEXT_BTN_TITLE") ?>" class="adm-btn-save">
    <?
    $o_tab->End();
    ?>

</form>

<?
$arFiles = $files->getList();

if (!empty($arFiles)) {
    ?>
    <h2 class="table-title"><?= Loc::getMessage("travelsoft_sqlparsertools_TABLE_TITLE") ?></h2>
    <table id="files-list">
        <tbody>
            <? foreach ($arFiles as $file_name): ?>
                <tr>
                    <td><a title="<?= Loc::getMessage("travelsoft_sqlparsertools_DELETE_FILE_TITLE")?>" class="cross" href="<?= $APPLICATION->GetCurPageParam("delete_file=" . $file_name . "&lang=" . LANGUAGE_ID, ["lang", "delete_file"]);?>">&times;</a></td>
                    <td><?= $file_name ?></td>
                    <td><input onclick="window.location='<?= $APPLICATION->GetCurDir()?>/travelsoft_sqlparsertools_process.php?file_name=<?= $file_name?>&lang=<?= LANGUAGE_ID?>'" type="button" name="start" value="<?= Loc::getMessage("travelsoft_sqlparsertools_START_TITLE") ?>" title="<?= Loc::getMessage("travelsoft_sqlparsertools_START_TITLE") ?>" class="adm-btn-save"></td>
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

