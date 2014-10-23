<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiGetResultLog.php
 *
 * Apiモジュール get-resultコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetResultLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiGetResultLog クラス
 *
 * Apiモジュール get-resultコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiGetResultLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：実行結果取得成功
     */
    const CODE_GET_RESULT_SUCCESS = 20100;

    /**
     * ログ番号：実行結果取得失敗
     */
    const CODE_GET_RESULT_FAILURE = 20101;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetResultLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：実行結果取得成功
     *
     * @param string $requestId リクエストID
     * @return void
     */
    public function getResultSuccess($requestId)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->access(
            'リクエストID：' . $requestId . 'の実行結果を取得しました。',
            self::CODE_GET_RESULT_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：実行結果取得失敗
     *
     * @param string $requestId リクエストID
     * @param Exception $exception 例外
     * @return void
     */
    public function getResultFailure($requestId, $exception)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->notice(
            'リクエストID：' . $requestId . 'の実行結果取得に失敗しました。',
            self::CODE_GET_RESULT_FAILURE
        );
        $this->error($exception);
    }
}
