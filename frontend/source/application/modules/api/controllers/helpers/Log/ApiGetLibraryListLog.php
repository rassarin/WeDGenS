<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetLibraryListLog.php
 *
 * Apiモジュール get-library-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetLibraryListLog.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetLibraryListLog クラス
 *
 * Apiモジュール get-library-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetLibraryListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：利用可能ライブラリリスト取得成功
     */
    const CODE_GET_LIB_SUCCESS = 20010;

    /**
     * ログ番号：利用可能ライブラリリスト取得失敗
     */
    const CODE_GET_LIB_FAILURE = 20011;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetLibraryListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：利用可能ライブラリリスト取得成功
     *
     * @return void
     */
    public function getLibraryListSuccess()
    {
        $this->access(
            '利用可能ライブラリリストを取得しました。',
            self::CODE_GET_LIB_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：利用可能ライブラリリスト取得失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function getLibraryListFailure($exception)
    {
        $this->notice(
            '利用可能ライブラリリスト取得に失敗しました。',
            self::CODE_GET_LIB_FAILURE
        );
        $this->error($exception);
    }
}
