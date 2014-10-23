<?php
/**
 * applicaiton/modules/api/models/Xml/GetDataTypeList.php
 *
 * データ種別リストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetDataTypeList.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetDataTypeList クラス
 *
 * データ種別リストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetDataTypeList extends App_Xml
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
     * データ種別リストXML DOMの生成
     *
     * @param array $result データ種別リスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = $this->genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $availableData = $dom->createElement('data_types');

        foreach ($results as $result) {
            $data = $dom->createElement('data_type');
            $data->setAttribute('id', $result['data_type_id']);

            $name = $data->appendChild($dom->createElement('name'));
            $name->appendChild($dom->createTextNode($result['data_type']));

            if (!App_Utils::isEmpty($result['description'])) {
                $desc = $data->appendChild($dom->createElement('description'));
                $desc->appendChild($dom->createCDATASection($result['description']));
            }
            $availableData->appendChild($data);
        }
        $docRoot->appendChild($availableData);
        return $dom;
    }
}
