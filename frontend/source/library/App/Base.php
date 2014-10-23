<?php
/**
 * library/App/Base.php
 *
 * アプリケーション基本機能抽象クラス
 *
 * @category    App
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Base.php 12 2014-02-26 02:23:33Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Base クラス
 *
 * アプリケーション基本機能抽象クラス
 *
 * @category    App
 * @package     Base
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Base
{
    // ------------------------------------------------------------------ //

    /**
     * 設定
     *
     * @var array
     */
    protected $_config = array();

    // ------------------------------------------------------------------ //

    /**
     * 外部ファイルパス設定
     *
     * @var array
     */
    protected $_externalFileConfig = array();

    /**
     * モジュール名
     *
     * @var string
     */
    protected $_module = null;

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct()
    {
        // 初期化メソッド
        $this->init();
    }

    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     * ：抽象メソッド
     *
     * @return void
     */
    abstract public function init();

    // ------------------------------------------------------------------ //

    /**
     * サービス設定のセット
     *
     * @param array $config サービス設定
     * @return void
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    // ------------------------------------------------------------------ //

    /**
     * サービス設定の取得
     *
     * @return array サービス設定
     */
    public function getConfig()
    {
        return $this->_config;
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュール名のセット
     *
     * @param string $moduleName モジュール名
     * @return void
     */
    public function setModuleName($moduleName)
    {
        $this->_module = $moduleName;
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュール名の取得
     *
     * @return string モジュール名
     */
    public function getModuleName()
    {
        return $this->_module;
    }

    // ------------------------------------------------------------------ //

    /**
     * DbTableモデルの取得
     *
     * @param string $tableName テーブル名
     * @param string $dbName DB接続名
     * @return  Zend_Db_Table_Abstract DbTableモデルクラス
     */
    public function getDbTable($tableName, $dbName = null)
    {
        return App_Db_TableModel::getTableModelClass($tableName, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュールDbTableモデルの取得
     *
     * @param string $tableName テーブル名
     * @param string $moduleName モジュール名
     * @param string $dbName DB接続名
     * @return  Zend_Db_Table_Abstract モジュールDbTableモデルクラス
     */
    public function getModuleDbTable($tableName, $moduleName = null, $dbName = null)
    {
        if (is_null($moduleName)) {
            $moduleName = $this->getModuleName();
        }
        return App_Db_TableModel::getModuleTableModelClass($moduleName, $tableName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モデルの取得
     *
     * @param string $modelName モデル名
     * @param string $dbName DB接続名
     * @return  Zend_Db_Table_Abstract モデルクラス
     */
    public function getModel($modelName, $dbName = null)
    {
        return App_Db_TableModel::getModelClass($modelName, $dbName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュールモデルの取得
     *
     * @param string $modelName モデル名
     * @param string $moduleName モジュール名
     * @param string $dbName DB接続名
     * @return  Zend_Db_Table_Abstract モジュールモデルクラス
     */
    public function getModuleModel($modelName, $moduleName = null, $dbName = null)
    {
        if (is_null($moduleName)) {
            $moduleName = $this->getModuleName();
        }
        return App_Db_TableModel::getModuleModelClass($moduleName, $modelName);
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLモデルの取得
     *
     * @param string $modelName モデル名
     * @return App_Xml モデルクラス
     */
    public function getXmlModel($modelName)
    {
        return App_Xml::getXmlModelClass($modelName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュールモデルの取得
     *
     * @param string $modelName モデル名
     * @param string $moduleName モジュール名
     * @return App_Xml モデルクラス
     */
    public function getXmlModuleModel($modelName, $moduleName = null)
    {
        if (is_null($moduleName)) {
            $moduleName = $this->getModuleName();
        }
        return App_Xml::getXmlModuleModelClass($moduleName, $modelName);
    }

    // ------------------------------------------------------------------ //

    /**
     * Jsonモデルの取得
     *
     * @param string $modelName モデル名
     * @return App_Json モデルクラス
     */
    public function getJsonModel($modelName)
    {
        return App_Json::getJsonModelClass($modelName);
    }

    // ------------------------------------------------------------------ //

    /**
     * モジュールモデルの取得
     *
     * @param string $modelName モデル名
     * @param string $moduleName モジュール名
     * @return App_Json モデルクラス
     */
    public function getJsonModuleModel($modelName, $moduleName = null)
    {
        if (is_null($moduleName)) {
            $moduleName = $this->getModuleName();
        }
        return App_Json::getJsonModuleModelClass($moduleName, $modelName);
    }

    // ------------------------------------------------------------------ //

    /**
     * DBアダプターの取得
     *
     * @param string $dbName DB接続名
     * @return  Zend_Db_Adapter_Abstract DBアダプター
     */
    public function getAdapter($dbName = null)
    {
        $resource = App_Utils::getDbResource();
        if (!is_null($dbName)) {
            return $resource->getDb($dbName);
        }
        return $resource->getDefaultDb();
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::INFO)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
     */
    public function accessLog($message, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->accessLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::NOTICE)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
     */
    public function noticeLog($message, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->noticeLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::DEBUG)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
     */
    public function debugLog($message, $code = App_Log::CODE_DEBUG)
    {
        $logger = $this->getAccessLogger();
        if ($logger) {
            $logger->debugLog(
                $message,
                $code
            );
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::CRIT)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
     */
    public function criticalLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->criticalLog($exception, $code);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::ERR)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
     */
    public function errorLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->errorLog($exception, $code);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーログの出力(ログ出力プライオリティ：App_Log::WARN)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
     */
    public function warningLog($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->warningLog($exception, $code);
        }
    }

    // ---------------------------------------------------------------------- //

    /**
     * アクセスログインスタンスの取得
     *
     * @return App_Log_Access|boolean
    */
    public function getAccessLogger()
    {
        $loggers = $this->_getLoggers();
        if (is_array($loggers)) {
            if (array_key_exists('accesslog', $loggers)) {
                return $loggers['accesslog'];
            }
        }
        return false;
    }

    // ---------------------------------------------------------------------- //

    /**
     * エラーログインスタンスの取得
     *
     * @return App_Log_Error|boolean
    */
    public function getErrorLogger()
    {
        $loggers = $this->_getLoggers();
        if (is_array($loggers)) {
            if (array_key_exists('errorlog', $loggers)) {
                return $loggers['errorlog'];
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * 分割設定ファイルの読み込み
     *
     * @param string $configFile 設定ファイルパス
     * @param string $section セクション名
     * @return array 設定値
     */
    public function loadPartialConfig($configFile, $section = null)
    {
        // 設定読み込み
        $cache   = App_Utils::getConfigCache($configFile);
        $cacheId = get_class($this)
                 .'_'
                 . preg_replace('/\./', '_', basename($configFile));
        if (!is_null($section)) {
            $cacheId .= '_' . $section;
        }

        $cacheId = preg_replace('/\-/', '_', $cacheId);

        if (!($config = $cache->load($cacheId))) {
            $config = App_Utils::loadConfig($configFile, $section);
            $cache->save($config, $cacheId);
        }
        return $config->toArray();
    }

    // ---------------------------------------------------------------------- //

    /**
     * Loggerプラグインリソースの取得
     *
     * @return array ロガー格納配列
    */
    private function _getLoggers()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bootstrap = $frontController->getParam('bootstrap');
        if (!$bootstrap->hasPluginResource('Logger')) {
            return false;
        }
        $loggers = $bootstrap->getResource('Logger');
        return $loggers;
    }
}

