<?php
/**
 * applicaiton/modules/api/models/Json/GetDataTypeList.php
 *
 * データ種別リストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetDataTypeList.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_GetDataTypeList クラス
 *
 * データ種別リストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_GetDataTypeList extends App_Json
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
     * データ種別リストJSONの生成
     *
     * @param array $result データ種別リスト
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'data_types' => $results
        );
        return App_Json::encode($response);
    }
}
