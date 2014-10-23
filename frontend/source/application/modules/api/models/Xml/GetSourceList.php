<?php
/**
 * applicaiton/modules/api/models/Xml/GetSourceList.php
 *
 * ソースリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetSourceList.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetSourceList クラス
 *
 * ソースリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetSourceList extends App_Xml
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
     * ソースリストXML DOMの生成
     *
     * @param array $result ソースリスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = $this->genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $sources = $dom->createElement('sources');

        foreach ($results as $result) {
            $source = $dom->createElement('source');
            $source->setAttribute('id', $result['source_id']);

            $name = $source->appendChild($dom->createElement('name'));
            $name->appendChild($dom->createTextNode($result['source_name']));

            $area = $source->appendChild($dom->createElement('area'));
            $area->setAttribute('nw_lat', $result['area_nw_lat']);
            $area->setAttribute('nw_lon', $result['area_nw_lon']);
            $area->setAttribute('se_lat', $result['area_se_lat']);
            $area->setAttribute('se_lon', $result['area_se_lon']);

            $sources->appendChild($source);
        }
        $docRoot->appendChild($sources);

        return $dom;
    }
}
