<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiAuthLog.php
 *
 * Apiモジュール authコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiAuthLog.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiAuthLog クラス
 *
 * Apiモジュール authコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiAuthLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：ログイン
     */
    const CODE_LOGIN  = 20601;

    /**
     * ログ番号：ログアウト
     */
    const CODE_LOGOUT = 20602;

    /**
     * ログ番号：ログイン失敗
     */
    const CODE_LOGIN_DENIED = 20603;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiAuthLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ログイン
     *
     * @param string $userId ログインしたユーザID
     * @return void
     */
    public function loginMessage($userId)
    {
        $this->notice(
            $userId . 'がログインしました。',
            self::CODE_LOGIN
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ログアウト
     *
     * @param string $userId ログアウトしたユーザID
     * @return void
     */
    public function logoutMessage($userId)
    {
        $this->notice(
            $userId . 'がログアウトしました。',
            self::CODE_LOGOUT
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ログイン失敗
     *
     * @param string $userId ログイン失敗したユーザID
     * @return void
     */
    public function loginDeniedMessage($userId)
    {
        if (App_Utils::isEmpty($userId)) {
            $userId = 'empty user_id';
        }
        $this->notice(
            $userId . 'がログインに失敗しました。',
            self::CODE_LOGIN_DENIED
        );
    }
}
