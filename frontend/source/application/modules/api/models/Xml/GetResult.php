<?php
/**
 * applicaiton/modules/api/models/Xml/GetResult.php
 *
 * リクエスト実行結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetResult.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetResult クラス
 *
 * リクエスト実行結果XMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetResult extends App_Xml
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
     * リクエスト実行結果XML DOMの生成
     *
     * @param array $xml MetXML文字列
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($xml)
    {
        return $xml;
    }
}
