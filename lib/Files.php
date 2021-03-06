<?php

namespace travelsoft\sqlimporttools;

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
                throw new \Exception(Loc::getMessage("travelsoft_sqlimporttools_UPLOAD_FILE_ERROR_" . $file["error"]));
            }

            if (\array_pop(\explode(".", $file["name"])) !== "sql") {
                throw new \Exception(Loc::getMessage("travelsoft_sqlimporttools_BAD_FILE_EXTENSION"));
            }

            if (!\move_uploaded_file($file['tmp_name'], Config::getAbsUploadSqlFilePath(\basename($file['name'])))) {
                throw new \Exception(Loc::getMessage("travelsoft_sqlimporttools_FILE_UPLOADING_ERROR"));
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $source_images_paths
     * @param int $hotel_id
     * @return array
     */
    public function downloadHotelImages(array $source_images_paths, int $hotel_id) {

        $arDownloadedFiles = [];

        foreach ($source_images_paths as $path) {

            $path = str_replace(["\"", "'"], ["", ""], $path);

            $save_image_path = Config::getAbsUploadHotelsImagePath($this->getFileNameByPath($path), $hotel_id);

            if ($this->dowloadFile($path, $save_image_path)) {
                $arDownloadedFiles[] = $save_image_path;
            }
        }

        return $arDownloadedFiles;
    }

    /**
     * @param array $source_images_paths
     * @param int $room_id
     * @return array
     */
    public function downloadRoomImages(array $source_images_paths, int $room_id) {

        $arDownloadedFiles = [];

        foreach ($source_images_paths as $path) {

            $path = str_replace(["\"", "'"], ["", ""], $path);

            $image_path = Config::getAbsUploadRoomsImagePath($this->getFileNameByPath($path), $room_id);
            if ($this->dowloadFile($path, $image_path)) {
                $arDownloadedFiles[] = $image_path;
            }
        }

        return $arDownloadedFiles;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getFileNameByPath(string $path) {

        return (string) array_pop(explode("/", $path));
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

    /**
     * @param string $source_file_path
     * @param string $save_file_path
     * @return boolean
     */
    public function dowloadFile(string $source_file_path, string $save_file_path) {
        $content = \file_get_contents($source_file_path);
        if ($content) {
            \file_put_contents($save_file_path, $content);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $path
     * @return array
     */
    public function getFileUploadArray(string $path) {

        return \CFile::MakeFileArray($path);
    }

}
