<?php
/**
 * applicaiton/modules/api/models/Json/GetStationList.php
 *
 * ステーションリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetStationList.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_GetStationList クラス
 *
 * ステーションリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_GetStationList extends App_Json
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
     * ステーションリストJSONの生成
     *
     * @param array $result ステーションリスト
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'stations' => $results
        );
        return App_Json::encode($response);
    }
}
