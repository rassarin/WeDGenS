<?php
/**
 * applicaiton/modules/api/models/Xml/MetXml.php
 *
 * MetXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: MetXml.php 86 2014-03-19 09:03:41Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Xml
*/

/**
 * Api_Model_Xml_MetXml クラス
 *
 * MetXMLモデルクラス
 *
 * @category    App
 * @package     Model
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Model_Xml_MetXml extends App_Xml
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
     * データソースリストの取得
     *
     * @param string $xml XML文字列
     * @return array データソースリスト
     */
    public function getDataResources($xml)
    {
        $results = array();
        $this->setXml($xml);
        $sources = $this->searchXpath('/sources/source');
        foreach ($sources as $sourceObj) {
            $record = array();
            $record['source_id']   = (string) $sourceObj['id'];
            $record['source_name'] = (string) $sourceObj->name;
            $record['area_nw_lat'] = (string) $sourceObj->area['nw_lat'];
            $record['area_nw_lon'] = (string) $sourceObj->area['nw_lon'];
            $record['area_se_lat'] = (string) $sourceObj->area['se_lat'];
            $record['area_se_lon'] = (string) $sourceObj->area['se_lon'];
            array_push($results, $record);
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * リージョンリストの取得
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @param string $xml XML文字列
     * @return array リージョンリスト
     */
    public function getRegions($input, $xml)
    {
        $results = array();

        $this->setXml($xml);
        $query   = $this->_genRegionXpathQuery($input);
        $regions = $this->searchXpath($query);
        foreach ($regions as $regionObj) {
            $record = array();
            $record['region_id']   = (string) $regionObj['id'];
            $record['region_name'] = (string) $regionObj->name;
            $record['area_nw_lat'] = (string) $regionObj->area['nw_lat'];
            $record['area_nw_lon'] = (string) $regionObj->area['nw_lon'];
            $record['area_se_lat'] = (string) $regionObj->area['se_lat'];
            $record['area_se_lon'] = (string) $regionObj->area['se_lon'];
            array_push($results, $record);
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * ステーションリストの取得
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @param string $xml XML文字列
     * @param array $climateDataDef 気候データ定義
     * @return array ステーションリスト
     */
    public function getStations($input, $xml, $climateDataDef = array())
    {
        $results = array();

        $this->setXml($xml);
        $query      = $this->_genStationXpathQuery($input);
        $stations   = $this->searchXpath($query);

        // ユーザ指定観測年終了(デフォルト値は今年)
        $targetEndYear = App_Utils::getCurrentYear();
        if (!App_Utils::isEmpty($input->getEscaped('end_year'))) {
            $targetEndYear = $input->getEscaped('end_year');
        }
        // ユーザ指定観測年開始(デフォルト値は0年)
        $targetBeginYear = 0;
        if (!App_Utils::isEmpty($input->getEscaped('begin_year'))) {
            $targetBeginYear = $input->getEscaped('begin_year');
        }
        if ($targetBeginYear > $targetEndYear) {
            list($targetEndYear, $targetBeginYear) = array($targetBeginYear, $targetEndYear);
        }

        // 気候データの有無を抽出条件とするかどうか
        $useClimateCondition = false;
        $requiredDuration    = null;
        $requiredClimate     = array();
        $optionalClimate     = array();
        if (!App_Utils::isEmpty($climateDataDef)) {
            $useClimateCondition = true;
            $requiredDuration = $climateDataDef['use_duration'];
            if (array_key_exists('required', $climateDataDef)) {
                $requiredClimate = $climateDataDef['required'];
            }
            if (array_key_exists('optional', $climateDataDef)) {
                $optionalClimate = $climateDataDef['optional'];
            }
        }

        foreach ($stations as $station) {
            $record = array();
            $record['station_id']   = (string) $station['id'];
            $record['station_name'] = (string) $station->name;
            $record['place_alt']    = (string) $station->place['alt'];
            $record['place_lat']    = (string) $station->place['lat'];
            $record['place_lon']    = (string) $station->place['lon'];
            $record['start']        = (string) $station->operational['start'];
            $record['end']          = (string) $station->operational['end'];

            // データが記録されている観測年の取得
            $startYear = 0;
            $startDate = null;
            if (!App_Utils::isEmpty($record['start'])) {
                $startDate = App_Utils::setZendDate($record['start']);
                $startYear = $startDate->toString('Y');
            }
            $endYear = App_Utils::getCurrentYear();
            $endDate = App_Utils::getZendDate();
            if (!App_Utils::isEmpty($record['end'])) {
                $endDate = App_Utils::setZendDate($record['end']);
                $endYear = $endDate->toString('Y');
            }

            // CLIGENの場合：データが1年分以上あるか
            $hasOneYearOver = true;
            if (!App_Utils::isEmpty($input->getEscaped('lib_id'))) {
                $libId = $input->getEscaped('lib_id');
                if ($libId == App_Const::LIBRARY_TYPE_CLIGEN) {
                    $endDate->sub(1, Zend_Date::YEAR);
                    $onYearAgo = $endDate->toString('Y-m-d H:i:s');
                    // 観測終了年の1年前の日付が観測開始年より古い場合、1年未満
                    if (App_Utils::isLater($startDate->toString('Y-m-d H:i:s'), $onYearAgo)) {
                        $hasOneYearOver = false;
                    }
                }
            }

            $hasOptionalClimate = false;
            $record['elements'] = array();
            $hasElement  = 0;
            $elementList = $station->elements->element;
            foreach ($elementList as $element) {
                $elementRecord  = array();
                $durationRecord = array();

                $elementId = (string) $element['id'];
                $elementRecord['element_id'] = preg_replace('/\s+/u', '', $elementId);
                if ($useClimateCondition) {
                    // 必須気候データがあるか
                    if (in_array($elementRecord['element_id'], $requiredClimate)) {
                        $hasElement ++;
                    }
                    // 選択可能気候データがあるか
                    if (in_array($elementRecord['element_id'], $optionalClimate)) {
                        $hasOptionalClimate = true;
                    }
                } else {
                    $hasElement ++;
                }

                $hasDuration = false;
                $durations   = $element->duration;
                foreach ($durations as $duration) {
                    $durationId = (string) $duration['id'];
                    if ($useClimateCondition) {
                        // 必須デュレーションデータがあるか
                        if ($durationId == $requiredDuration) {
                            $hasDuration = true;
                        }
                    } else {
                        $hasDuration = true;
                    }
                    array_push($durationRecord, $durationId);
                }
                $elementRecord['duration'] = $durationRecord;
                if (!App_Utils::isEmpty($elementRecord['duration'])) {
                    if ($hasDuration) {
                        array_push($record['elements'], $elementRecord);
                    }
                }
            }

            if (!App_Utils::isEmpty($record['elements'])) {
                $hasTargetClimate = false;
                if ($hasElement >= count($requiredClimate)) {
                    $hasTargetClimate = true;
                } else {
                    if ($hasOptionalClimate && App_Utils::isEmpty($requiredClimate)) {
                        $hasTargetClimate = true;
                    }
                }
                if ($hasTargetClimate) {
                    if (($targetBeginYear >= $startYear) || ($targetEndYear <= $endYear)) {
                        if ($hasOneYearOver) {
                            array_push($results, $record);
                        }
                    }
                }
            }
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト結果の取得
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @param string $xml XML文字列
     * @param array $xslConfig XSL設定
     * @return string XSLT適用後文字列
     */
    public function getResult($input, $xml, $xslConfig)
    {
        $result = null;
        $this->setXml($xml);
        $formt = $input->getEscaped('format');
        if ($formt == App_Const::RESULT_FORMAT_XML) {
            $result = $this->getXml();
        } else {
            switch($formt) {
                case App_Const::RESULT_FORMAT_HTML:
                    $xslFile = $xslConfig['html'];
                    break;
                case App_Const::RESULT_FORMAT_CSV:
                    $xslFile = $xslConfig['csv'];
                    break;
                case App_Const::RESULT_FORMAT_CHART:
                    $xslFile = $xslConfig['chart'];
                    break;
                default:
                    break;
            }
            $this->setStyleSheet($xslFile);
            $result = $this->transform($this->getDOM());
        }
        return $result;
    }

    // ------------------------------------------------------------------ //

    /**
     * 実行結果XMLにコメントを付与
     *
     * @param array $requestInfo リクエスト情報
     * @return string XML文字列
     */
    public function addRequestInfoComment($xml, $requestInfo)
    {
        $this->setXml($xml);
        $dom = DOMDocument::loadXML($this->getSimpleXMLDocument()->asXML());
        $dom->formatOutput       = true;
        $dom->preserveWhiteSpace = false;
        $dom->encoding = "UTF-8";
        $docRoot = $dom->documentElement;
        $comment = App_Json::decode($requestInfo['params']);
        $comment['library'] = array(
            'lib_id'   => $requestInfo['lib_id'],
            'lib_name' => $requestInfo['lib_name'],
        );

        $comment = $docRoot->appendChild(
            $dom->createComment(
                "Request parameters JSON you send are as follows: \n" .
                App_Json::prettyPrint(App_Json::encode($comment))
            )
        );
        return $dom->saveXML();
    }

    // ------------------------------------------------------------------ //

    /**
     * リージョン取得XPathクエリーの生成
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return string XPathクエリー
     */
    private function _genRegionXpathQuery($input)
    {
        $query = '/stations/source';
        $regionId  = $input->getEscaped('region_id');

        // リージョンID指定で取得
        if (!App_Utils::isEmpty($regionId)) {
            $query .= '/region[@id="' . $regionId . '"]';
        } else {
            $query .= '/region';
        }
        return $query;
    }

    // ------------------------------------------------------------------ //

    /**
     * ステーション取得XPathクエリーの生成
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return string XPathクエリー
     */
    private function _genStationXpathQuery($input)
    {
        $query = '/stations/source';
        $regionId  = $input->getEscaped('region_id');
        $stationId = $input->getEscaped('station_id');

        // リージョン定義が無いデータソースの場合
        if ($regionId == App_Const::REGION_NONE) {
            $regionId = null;
        }

        if (App_Utils::isEmpty($regionId) && App_Utils::isEmpty($stationId)) {
            // 北西を左上頂点、南西を右下頂点とする矩形範囲指定で取得
            $nwLatPoint = $input->getEscaped('nw_lat');
            $nwLonPoint = $input->getEscaped('nw_lon');
            $seLatPoint = $input->getEscaped('se_lat');
            $seLonPoint = $input->getEscaped('se_lon');

            $query .= '//station/place';
            if (!App_Utils::isEmpty($nwLatPoint)) {
                $query .= '[@lat<="' . $nwLatPoint . '"]';
            }
            if (!App_Utils::isEmpty($nwLatPoint)) {
                $query .= '[@lon>="' . $nwLonPoint . '"]';
            }
            if (!App_Utils::isEmpty($nwLatPoint)) {
                $query .= '[@lat>="' . $seLatPoint . '"]';
            }
            if (!App_Utils::isEmpty($nwLatPoint)) {
                $query .= '[@lon<="' . $seLonPoint . '"]';
            }
            $query .= '/parent::*';
        } else {
            // リージョンID、ステーションID指定で取得
            if (!App_Utils::isEmpty($regionId)) {
                $query .= '/region[@id="' . $regionId . '"]';
                if (!App_Utils::isEmpty($stationId)) {
                    $query .= '/station[@id="' . $stationId . '"]';
                } else {
                    $query .= '/station';
                }
            } else {
                if (!App_Utils::isEmpty($stationId)) {
                    $query .= '//station[@id="' . $stationId . '"]';
                } else {
                    $query .= '/station';
                }
            }
        }
        return $query;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト結果MetXML取得XPathクエリーの生成
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return string XPathクエリー
     */
    private function _genResultXpathQuery($input)
    {
        $query = '/dataset';
        return $query;
    }
}
