<?php
/**
 * applicaiton/modules/api/models/Json/GetRegionList.php
 *
 * リージョンリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetRegionList.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_GetRegionList クラス
 *
 * リージョンリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_GetRegionList extends App_Json
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
     * リージョンリストJSONの生成
     *
     * @param array $result リージョンリスト
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'regions' => $results
        );
        return App_Json::encode($response);
    }
}
