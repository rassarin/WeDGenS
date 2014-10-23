<?php
/**
 * library/App/Db/TableModel.php
 *
 * テーブルモデル生成クラス。
 *
 * テーブル名を元に該当するテーブルクラスのインスタンスを生成する。
 *
 * @category    App
 * @package     Db
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: TableModel.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Db_TableModel クラス
 *
 * テーブルモデル生成クラス。
 *
 * @category    App
 * @package     Db
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Db_TableModel
{
    // ------------------------------------------------------------------ //

    /**
     * テーブル名からテーブルモデルクラス名を取得する
     *
     * @param  string $tableName テーブル名
     * @return string テーブルモデルクラス名
     */
    public static function getTableModelClassName($tableName)
    {
        $className = 'Model_DbTable_'
                   . App_Utils::underscoreToCamelCase($tableName);

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * テーブル名からモジュールテーブルモデルクラス名を取得する
     *
     * @param  string $moduleName モジュール名
     * @param  string $tableName テーブル名
     * @return string モジュールテーブルモデルクラス名
     */
    public static function getModuleTableModelClassName($moduleName, $tableName)
    {
        $className = ucfirst(strtolower($moduleName))
                   .  '_Model_DbTable_'
                   . ucfirst(App_Utils::camelCaseToUnderscore($tableName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモデルクラス名を取得する
     *
     * @param  string $modelName モデル名
     * @return string モデルクラス名
     */
    public static function getModelClassName($modelName)
    {
        $className = 'Model_'
                   . ucfirst(App_Utils::dashToCamelCase($modelName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールモデルクラス名を取得する
     *
     * @param  string $moduleName モジュール名
     * @param  string $modelName モデル名
     * @return string モジュールモデルクラス名
     */
    public static function getModuleModelClassName($moduleName, $modelName)
    {
        $className = ucfirst(strtolower($moduleName))
                   .  '_Model_'
                   . ucfirst(App_Utils::dashToCamelCase($modelName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * テーブル名からテーブルモデルクラスを生成する
     *
     * @param  string $tableName テーブル名
     * @param  string $dbName DB接続名
     * @return Zend_Db_Table_Abstract テーブルモデルクラス
     */
    public static function getTableModelClass($tableName, $dbName = null)
    {
        // テーブル名からテーブルモデルクラスを生成する。
        $className = self::getTableModelClassName($tableName);
        return self::_getModelClassInstance($className, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * テーブル名からモジュールテーブルモデルクラスを生成する
     *
     * @param  string $moduleName モジュール名
     * @param  string $tableName テーブル名
     * @param  string $dbName DB接続名
     * @return Zend_Db_Table_Abstract モジュールテーブルモデルクラス
     */
    public static function getModuleTableModelClass($moduleName, $tableName, $dbName = null)
    {
        // テーブル名からモジュールテーブルモデルクラスを生成する。
        $className = self::getModuleTableModelClassName($moduleName, $tableName);
        return self::_getModelClassInstance($className, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモデルクラスを生成する
     *
     * @param  string $modelName モデル名
     * @param  string $dbName DB接続名
     * @return Zend_Db_Table_Abstract テーブルモデルクラス
     */
    public static function getModelClass($modelName, $dbName = null)
    {
        // モデル名からテーブルモデルクラスを生成する。
        $className = self::getModelClassName($modelName);
        return self::_getModelClassInstance($className, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールモデルクラスを生成する
     *
     * @param  string $moduleName モジュール名
     * @param  string $modelName モデル名
     * @param  string $dbName DB接続名
     * @return Zend_Db_Table_Abstract モジュールモデルクラス
     */
    public static function getModuleModelClass($moduleName, $modelName, $dbName = null)
    {
        // モデル名からモジュールモデルクラスを生成する。
        $className = self::getModuleModelClassName($moduleName, $modelName);
        return self::_getModelClassInstance($className, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * Zend_Db_Tableクラスを生成する
     *
     * @param  string $tableName テーブル名
     * @param  string $dbName DB接続名
     * @return Zend_Db_Table_Abstract Zend_Db_Tableクラス
     */
    private static function _getModelClassInstance($className, $dbName = null)
    {
        $adapter  = null;
        $resource = App_Utils::getDbResource();
        if (!is_null($dbName)) {
            $adapter = $resource->getDb($dbName);
        } else {
            $adapter = $resource->getDefaultDb();
        }
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance(array('db' => $adapter));
    }
}