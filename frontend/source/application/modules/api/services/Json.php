<?php
/**
 * applicaiton/modules/api/services/Json.php
 *
 * JSONレスポンスサービスクラス
 *
 * JSONレスポンスサービス。
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Json.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Service_Search
*/

/**
 * Api_Service_Json クラス
 *
 * JSONレスポンスサービスクラス
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Service_Json extends Service_Api
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // 共通初期化処理を記述
    }


    // ------------------------------------------------------------------ //

    /**
     * 通知JSONの生成。
     *
     * @param string $message エラーメッセージ
     * @return string JSON文字列
     */
    public function notifyResponse($message)
    {
        $json = $this->getJsonModuleModel('notification')
                    ->notifyJson($message);

        return $json;
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーレスポンスJSONの生成。
     *
     * @param string $message エラーメッセージ
     * @return string JSON文字列
     */
    public function errorResponse($message)
    {
        $json = $this->getJsonModuleModel('notification')
                     ->errorJson($message);

        return $json;
    }

    // ------------------------------------------------------------------ //

    /**
     * 成功レスポンスJSONの生成。
     *
     * @return string JSON文字列
     */
    public function successResponse()
    {
        return $this->notifyResponse('SUCCESS');
    }

    // ------------------------------------------------------------------ //

    /**
     * 失敗レスポンスJSONの生成。
     *
     * @return string JSON文字列
     */
    public function failureResponse()
    {
        return $this->notifyResponse('FAILURE');
    }

    // ------------------------------------------------------------------ //

    /**
     * アクション実行結果レスポンスJSONの生成。
     *
     * @param mixed $results 結果
     * @param string $controller 実行コントローラ名
     * @return string JSON文字列
     */
    public function formatResponse($results, $controller)
    {
        $json = $this->getJsonModuleModel($controller)
                     ->formatToJson($results);
        return $json;
    }
}

