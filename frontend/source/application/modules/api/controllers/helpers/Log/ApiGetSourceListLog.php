<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetSourceListLog.php
 *
 * Apiモジュール get-source-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetSourceListLog.php 20 2014-02-27 10:11:47Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetSourceListLog クラス
 *
 * Apiモジュール get-source-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetSourceListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：データソースリスト取得成功
     */
    const CODE_GET_SOURCE_SUCCESS = 20030;

    /**
     * ログ番号：データソースリスト取得失敗
     */
    const CODE_GET_SOURCE_FAILURE = 20031;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetSourceListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：データソースリスト取得成功
     *
     * @return void
     */
    public function getSourceListSuccess()
    {
        $this->access(
            'データソースリストを取得しました。',
            self::CODE_GET_SOURCE_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：データソースリスト取得失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function getSourceListFailure($exception)
    {
        $this->notice(
            'データソースリスト取得に失敗しました。',
            self::CODE_GET_SOURCE_FAILURE
        );
        $this->error($exception);
    }
}
