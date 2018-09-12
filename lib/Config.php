<?php
namespace travelsoft\sqlimporttools;

/**
 * Класс настроек модуля
 *
 * @author dimabresky
 */
class Config {
    
    const RELATIVE_MODULE_UPLOAD_DIR = "/upload/travelsoft_sqlimporttools/";
    
    const RELATIVE_UPLOAD_SQL_FILES_DIR = "/upload/travelsoft_sqlimporttools/sql";
    
    const RELATIVE_UPLOAD_IMAGES_DIR = "/upload/travelsoft_sqlimporttools/images";
    
    // db settings
    const DB_NAME = "hotels_booking";
    
    const DB_HOST = "localhost:31006";
    
    const DB_LOGIN = "hotels_b";
    
    const DB_PASSWORD = "qw23QW@#";
    
    const SQL_BATCH_DELEMITER = ";";
    
    const PROGRESS_TOTAL = 10;
    
    const ROWS_LIMIT_DB_QUERY = 100;
    
    // iblocks id
    const CITIES_IBLOCK_ID = 30;
    
    const COUNTRIES_IBLOCK_ID = 35;
    
    const POLICIES_IBLOCK_ID = 59;
    
    const FACILITIES_IBLOCK_ID = 39;
    
    const FACILITIES_TYPE_IBLOCK_ID = 58;
    
    const HOTELS_IBLOCK_ID = 31;
    
    const ROOMS_IBLOCK_ID = 57;
    
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
     * @return string
     */
    public static function getAbsLogFilePath () {
        return \Bitrix\Main\Application::getDocumentRoot() . "/upload/travelsoft_sqlimporttools/export_log.txt";
    }
}
