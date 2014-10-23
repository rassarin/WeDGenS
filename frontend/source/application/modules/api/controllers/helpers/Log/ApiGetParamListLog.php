<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetParamListLog.php
 *
 * Apiモジュール get-param-listコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetParamListLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetParamListLog クラス
 *
 * Apiモジュール get-param-listコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetParamListLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：パラメータリスト取得成功
     */
    const CODE_GET_PARAM_SUCCESS = 20060;

    /**
     * ログ番号：パラメータリスト取得失敗
     */
    const CODE_GET_PARAM_FAILURE = 20061;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetParamListLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：パラメータリスト取得成功
     *
     * @param integer $libId ライブラリID
     * @return void
     */
    public function getParamListSuccess($libId)
    {
        if (App_Utils::isEmpty($libId)) {
            $libId = '不明';
        }
        $this->access(
            'ライブラリID：' . $libId . 'のパラメータリストを取得しました。',
            self::CODE_GET_PARAM_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：パラメータリスト取得失敗
     *
     * @param integer $libId ライブラリID
     * @param Exception $exception 例外
     * @return void
     */
    public function getParamListFailure($libId, $exception)
    {
        if (App_Utils::isEmpty($libId)) {
            $libId = '不明';
        }
        $this->notice(
            'ライブラリID：' . $libId . 'のパラメータリスト取得に失敗しました。',
            self::CODE_GET_PARAM_FAILURE
        );
        $this->error($exception);
    }
}
