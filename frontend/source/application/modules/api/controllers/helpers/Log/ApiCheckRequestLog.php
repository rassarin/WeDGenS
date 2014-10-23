<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiCheckRequestLog.php
 *
 * Apiモジュール check-requestコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiCheckRequestLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiCheckRequestLog クラス
 *
 * Apiモジュール check-requestコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiCheckRequestLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：リクエスト進捗確認成功
     */
    const CODE_CHECK_REQUEST_SUCCESS = 20090;

    /**
     * ログ番号：リクエスト進捗確認失敗
     */
    const CODE_CHECK_REQUEST_FAILURE = 20091;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiCheckRequestLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リクエスト進捗確認成功
     *
     * @param string $requestId リクエストID
     * @param Exception $exception 例外
     * @return void
     */
    public function checkRequestSuccess($requestId)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->access(
            'リクエストID：' . $requestId . 'の進捗確認が成功しました。',
            self::CODE_CHECK_REQUEST_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：リクエスト進捗確認失敗
     *
     * @param string $requestId リクエストID
     * @param Exception $exception 例外
     * @return void
     */
    public function checkRequestFailure($requestId, $exception)
    {
        if (App_Utils::isEmpty($requestId)) {
            $requestId = '不明';
        }
        $this->notice(
            'リクエストID：' . $requestId . 'の進捗確認が失敗しました。',
            self::CODE_CHECK_REQUEST_FAILURE
        );
        $this->error($exception);
    }
}
