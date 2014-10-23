<?php
/**
 * applicaiton/modules/api/models/Xml/GetParamList.php
 *
 * 利用可能データリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetParamList.php 74 2014-03-14 07:54:11Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_GetParamList クラス
 *
 * パラメータリストXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_GetParamList extends App_Xml
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
     * パラメータリストXML DOMの生成
     *
     * @param array $result パラメータリスト
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function formatToXml($results)
    {
        $rootTag   = self::WGS_ROOT_TAG;
        $dom       = $this->genDOMDocumentNS($rootTag);
        $docRoot   = $dom->documentElement;
        $libParams = $dom->createElement('parameters');

        foreach ($results as $result) {
            // disalbled指定のものは無視する。
            if (array_key_exists('disabled', $result) && $result['disabled']) {
                continue;
            }

            $param = $dom->createElement('parameter');
            $param->setAttribute('id', $result['id']);

            $name = $param->appendChild($dom->createElement('label'));
            $name->appendChild($dom->createTextNode($result['label']));
            $type = $param->appendChild($dom->createElement('type'));
            $type->appendChild($dom->createTextNode($result['type']));
            $foramt = $param->appendChild($dom->createElement('format'));
            $foramt->appendChild($dom->createTextNode($result['format']));

            $validateRules = $result['validate'];
            $validate = $param->appendChild($dom->createElement('validate'));
            foreach ($validateRules as $key => $ruleDef) {
                switch($key) {
                    case 'required':
                    case 'digits':
                    case 'alphanum':
                    case 'climate':
                        $ruleDef = 'false';
                        if ($ruleDef) {
                            $ruleDef = 'true';
                        }
                        break;
                    case 'range':
                        if (is_array($ruleDef)) {
                            $ruleDef = implode(',', $ruleDef);
                        }
                        break;
                    default:
                        break;
                }
                $rule = $validate->appendChild($dom->createElement('rule'));
                $rule->setAttribute('id', $key);
                $rule->appendChild($dom->createTextNode($ruleDef));
            }

            switch($result['type']) {
                case 'checkbox':
                case 'select':
                    if (array_key_exists('items', $result)) {
                        $itemSet = $result['items'];
                        $items   = $param->appendChild($dom->createElement('items'));
                        foreach ($itemSet as $key => $itemValue) {
                            $item = $items->appendChild($dom->createElement('item'));
                            $item->setAttribute('id', $itemValue['key']);
                            $item->appendChild($dom->createTextNode($itemValue['value']));
                        }
                    }
                    if (array_key_exists('relational_items', $result)) {
                        $childItemSet = $result['relational_items'];
                        $childItems   = $param->appendChild($dom->createElement('relational_items'));
                        $childItems->setAttribute('parent_item', $result['parent_item']);
                        foreach ($childItemSet as $key => $itemSet) {
                            $childItem = $childItems->appendChild($dom->createElement('relational_item'));
                            $childItem->setAttribute('id', $key);
                            $items = $childItem->appendChild($dom->createElement('items'));
                            foreach ($itemSet as $key => $itemValue) {
                                $item = $items->appendChild($dom->createElement('item'));
                                $item->setAttribute('id', $itemValue['key']);
                                $item->appendChild($dom->createTextNode($itemValue['value']));
                            }
                        }
                    }
                    break;
                default:
                    break;
            }

            if (!App_Utils::isEmpty($result['description'])) {
                $desc = $param->appendChild($dom->createElement('description'));
                $desc->appendChild($dom->createCDATASection($result['description']));
            }
            $libParams->appendChild($param);
        }
        $docRoot->appendChild($libParams);
        return $dom;
    }
}
