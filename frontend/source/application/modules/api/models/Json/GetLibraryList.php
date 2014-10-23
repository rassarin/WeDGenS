<?php
/**
 * applicaiton/modules/api/models/Json/GetLibraryList.php
 *
 * ライブラリリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetLibraryList.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_GetLibraryList クラス
 *
 * ライブラリリストJSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_GetLibraryList extends App_Json
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
     * ライブラリリストJSONの生成
     *
     * @param array $result ライブラリリスト
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'libraries' => $results
        );
        return App_Json::encode($response);
    }
}
