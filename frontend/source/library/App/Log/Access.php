<?php
/**
 * library/App/Log/Access.php
 *
 * アプリケーションアクセスログ取得クラス。
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Access.php 7 2014-02-21 09:29:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Log
*/

/**
 * App_Log_Access クラス
 *
 * アプリケーションアクセスログ取得クラス
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Log_Access extends App_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：エラー：その他のエラー
     */
    const CODE_ERR            = 91000;

    /**
     * ログ番号：エラー：バリデートエラー
     */
    const CODE_VALIDATE_ERR   = 91400;

    /**
     * ログ番号：エラー：パーミッションエラー
     */
    const CODE_PERMISSION_ERR = 91400;

    /**
     * ログ番号：エラー：制約エラー
     */
    const CODE_VIOLATION_ERR  = 91500;

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::INFO)
     *
     * @param string $message メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function accessLog($message, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスは無視するか？
        if ($this->isIgnoreRobot() && self::isRobot()) {
            return;
        }

        self::_outputToLogger(
            $message,
            $code,
            self::INFO
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::NOTICE)
     *
     * @param string $message メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function noticeLog($message, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスでも表示
        self::_outputToLogger(
            $message,
            $code,
            self::NOTICE
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスログの出力(ログ出力プライオリティ：App_Log::DEBUG)
     *
     * @param string $message メッセージ
     * @param integer $code ログ番号
     * @return void
    */
    public function debugLog($message, $code = self::CODE_DEFAULT)
    {
        // ロボットからのアクセスでも表示
        self::_outputToLogger(
            $message,
            $code,
            self::DEBUG
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力
     *
     * @param  string  $message    メッセージ
     * @param  string  $userId     ログインユーザ
     * @param  integer $priority   ログ出力プライオリティ
     * @param  integer $code       ログ番号
     * @return void
     */
    public function logging($message, $userId, $priority, $code)
    {
        // アクセス元IPアドレスの取得
        $ipAddr = self::getRemoteAddress();

        // ログアイテムのセット
        $this->setEventItem(self::LOG_ITEM_IP_ADDR, self::setDefaultValue($ipAddr));
        $this->setEventItem(self::LOG_ITEM_CODE,    self::setDefaultValue($code, self::CODE_DEFAULT));
        $this->setEventItem(self::LOG_ITEM_USER_ID, self::setDefaultValue($userId));

        $this->_toCustomLog(self::messageFilter($message), $priority);
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスロガーでの出力
     *
     * @param string $message メッセージ
     * @param integer $code ログ番号
     * @param integer $priority ログ出力プライオリティ
     * @return void
    */
    private function _outputToLogger($message, $code, $priority)
    {
        $this->logging(
            $message,
            self::getLoginUserId(),
            $priority,
            $code
        );
    }
}
