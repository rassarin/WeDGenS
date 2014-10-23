<?php
/**
 * applicaiton/modules/api/models/Json/CheckRequest.php
 *
 * リクエスト進捗確認結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: CheckRequest.php 54 2014-03-10 13:51:29Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_CheckRequest クラス
 *
 * リクエスト進捗確認結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_CheckRequest extends App_Json
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // nop
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト進捗確認結果JSONの生成
     *
     * @param array $result リクエスト進捗確認結果
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $checkResult = array(
           'request_id' => $results['request_id'],
           'status'     => $results['request_status_id'],
        );
        $response = array(
            'request' => $checkResult
        );
        return App_Json::encode($response);
    }
}
