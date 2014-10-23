<?php
/**
 * applicaiton/modules/api/models/Xml/GetStationList.php
 *
 * ステーションリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetStationList.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetStationList クラス
 *
 * ステーションリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetStationList extends App_Xml
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
     * ステーションリストXML DOMの生成
     *
     * @param array $result ステーションリスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag  = self::WGS_ROOT_TAG;
        $dom      = $this->genDOMDocumentNS($rootTag);
        $docRoot  = $dom->documentElement;
        $stations = $dom->createElement('stations');

        foreach ($results as $result) {
            $station = $dom->createElement('station');
            $station->setAttribute('id', $result['station_id']);

            $name = $station->appendChild($dom->createElement('name'));
            $name->appendChild($dom->createTextNode($result['station_name']));

            $place = $station->appendChild($dom->createElement('place'));
            if (!App_Utils::isEmpty($result['place_alt'])) {
                $place->setAttribute('alt', $result['place_alt']);
            }
            if (!App_Utils::isEmpty($result['place_lat'])) {
                $place->setAttribute('lat', $result['place_lat']);
            }
            if (!App_Utils::isEmpty($result['place_lon'])) {
                $place->setAttribute('lon', $result['place_lon']);
            }

            $operational = $station->appendChild($dom->createElement('operational'));
            $operational->setAttribute('start', $result['start']);
            if (!App_Utils::isEmpty($result['end'])) {
                $operational->setAttribute('end', $result['end']);
            }

            $elements = $station->appendChild($dom->createElement('elements'));
            foreach ($result['elements'] as $elementRecord) {
                $element = $dom->createElement('element');
                $element->setAttribute('id', $elementRecord['element_id']);

                if (!App_Utils::isEmpty($elementRecord['duration'])) {
                    foreach ($elementRecord['duration'] as $durationRecord) {
                        $duration = $dom->createElement('duration');
                        $duration->setAttribute('id', $durationRecord);
                        $element->appendChild($duration);
                    }
                }
                $elements->appendChild($element);
            }
            $stations->appendChild($station);
        }
        $docRoot->appendChild($stations);

        return $dom;
    }
}
