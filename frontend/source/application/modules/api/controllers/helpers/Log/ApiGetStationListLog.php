<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetStationListLog.php
 *
 * Apiモジュール get-station-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetStationListLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetStationListLog クラス
 *
 * Apiモジュール get-station-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetStationListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：ステーションリスト取得成功
     */
    const CODE_GET_STATION_SUCCESS = 20050;

    /**
     * ログ番号：ステーションリスト取得失敗
     */
    const CODE_GET_STATION_FAILURE = 20051;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetStationListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ステーションリスト取得成功
     *
     * @return void
     */
    public function getStationListSuccess()
    {
        $this->access(
            'ステーションリストを取得しました。',
            self::CODE_GET_STATION_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ステーションリスト取得失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function getStationListFailure($exception)
    {
        $this->notice(
            'ステーションリスト取得に失敗しました。',
            self::CODE_GET_STATION_FAILURE
        );
        $this->error($exception);
    }
}
