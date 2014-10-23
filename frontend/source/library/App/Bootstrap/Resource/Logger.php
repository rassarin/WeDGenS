<?php
/**
 * library/App/Bootstrap/Resource/Logger.php
 *
 * ログリソースプラグイン。
 *
 * ブートストラップにてログ初期化処理を行うためのリソースプラグイン。
 *
 * @category    App
 * @package     Bootstrap
 * @subpackage  Resource
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Logger.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Application_Resource_ResourceAbstract
*/

/**
 * App_Bootstrap_Resource_Logger クラス
 *
 *  ログリソースプラグイン定義クラス
 *
 * @category    App
 * @package     Bootstrap
 * @subpackage  Resource
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Bootstrap_Resource_Logger extends Zend_Application_Resource_ResourceAbstract
{
    // ---------------------------------------------------------------------- //

    /**
     * @var array ロガー格納配列
     */
    protected $_loggers;

    // ---------------------------------------------------------------------- //

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return array ロガー格納配列
     */
    public function init()
    {
        return $this->getLoggers();
    }

    // ---------------------------------------------------------------------- //

    /**
     * ロガー格納配列のセット
     *
     * @param  App_Log $loggers
     * @return App_Bootstrap_Resource_Logger
     */
    public function setLoggers(array $loggers)
    {
        $this->_loggers = $loggers;
        return $this;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ロガー格納配列の取得
     *
     * @return array ロガー格納配列
     */
    public function getLoggers()
    {
        if (null === $this->_loggers) {
            $options = $this->getOptions();
            $loggers = $this->_initLoggers($options);
            $this->setLoggers($loggers);
        }
        return $this->_loggers;
    }

    // ---------------------------------------------------------------------- //

    /**
     * App_Logインスタンスの初期化
     *
     * @param array $options App_Logオプション
     * @return array ロガー格納配列
     */
    protected function _initLoggers($options)
    {
        $loggers = array();

        // 各ロガーインスタンスを生成し、配列に格納
        if (isset($options['accesslog'])) {
            $loggers['accesslog'] = $this->_getLoggerInstance(
                'accesslog',
                $options['accesslog']
            );
        }

        if (isset($options['errorlog'])) {
            $loggers['errorlog']  = $this->_getLoggerInstance(
                'errorlog',
                $options['errorlog']
            );
        }

        return $loggers;
    }

    // ---------------------------------------------------------------------- //

    /**
     * App_Logインスタンスの初期化
     *
     * @param string $loggerType ログ種別
     * @param array $options App_Log初期化オプション
     * @return App_Log App_Logインスタンス
     */
    private function _getLoggerInstance($loggerType, $options)
    {
        $logger = null;
        $writer = null;

        // ロガーインスタンス生成
        switch ($loggerType) {
            case 'accesslog':
                $logger = new App_Log_Access();
                break;
            case 'errorlog':
                $logger = new App_Log_Error();
                break;
            default:
                $logger = new App_Log();
                break;
        }

        // ライターをロガーにセット
        $writer = $this->_getWriterInstance($loggerType, $options);
        $logger->addWriter($writer);

        // フィルター設定
        if (isset($options['filter'])) {
            $filterOptions = $options['filter'];
            $filters = $this->_getLogFilterInstance($filterOptions);

            // フィルターをロガーにセット
            if (count($filters) > 0) {
                foreach ($filters as $filter) {
                    $logger->addFilter($filter);
                }
            }
        }

        // ロボットからのアクセスを無視するか設定
        if (isset($options['ignore_robot'])) {
            $logger->ignoreRobot();
        }

        return $logger;
    }

    // ---------------------------------------------------------------------- //

    /**
     * Zend_Log_Writer_Dbインスタンス取得
     *
     * @param string $loggerType ログ種別
     * @param string $options App_Log初期化オプション
     * @return Zend_Log_Writer
     */
    private function _getWriterInstance($loggerType, $options)
    {
        $writer = new Zend_Log_Writer_Null;
        if (isset($options['writer'])) {
            $writerOptions = $options['writer'];

            // ライタータイプに対応したインスタンス生成
            $writerType = strtolower(trim($writerOptions['type']));
            switch ($writerType) {
                // db：Zend_Log_Writer_Db
                case 'db':
                    $writer = $this->_getDbWriterInstance($writerOptions['params']);
                    break;

                // stream：Zend_Log_Writer_Stream
                case 'stream':
                    $writer = $this->_getStreamWriterInstance(
                        $loggerType,
                        $writerOptions['params']
                    );
                    break;

                // null：Zend_Log_Writer_Null
                case 'null':
                default:
                    $writer = new Zend_Log_Writer_Null();
                    break;
            }
        }

        return $writer;
    }

    // ---------------------------------------------------------------------- //

    /**
     * Zend_Log_Writer_Dbインスタンス取得
     *
     * @param string $dbWriterOptions Zend_Log_Writer_Db初期化オプション
     * @return Zend_Log_Writer_Db
     */
    private function _getDbWriterInstance($dbWriterOptions)
    {
        $dbAdapter     = $dbWriterOptions['dbAdapter'];
        $dbOptions     = $dbWriterOptions['dbOptions'];
        $dbLogTable    = $dbWriterOptions['logTable'];
        $columnMapping = $dbWriterOptions['columnMapping'];

        $db     = Zend_Db::factory($dbAdapter, $dbOptions);
        $writer = new Zend_Log_Writer_Db($db, $dbLogTable, $columnMapping);

        return $writer;
    }

    // ---------------------------------------------------------------------- //

    /**
     * Zend_Log_Writer_Streamインスタンス取得
     *
     * @param string $loggerType          ログ種別
     * @param string $streamWriterOptions Zend_Log_Writer_Stream初期化オプション
     * @return Zend_Log_Writer_Stream
     */
    private function _getStreamWriterInstance($loggerType, $streamWriterOptions)
    {
        $dateSuffix  = date('Ymd');
        $logfileName = $streamWriterOptions['logfileName'];
        $streamOrUrl = $logfileName . '-' . $dateSuffix . '.log';
        $mode        = isset($streamWriterOptions['mode']) ? $streamWriterOptions['mode'] : 'a';
        $writer      = new Zend_Log_Writer_Stream($streamOrUrl, $mode);

        // ログ出力フォーマットの設定
        $formatter = $this->_getLogFormatterInstance($loggerType);
        $writer->setFormatter($formatter);

        return $writer;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ログフォーマッターインスタンス取得
     *
     * @param string $loggerType ロガー種別
     * @return Zend_Log_Formatter_Simple
     */
    private function _getLogFormatterInstance($loggerType)
    {
        switch ($loggerType) {
            case 'errorlog':
                $formatter = $this->_getErrorLogFormatterInstance();
                break;
            case 'accesslog':
            default:
                $formatter = $this->_getAccessLogFormatterInstance();
                break;
        }
        return $formatter;
    }

    // ---------------------------------------------------------------------- //

    /**
     * エラーログフォーマッターインスタンス取得
     *
     * @return Zend_Log_Formatter_Simple
     */
    private function _getErrorLogFormatterInstance()
    {
        $items = array(
            "%timestamp%",
            "%userId%",
            "%ipAddr%",
            "%priorityName%[%code%]",
            "%message% in %errorFile% at %errorLine%",
        );
        $formatter = new Zend_Log_Formatter_Simple(implode("\t", $items) . PHP_EOL);
        return $formatter;
    }

    // ---------------------------------------------------------------------- //

    /**
     * アクセスログフォーマッターインスタンス取得
     *
     * @return Zend_Log_Formatter_Simple
     */
    private function _getAccessLogFormatterInstance()
    {
        $items = array(
            "%timestamp%",
            "%userId%",
            "%ipAddr%",
            "%priorityName%[%code%]",
            "%message%",
        );
        $formatter = new Zend_Log_Formatter_Simple(implode("\t", $items) . PHP_EOL);
        return $formatter;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ログフィルターインスタンス取得
     *
     * @param array $filterOptions ログフィルターオプション
     * @return array ログフィルターインスタンス
     */
    private function _getLogFilterInstance($filterOptions)
    {
        $filters = array();

        // プライオリティフィルタ
        if (isset($filterOptions['priority'])) {
            $priority = $filterOptions['priority'];
            if (!is_integer($priority)) {
                $c = 'App_Log::' . strtoupper(trim($priority));
                $priority = constant($c);
            }
            array_push($filters, new Zend_Log_Filter_Priority($priority));
        }

        // 正規表現フィルタ
        if (isset($filterOptions['regexp'])) {
            $regexp = $filterOptions['regexp'];
            array_push($filters, new Zend_Log_Filter_Message($regexp));
        }
        return $filters;
    }
}
