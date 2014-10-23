<?php
/**
 * applicaiton/modules/api/models/Xml/SendRequest.php
 *
 * リクエスト送信結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: SendRequest.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_SendRequest クラス
 *
 * リクエスト送信結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_SendRequest extends App_Xml
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
        $request = $dom->createElement('request');
        if (array_key_exists('request_id', $result)) {
            $request->setAttribute('id', $result['request_id']);
        }
        if (array_key_exists('request_status', $result)) {
            $status = $request->appendChild($dom->createElement('status'));
            $status->appendChild($dom->createTextNode($result['request_status']));
        }
        $docRoot->appendChild($request);
        return $dom;
    }
}
