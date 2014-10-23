<?php
/**
 * applicaiton/modules/api/models/Json/SendRequest.php
 *
 * リクエスト送信結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: SendRequest.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_SendRequest クラス
 *
 * リクエスト送信結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_SendRequest extends App_Json
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
     * リクエスト送信結果JSONの生成
     *
     * @param array $result リクエスト送信結果
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'request' => $results
        );
        return App_Json::encode($response);
    }
}
