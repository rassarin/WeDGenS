<?php
/**
 * library/App/Log/Error.php
 *
 * アプリケーションエラーログ取得クラス。
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Error.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Log
*/

/**
 * App_Log_Error クラス
 *
 * アプリケーションエラーログ取得クラス
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Log_Error extends App_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：例外キャッチ
     */
    const CODE_EXCEPTION_ERR  = 91000;

    /**
     * ログ番号：アプリケーションエラー
     */
    const CODE_SYSTEM_ERR     = 91100;

    /**
     * ログ番号：I/Oエラー
     */
    const CODE_IO_ERR         = 91200;

    /**
     * ログ番号：APIアクセスエラー
     */
    const CODE_API_ERR        = 91210;

    /**
     * ログ番号：HDFSアクセスエラー
     */
    const CODE_HDFS_ERR       = 91220;

    /**
     * ログ番号：DBエラー
     */
    const CODE_DB_ERR         = 91300;

    /**
     * ログ番号：バリデーションエラー
     */
    const CODE_VALIDATE_ERR   = 91400;

    /**
     * ログ番号：バイオレーションエラー
     */
    const CODE_VIOLATION_ERR  = 91500;

    /**
     * ログ番号：パーミッションエラー
     */
    const CODE_PERMISSION_ERR = 91600;

    /**
     * ログ番号：認証エラー
     */
    const CODE_AUTH_ERR       = 91601;

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::INFO)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function errorLog($excption, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスでも表示
        self::_outputToLogger(
            $excption,
            $code,
            self::ERR
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::CRIT)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function criticalLog($excption, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスでも表示
        self::_outputToLogger(
            $excption,
            $code,
            self::CRIT
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::WARN)
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @return void
    */
    public function warningLog($excption, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスでも表示
        self::_outputToLogger(
            $excption,
            $code,
            self::WARN
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力
     *
     * @param  string  $message  ログ出力メッセージ
     * @param  string  $userId   ユーザID
     * @param  integer $priority ログ出力プライオリティ
     * @param  integer $code     エラーコード
     * @param  string  $file     エラーファイル
     * @param  integer $line     エラー発生行番号
     * @return void
     */
    public function logging(
        $message, $userId, $priority, $code, $file, $line
    ) {
        // アクセス元IPアドレスの取得
        $ipAddr = self::getRemoteAddress();

        // ログアイテムのセット
        $this->setEventItem(self::LOG_ITEM_IP_ADDR,   self::setDefaultValue($ipAddr));
        $this->setEventItem(self::LOG_ITEM_CODE,      self::setDefaultValue($code, self::CODE_DEFAULT));
        $this->setEventItem(self::LOG_ITEM_ERR_FILE,  self::setDefaultValue($file));
        $this->setEventItem(self::LOG_ITEM_ERR_LINE,  self::setDefaultValue($line));
        $this->setEventItem(self::LOG_ITEM_USER_ID,   self::setDefaultValue($userId));

        $this->_toCustomLog(self::messageFilter($message), $priority);
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーロガーでの出力
     *
     * @param App_Exception|string $excption
     * @param integer $code ログ番号
     * @param integer $priority ログ出力プライオリティ
     * @return void
    */
    private function _outputToLogger($exception, $code, $priority)
    {
        $file = null;
        $line = null;

        if ($exception instanceof Exception) {
            $message = $exception->getMessage();
            $code    = $exception->getCode();
            $file    = $exception->getFile();
            $line    = $exception->getLine();
        } elseif (is_string($exception)) {
            $message = $exception;
        }

        $this->logging(
            $message,
            self::getLoginUserId(),
            $priority,
            $code,
            $file,
            $line
        );
    }
}
