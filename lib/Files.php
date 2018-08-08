<?php

namespace travelsoft\sqlparsertools;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Класс для работы с файлами
 *
 * @author dimabresky
 */
class Files {

    /**
     * Метод загрузки файла на сервер
     * @param array $file
     * @return boolean
     * @throws \Exception
     */
    public function upload(array $file) {

        if (
                isset($file["name"]) &&
                isset($file["type"]) &&
                isset($file["size"]) &&
                isset($file["tmp_name"]) &&
                isset($file["error"])
        ) {

            if ($file["error"] !== 0) {
                throw new \Exception(Loc::getMessage("travelsoft_sqlparsertools_UPLOAD_FILE_ERROR_" . $file["error"]));
            }

            if (\array_pop(\explode(".", $file["name"])) !== "sql") {
                throw new \Exception(Loc::getMessage("travelsoft_sqlparsertools_BAD_FILE_EXTENSION"));
            }

            if (!\move_uploaded_file($file['tmp_name'], Config::getAbsUploadSqlFilePath(\basename($file['name'])))) {
                throw new \Exception(Loc::getMessage("travelsoft_sqlparsertools_FILE_UPLOADING_ERROR"));
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает список sql файлов
     * @return array
     */
    public function getList() {

        $arFiles = \array_values(\array_diff(\scandir(Config::getAbsUploadSqlFilesDir()), [".", "..", ".htaccess"]));

        if (is_array($arFiles) && !empty($arFiles)) {
            return $arFiles;
        } else {
            return [];
        }
    }

}
