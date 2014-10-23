<?php
/**
 * applicaiton/modules/api/models/Json/RegisterData.php
 *
 * ユーザデータ登録結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: RegisterData.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Json
*/

/**
 * Api_Model_Json_RegisterData クラス
 *
 * ユーザデータ登録結果JSONモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Json_RegisterData extends App_Json
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
     * ユーザデータ登録結果JSONの生成
     *
     * @param array $result ユーザデータ登録結果
     * @return string JSON文字列
     */
    public function formatToJson($results)
    {
        $response = array(
            'user_data' => $results
        );
        return App_Json::encode($response);
    }
}
