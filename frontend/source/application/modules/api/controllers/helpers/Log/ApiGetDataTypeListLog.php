<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetDataTypeListLog.php
 *
 * Apiモジュール get-data-type-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetDataTypeListLog.php 19 2014-02-26 11:43:31Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetDataTypeListLog クラス
 *
 * Apiモジュール get-data-type-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetDataTypeListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：利用可能データリスト取得成功
     */
    const CODE_GET_DATA_TYPE_SUCCESS = 20020;

    /**
     * ログ番号：利用可能データリスト取得失敗
     */
    const CODE_GET_DATA_TYPE_FAILURE = 20021;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetDataTypeListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：利用可能データリスト取得成功
     *
     * @return void
     */
    public function getDataTypeListSuccess()
    {
        $this->access(
            '利用可能データリストを取得しました。',
            self::CODE_GET_DATA_TYPE_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：利用可能データリスト取得失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function getDataTypeListFailure($exception)
    {
        $this->notice(
            '利用可能データリスト取得に失敗しました。',
            self::CODE_GET_DATA_TYPE_FAILURE
        );
        $this->error($exception);
    }
}
