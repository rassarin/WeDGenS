<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiSendRequestLog.php
 *
 * Apiモジュール send-requestコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiSendRequestLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiSendRequestLog クラス
 *
 * Apiモジュール send-requestコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiSendRequestLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：リクエスト送信成功
     */
    const CODE_SEND_REQUEST_SUCCESS = 20080;

    /**
     * ログ番号：リクエスト送信失敗
     */
    const CODE_SEND_REQUEST_FAILURE = 20081;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiSendRequestLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リクエスト送信成功
     *
     * @param string $requestId リクエストID
     * @return void
     */
    public function sendRequestSuccess($requestId)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->access(
            'リクエストID：' . $requestId . 'のリクエストを送信しました。',
            self::CODE_SEND_REQUEST_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リクエスト送信失敗
     *
     * @param string $requestId リクエストID
     * @param Exception $exception 例外
     * @return void
     */
    public function sendRequestFailure($requestId, $exception)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->notice(
            'リクエストID：' . $requestId . 'のリクエスト送信に失敗しました。',
            self::CODE_SEND_REQUEST_FAILURE
        );
        $this->error($exception);
    }
}
