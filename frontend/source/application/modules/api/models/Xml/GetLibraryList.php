<?php
/**
 * applicaiton/modules/api/models/Xml/GetLibraryList.php
 *
 * 利用可能ライブラリリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetLibraryList.php 19 2014-02-26 11:43:31Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetLibraryList クラス
 *
 * 利用可能ライブラリリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetLibraryList extends App_Xml
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
     * 利用可能ライブラリリストXML DOMの生成
     *
     * @param array $result 利用可能ライブラリリスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = $this->genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $availableLibs = $dom->createElement('libraries');

        foreach ($results as $result) {
            $library = $dom->createElement('library');
            $library->setAttribute('id', $result['lib_id']);

            $name = $library->appendChild($dom->createElement('name'));
            $name->appendChild($dom->createTextNode($result['lib_name']));

            if (!App_Utils::isEmpty($result['description'])) {
                $desc = $library->appendChild($dom->createElement('description'));
                $desc->appendChild($dom->createCDATASection($result['description']));
            }
            $availableLibs->appendChild($library);
        }
        $docRoot->appendChild($availableLibs);
        return $dom;
    }
}
