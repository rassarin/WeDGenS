<?php
/**
 * library/App/Exception.php
 *
 * 例外処理基底クラス。
 *
 * 各例外処理定義クラスは本クラスを継承する。
 *
 * @category    App
 * @package     Exception
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Exception.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Exception
*/

/**
 * App_Exception クラス
 *
 * 例外処理基底クラス。
 *
 * @category    App
 * @package     Exception
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Exception extends Zend_Exception
{
    // ------------------------------------------------------------------ //

    /**
     * 例外発生
     */
    const APP_EXCEPTION        = 90001;

    /**
     * アプリケーションエラー
     */
    const APP_SYSTEM_ERROR     = 91001;

    /**
     * I/Oエラー
     */
    const APP_IO_ERROR         = 92001;

    /**
     * メール関連エラー
     */
    const APP_MAIL_ERROR       = 92002;

    /**
     * DBエラー
     */
    const APP_DB_ERROR         = 93001;

    /**
     * バリデーションエラー
     */
    const APP_VALIDATE_ERROR   = 94001;

    /**
     * バイオレーションエラー
     */
    const APP_VIOLATION_ERROR  = 94002;

    /**
     * パーミッションエラー
     */
    const APP_PERMISSION_ERROR = 94003;

    /**
     * CSRFエラー
     */
    const APP_CSRF_ERROR       = 94004;

    /**
     * APIアクセスエラー
     */
    const APP_API_ERROR        = 95001;

    /**
     * 認証エラー
     */
    const APP_AUTH_ERROR       = 96001;

}
