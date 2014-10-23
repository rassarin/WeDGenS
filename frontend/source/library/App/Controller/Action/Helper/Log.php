<?php
/**
 * library/App/Controller/Action/Helper/Log.php
 *
 * ログ出力ヘルパー。
 *
 * 各アクションのログ出力ヘルパーは本クラスを継承する。
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Log.php 7 2014-02-21 09:29:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action_Helper_Abstract
*/

/**
 * App_Controller_Action_Helper_Log クラス
 *
 * Appログ出力ヘルパー基底クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Controller_Action_Helper_Log extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * ログメッセージ
     *
     * @var string
     */
    protected $_messages = array();

    // ------------------------------------------------------------------ //

    /**
     * ヘルパーデフォルトメソッド
     *
     * @return App_Controller_Action_Helper_Acl アクセス制御ヘルパー
    */
    public function direct()
    {
        return $this;
    }

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'Log';
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::INFO)
     *
     * @param string  $message 詳細メッセージ
     * @param integer $code ログ番号
     * @return void
     */
    public function access($message, $code = App_Log::CODE_DEFAULT)
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
    public function notice($message, $code = App_Log::CODE_DEFAULT)
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
    public function debug($message, $code = App_Log::CODE_DEFAULT)
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
    public function critical($exception, $code = App_Log::CODE_DEFAULT)
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
    public function error($exception, $code = App_Log::CODE_DEFAULT)
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
    public function warning($exception, $code = App_Log::CODE_DEFAULT)
    {
        $logger = $this->getErrorLogger();
        if ($logger) {
            $logger->warningLog($exception, $code);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * デバッグ情報の出力(ログ出力プライオリティ：App_Log::DEBUG)
     *
     * @param mixed $value 値
     * @param string $label メッセージ
     * @return void
     */
    public function dump($value, $label = null)
    {
        $this->debug(Zend_Debug::dump($value, $label, false), App_Log::CODE_DEBUG);
    }

    // ------------------------------------------------------------------ //

    /**
     * バリデートエラーの出力(ログ出力プライオリティ：App_Log::WARN)
     *
     * @param Zend_Filter_Input $input ユーザ入力値
     * @return void
     */
    public function validateMessage($input)
    {
        $this->access(
            'Validatorエラー：' . self::_outputValiationError($input),
            App_Log_Access::CODE_VALIDATE_ERR
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * 例外キャッチエラーログの出力(ログ出力プライオリティ：App_Log::WARN)
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function exceptionMessage($exception)
    {
        $this->critical($exception, App_Log_Error::CODE_EXCEPTION_ERR);
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

    // ---------------------------------------------------------------------- //

    /**
     * バリデーションエラーメッセージの取得
     *
     * @param Zend_Filter_Input $input ユーザ入力値
     * @param string $glue 区切り文字
     * @return string バリデーションエラーメッセージ
    */
    private static function _outputValiationError($input, $glue = ', ')
    {
        $errors = array();
        foreach ($input->getMessages() as $key => $value) {
            array_push($errors, $key . ' => [' . implode(", ", $value) .']');
        }

        return implode($glue, $errors);
    }
}
