<?php
/**
 * applicaiton/modules/api/models/Xml/GetRegionList.php
 *
 * リージョンリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetRegionList.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetRegionList クラス
 *
 * リージョンリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetRegionList extends App_Xml
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
     * リージョンリストXML DOMの生成
     *
     * @param array $result リージョンリスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = $this->genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $regions = $dom->createElement('regions');

        foreach ($results as $result) {
            $region = $dom->createElement('region');
            $region->setAttribute('id', $result['region_id']);

            $name = $region->appendChild($dom->createElement('name'));
            $name->appendChild($dom->createTextNode($result['region_name']));

            $area = $region->appendChild($dom->createElement('area'));
            $area->setAttribute('nw_lat', $result['area_nw_lat']);
            $area->setAttribute('nw_lon', $result['area_nw_lon']);
            $area->setAttribute('se_lat', $result['area_se_lat']);
            $area->setAttribute('se_lon', $result['area_se_lon']);

            $regions->appendChild($region);
        }
        $docRoot->appendChild($regions);

        return $dom;
    }
}
