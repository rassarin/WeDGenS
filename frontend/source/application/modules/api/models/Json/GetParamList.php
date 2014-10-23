<?php
/**
 * applicaiton/modules/api/models/Json/GetParamList.php
 *
 * パラメータリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetParamList.php 74 2014-03-14 07:54:11Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_GetParamList クラス
 *
 * パラメータリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_GetParamList extends App_Json
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
     * パラメータリストJSONの生成
     *
     * @param array $result パラメータリスト
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $params = array();
        foreach ($results as $result) {
            // disalbled指定のものは無視する。
            if (array_key_exists('disabled', $result) && $result['disabled']) {
                continue;
            }
            array_push($params, $result);
        }
        $response = array(
            'parameters' => $params
        );
        return App_Json::encode($response);
    }
}
