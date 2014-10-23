<?php
/**
 * applicaiton/modules/api/controllers/helpers/Log/ApiRegisterDataLog.php
 *
 * Apiモジュール register-dataコントローラ ログ出力ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiRegisterDataLog.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Api_Log_ApiRegisterDataLog クラス
 *
 * Apiモジュール register-dataコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Log_ApiRegisterDataLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ログ番号：ユーザデータ登録成功
     */
    const CODE_REGISTER_DATA_SUCCESS = 20070;

    /**
     * ログ番号：ユーザデータ登録失敗
     */
    const CODE_REGISTER_DATA_FAILURE = 20071;

    /**
     * ログ番号：アップロード失敗
     */
    const CODE_UPLOAD_FAILURE        = 20072;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiRegisterDataLog';
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ユーザデータ登録成功
     *
     * @return void
     */
    public function registerDataSuccess()
    {
        $this->access(
            'ユーザデータ登録が成功しました。',
            self::CODE_REGISTER_DATA_SUCCESS
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：ユーザデータ登録失敗
     *
     * @param Exception $exception 例外
     * @return void
     */
    public function registerDataFailure($exception)
    {
        $this->notice(
            'ユーザデータ登録が失敗しました。',
            self::CODE_REGISTER_DATA_FAILURE
        );
        $this->error($exception);
    }

    // ------------------------------------------------------------------ //

    /**
     * ログ出力：アップロード失敗
     *
     * @param App_FileTransfer $fileTransfer App_FileTransferインスタンス
     * @return void
     */
    public function uploadFailure($fileTransfer)
    {
        $buffer = array();
        foreach ($fileTransfer->getMessages() as $key => $message) {
            array_push($buffer, $message);
        }

        $this->notice(
              'ユーザデータのアップロードに失敗しました。：'
            . implode(',', $buffer),
            self::CODE_UPLOAD_FAILURE
        );
    }
}
