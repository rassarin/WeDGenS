<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetRegionListLog.php
 *
 * Apiモジュール get-region-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetRegionListLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetRegionListLog クラス
 *
 * Apiモジュール get-region-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetRegionListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：リージョンリスト取得成功
     */
    const CODE_GET_REGION_SUCCESS = 20040;

    /**
     * ログ番号：リージョンリスト取得失敗
     */
    const CODE_GET_REGION_FAILURE = 20041;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetRegionListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リージョンリスト取得成功
     *
     * @return void
     */
    public function getRegionListSuccess()
    {
        $this->access(
            'リージョンリストを取得しました。',
            self::CODE_GET_REGION_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リージョンリスト取得失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function getRegionListFailure($exception)
    {
        $this->notice(
            'リージョンリスト取得に失敗しました。',
            self::CODE_GET_REGION_FAILURE
        );
        $this->error($exception);
    }
}
