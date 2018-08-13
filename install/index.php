<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class travelsoft_sqlimporttools extends CModule {

    public $MODULE_ID = "travelsoft.sqlimporttools";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "N";
    public $namespaceFolder = "travelsoft";
    public $adminFilesList = array(
        "travelsoft_sqlimporttools.php",
        "travelsoft_sqlimporttools_process.php"
    );

    function __construct() {
        $arModuleVersion = array();
        $path_ = str_replace("\\", "/", __FILE__);
        $path = substr($path_, 0, strlen($path_) - strlen("/index.php"));
        include($path . "/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = Loc::getMessage("travelsoft_sqlimporttools_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("travelsoft_sqlimporttools_MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("travelsoft_sqlimporttools_PARTNER_NAME");
        $this->PARTNER_URI = "https://github.com/dimabresky/";

        set_time_limit(0);
    }

    public function copyFiles() {

        foreach ($this->adminFilesList as $file) {
            CopyDirFiles(
                    $_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . $this->MODULE_ID . "/install/admin/" . $file, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/" . $file, true, true
            );
        }
    }

    public function copyAdminThemes() {
        CopyDirFiles(
                $_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . $this->MODULE_ID . "/install/themes/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/", true, true
        );
    }

    public function deleteAdminThemes() {
        DeleteDirFilesEx("/bitrix/themes/.default/icons/" . $this->MODULE_ID);
        DeleteDirFilesEx("/bitrix/themes/.default/" . $this->MODULE_ID . ".css");
    }

    public function deleteFiles() {
        foreach ($this->adminFilesList as $file) {
            DeleteDirFilesEx("/bitrix/admin/" . $file);
        }

        return true;
    }

    public function createTechDirs() {

        $io = CBXVirtualIo::getInstance();
        if (!$io->CreateDirectory($io->RelativeToAbsolutePath("/upload/travelsoft_sqlimporttools/sql"))) {
            throw new Exception(Loc::getMessage("travelsoft_sqlimporttools_CREATE_TECH_DIRS_ERROR"));
        }
        
        if (!$io->CreateDirectory($io->RelativeToAbsolutePath("/upload/travelsoft_sqlimporttools/images"))) {
            throw new Exception(Loc::getMessage("travelsoft_sqlimporttools_CREATE_TECH_DIRS_ERROR"));
        }
        
        file_put_contents($io->RelativeToAbsolutePath("/upload/travelsoft_sqlimporttools/.htaccess"), "Deny from all");
    }

    public function deleteTechDirs() {
        $io = CBXVirtualIo::getInstance();
        $io->Delete($io->RelativeToAbsolutePath("/upload/travelsoft_sqlimporttools"));
    }

    public function DoInstall() {
        try {

            # регистрируем модуль
            ModuleManager::registerModule($this->MODULE_ID);

            # копирование файлов
            $this->copyFiles();
            
            # копирование стилей admin панели
            $this->copyAdminThemes();
            
            #создание технических дирректорий
            $this->createTechDirs();
        } catch (Exception $ex) {

            $GLOBALS["APPLICATION"]->ThrowException($ex->getMessage());

            $this->DoUninstall();

            return false;
        }

        return true;
    }

    public function DoUninstall() {

        # удаление файлов
        $this->deleteFiles();

        # удаление стилей admin панели
        $this->deleteAdminThemes();
        
        #создание технических дирректорий
        $this->deleteTechDirs();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        return true;
    }

}
