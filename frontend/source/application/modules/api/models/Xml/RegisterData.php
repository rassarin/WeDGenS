<?php
/**
 * applicaiton/modules/api/models/Xml/RegisterData.php
 *
 * ユーザデータ登録結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: RegisterData.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_RegisterData クラス
 *
 * ユーザデータ登録結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_RegisterData extends App_Xml
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
     * リクエスト送信結果XML DOMの生成
     *
     * @param array $result リクエスト送信結果
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($result)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = $this->genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $request = $dom->createElement('user_data');
        if (array_key_exists('user_data_id', $result)) {
            $request->setAttribute('id', $result['user_data_id']);
        }
        if (array_key_exists('register_status', $result)) {
            $status = $request->appendChild($dom->createElement('status'));
            $status->appendChild($dom->createTextNode($result['register_status']));
        }
        $docRoot->appendChild($request);
        return $dom;
    }
}
