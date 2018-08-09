<?php
namespace travelsoft\sqlparsertools;

/**
 * Класс настроек модуля
 *
 * @author dimabresky
 */
class Config {
    
    const RELATIVE_UPLOAD_SQL_FILES_DIR = "/upload/travelsoft_sqlparsertools_sql";
    
    const RELATIVE_UPLOAD_IMAGES_DIR = "/upload/travelsoft_sqlparsertools_images";
    
    const DB_NAME = "hotels_booking";
    
    const DB_HOST = "localhost:31006";
    
    const DB_LOGIN = "hotels_b";
    
    const DB_PASSWORD = "qw23QW@#";
    
    const PROGRESS_TOTAL = 100;
    
    const CITIES_IBLOCK_ID = 30;
    
    const COUNTRIES_IBLOCK_ID = 35;
    
    /**
     * @return string
     */
    public static function getAbsUploadSqlFilesDir () {
        
        return \Bitrix\Main\Application::getDocumentRoot() . self::RELATIVE_UPLOAD_SQL_FILES_DIR;
        
    }
    
    /**
     * @param string $file_name
     * @return string
     */
    public static function getAbsUploadSqlFilePath (string $file_name) {
        
        return self::getAbsUploadSqlFilesDir() . "/" . $file_name;
    }
    
    /**
     * @return string
     */
    public static function getAbsUploadImagesDir () {
        
        return \Bitrix\Main\Application::getDocumentRoot() . self::RELATIVE_UPLOAD_IMAGES_DIR;
        
    }
    
    /**
     * @param string $file_name
     * @return string
     */
    public static function getAbsUploadImagePath (string $file_name) {
        
        return self::getAbsUploadImagesDir() . "/" . $file_name;
    }
    
    /**
     * @return string
     */
    public static function getAbsLogFilePath () {
        return \Bitrix\Main\Application::getDocumentRoot() . "/upload/travelsoft_sqlparsertools_export_log.txt";
    }
}
