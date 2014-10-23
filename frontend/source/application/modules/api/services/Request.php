<?php
/**
 * applicaiton/modules/api/services/Request.php
 *
 * リクエスト管理サービスクラス
 *
 * リクエスト管理サービス。
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Request.php 88 2014-03-19 11:15:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Service_Api
*/

/**
 * Api_Service_Request クラス
 *
 * リクエスト管理サービスクラス
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Service_Request extends Service_Api
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // 共通初期化処理を記述
    }

    // ------------------------------------------------------------------ //

    /**
     * ライブラリリストの取得。
     *
     * @return array ライブラリリスト
     */
    public function getLibraries()
    {
        return $this->getModuleModel('library')->fetchAll();
    }

    // ------------------------------------------------------------------ //

    /**
     * データリストの取得。
     *
     * @return array データリスト
     */
    public function getDataTypes()
    {
        return $this->getModuleModel('data-type')->fetchAll();
    }

    // ------------------------------------------------------------------ //

    /**
     * パラメータリストの取得。
     *
     * @param integer $libId ライブラリID
     * @return array パラメータリスト
     */
    public function getLibraryParams($libId)
    {
        return $this->getModuleModel('library')->getParams($libId);
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストの送信。
     *
     * @param Zend_Filter_Input $dataInput ユーザ入力(入力データ部)
     * @param Zend_Filter_Input $paramInput ユーザ入力(ライブラリパラメータ部)
     * @return array 送信結果
     */
    public function sendRequest($dataInput, $paramInput)
    {

        $requestId = null;
        $results   = array(
            'request_status' => App_Const::FAILURE_CODE
        );
        $this->getAdapter()->beginTransaction();
        try {
            // 指定気候データを持つステーションが存在するかチェック
            if ($dataInput->getEscaped('data_type_id') == App_Const::DATA_TYPE_MET_BROKER) {
                $stations = $this->getStations($dataInput);
                if (App_Utils::isEmpty($stations)) {
                    throw new App_Exception_Xml(
                        '指定条件のデータを持つステーションがありません。',
                        App_Exception::APP_VIOLATION_ERROR
                    );
                }
            }

            $paramJson = $this->generateRequestJson($dataInput, $paramInput);
            $requestId = $this->getModuleModel('request')
                              ->registerRequest($dataInput, $paramJson);
            if (!$requestId) {
                throw new App_Exception(
                    'リクエストIDが取得できません。',
                    App_Exception::APP_SYSTEM_ERROR
                );
            }
            $this->getAdapter()->commit();
        } catch (Exception $exception) {
            $this->getAdapter()->rollBack();
            throw $exception;
        }

        // リクエストパラメータファイルをHDFSに格納
        $libId  = $dataInput->getEscaped('lib_id');
        $stored = $this->storeRequestParamToHdfs($libId, $requestId, $paramJson);
        if (!$stored) {
            throw new App_Exception(
                'HDFSへの格納に失敗しました。',
                App_Exception::APP_IO_ERROR
            );
        }

        // ジェネレータWebAPIへリクエストを送信
        $success = $this->sendToWgsGeneratorApi($requestId);
        if (!$success) {
            $canceled = $this->getModuleModel('request')
                             ->cancelRequest($requestId);
            $removed  = $this->removeRequestParamFromHdfs($requestId);
            throw new App_Exception(
                'WGSジェネレータWebAPIへのリクエスト送信に失敗しました。',
                App_Exception::APP_API_ERROR
            );
        }
        $results['request_id']     = $requestId;
        $results['request_status'] = App_Const::SUCCESS_CODE;

        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * パラメータJSONの生成。
     *
     * @param Zend_Filter_Input $dataInput ユーザ入力(入力データ部)
     * @param Zend_Filter_Input $paramInput ユーザ入力(ライブラリパラメータ部)
     * @return string パラメータJSON
     */
    public function generateRequestJson($dataInput, $paramInput)
    {
        $paramJson  = array();
        $dataBlock  = array();
        $dataTypeId = $dataInput->getEscaped('data_type_id');

        // 観測年終了(デフォルト値は今年)
        $endYear = App_Utils::getCurrentYear();
        if (!App_Utils::isEmpty($dataInput->getEscaped('end_year'))) {
            $endYear = $dataInput->getEscaped('end_year');
        }
        // 観測年開始(デフォルト値は終了年 - 10)
        $beginYear = $endYear - 10;
        if (!App_Utils::isEmpty($dataInput->getEscaped('begin_year'))) {
            $beginYear = $dataInput->getEscaped('begin_year');
        }
        if ($beginYear > $endYear) {
            list($endYear, $beginYear) = array($beginYear, $endYear);
        }

        $rangeTypeId = null;
        switch($dataTypeId) {
            case App_Const::DATA_TYPE_USER_DATA:
                $rangeTypeId = App_Const::RANGE_TYPE_USER;
                $dataBlock   = array(
                    'range_type' => $rangeTypeId,
                );
                break;
            case App_Const::DATA_TYPE_MET_BROKER:
                $rangeTypeId = $dataInput->getEscaped('range_type_id');
                $dataBlock   = array(
                    'range_type' => $rangeTypeId,
                    'source_id'  => $dataInput->getEscaped('source_id'),
                    'duration'   => $dataInput->getEscaped('duration_id'),
                    'begin_year' => $beginYear,
                    'end_year'   => $endYear,
                );
                break;
        }

        switch($rangeTypeId) {
            case App_Const::RANGE_TYPE_AREA:
                $nwLat = $dataInput->getEscaped('nw_lat');
                $seLat = $dataInput->getEscaped('se_lat');
                if ($seLat > $nwLat) {
                    list($seLat, $nwLat) = array($nwLat, $seLat);
                }
                $nwLon = $dataInput->getEscaped('nw_lon');
                $seLon = $dataInput->getEscaped('se_lon');
                if ($nwLon > $seLon) {
                    list($seLon, $nwLon) = array($nwLon, $seLon);
                }
                $dataBlock['area'] = array(
                    'nw_lat' => $nwLat,
                    'nw_lon' => $nwLon,
                    'se_lat' => $seLat,
                    'se_lon' => $seLon,
                );
                break;
            case App_Const::RANGE_TYPE_USER:
                $dataBlock['user'] = $dataInput->getEscaped('user_data_id');
                break;
            case App_Const::RANGE_TYPE_POINT:
                $dataBlock['point'] = array();
                if (is_array($dataInput->getEscaped('station_id'))) {
                    $stations = $dataInput->getEscaped('station_id');
                    $regionId = $dataInput->getEscaped('region_id');
                    foreach ($stations as $station) {
                        $point = array();
                        if ($regionId == App_Const::REGION_NONE) {
                            $regionId = null;
                        }
                        if (!App_Utils::isEmpty($regionId)) {
                            $point['region_id'] = $regionId;
                        }
                        $point['station_id'] = $station;
                        array_push($dataBlock['point'], $point);
                    }
                } else {
                    $point = array();
                    $regionId = $dataInput->getEscaped('region_id');
                    if ($regionId == App_Const::REGION_NONE) {
                        $regionId = null;
                    }
                    if (!App_Utils::isEmpty($regionId)) {
                        $point['region_id'] = $regionId;
                    }
                    $point['station_id'] = $dataInput->getEscaped('station_id');
                    array_push($dataBlock['point'], $point);
                }
                break;
            case App_Const::RANGE_TYPE_MESH:
                $dataBlock['mesh'] = array();
                break;
        }
        $paramJson['data']   = $dataBlock;
        $paramJson['params'] = $paramInput->getEscaped();
        $json = App_Json::encode($paramJson);
        $this->debugLog('リクエストJSON : ' . $json);

        return $json;
    }

    // ------------------------------------------------------------------ //

    /**
     * ジェネレータWebAPIへリクエストを送信。
     *
     * @param string $requestId リクエストID
     * @return boolean 真：リクエスト送信成功
     */
    public function sendToWgsGeneratorApi($requestId)
    {
        try {
            $genratorConfig = $this->getGenratorConfig();
            $generatorUrl   = $this->getWgsGeneratorUrl($requestId);
            $genratorParams = $genratorConfig['params'];
            $params = array(
                $genratorParams['request_id'] => $requestId,
            );
            $this->debugLog($generatorUrl);

            $responseJson = $this->getWgsGeneratorWebApi()
                                 ->access($generatorUrl, $params);
            if (array_key_exists('status', $responseJson)) {
                if ($responseJson['status'] == 0) {
                    return true;
                }
            }
            return false;
        } catch (Exception $exception) {
            $this->errorLog($exception);
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * ジェネレータWebAPI URLの取得。
     *
     * @param string $requestId リクエストID
     * @return string ジェネレータWebAPI URL
     */
    public function getWgsGeneratorUrl($requestId)
    {
        $genratorConfig = $this->getGenratorConfig();

        $requestInfo = $this->getModuleModel('request')->find($requestId);
        $ipAddr      = $requestInfo['ip_addr'];
        $contextName = $requestInfo['context_name'];
        if (!preg_match('/\//u', $contextName)) {
            $contextName = '/' . $contextName;
        }

        $url = 'http://'
             . $requestInfo['ip_addr']
             . ':' . $genratorConfig['port']
             . $contextName
             . '/' . $genratorConfig['action'];

        $validator = App_Validate_Common::setUriValidator();
        if (!$validator->isValid($url)) {
            throw new App_Exception(
                'ジェネレータURLが取得できません。',
                App_Exception::APP_VALIDATE_ERROR
            );
        }
        return $url;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストの進捗確認。
     *
     * @param string $requestId リクエストID
     * @return array 進捗確認結果
     */
    public function checkRequest($requestId)
    {
        return $this->getModuleModel('request')->check($requestId);
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト実行結果の取得。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return string リクエスト実行結果
     */
    public function getResult($input)
    {
        $result = null;
        try {
            $requestId       = $input->getEscaped('request_id');
            $requestInfo     = $this->checkRequest($requestId);
            $requestStatusId = $requestInfo['request_status_id'];
            if ($requestStatusId != App_Const::REQUEST_STAT_FINISHED) {
                switch($requestStatusId) {
                    case App_Const::REQUEST_STAT_ACCEPT:
                    case App_Const::REQUEST_STAT_RUNNING:
                        throw new App_Exception_Constraint(
                            'リクエストの処理が完了していません。',
                            App_Exception::APP_SYSTEM_ERROR
                        );
                        break;
                    default:
                        throw new App_Exception_Constraint(
                            'リクエストの処理でエラーが発生しました。',
                            App_Exception::APP_SYSTEM_ERROR
                        );
                        break;
                }
            }

            $xml = $this->getResultXml($requestId);
            if ($xml === false) {
                throw new App_Exception(
                    'リクエスト結果MetXMLファイルのアクセスに失敗しました。',
                    App_Exception::APP_IO_ERROR
                );
            }
            $checkOnlyFlag = $input->getEscaped('check_only');
            if ($checkOnlyFlag) {
                return true;
            }

            // リクエスト情報をコメントとして付与
            $xml = $this->getXmlModuleModel('met-xml')
                        ->addRequestInfoComment($xml, $requestInfo);

            $formt = $input->getEscaped('format');
            if ($formt == App_Const::RESULT_FORMAT_ZIP) {
                $result    = $this->getResultArchives($requestId, $xml);
            } else {
                $xslConfig = $this->getXsltConfig();
                $result    = $this->getXmlModuleModel('met-xml')
                                  ->getResult($input, $xml, $xslConfig);
            }

        } catch (Exception $exception) {
            throw $exception;
        }
        return $result;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザデータの登録。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @param string $tmpFile アップロード一時ファイルパス
     * @return array ユーザデータ登録結果
     */
    public function registerUserData($input, $tmpFile)
    {
        $results = array(
            'register_status' => App_Const::FAILURE_CODE
        );
        $this->getAdapter()->beginTransaction();
        try {
            $verified = $this->validateUploadMetXml($tmpFile);
            if (!$verified) {
                $lastError = error_get_last();
                $message   = 'XMLスキーマのチェックで不正と判定されました。';
                if (!App_Utils::isEmpty($lastError)) {
                    $message .= '：' . $lastError['message'];
                }
                throw new App_Exception_Xml(
                    $message,
                    App_Exception::APP_VALIDATE_ERROR
                );
            }

            $userDataId = $this->getModuleModel('data')->registerUserData($input);
            if (!$userDataId) {
                throw new App_Exception(
                    'ユーザデータIDが取得できません。',
                    App_Exception::APP_SYSTEM_ERROR
                );
            }
            $success = $this->storeUserDataToHdfs($userDataId, $tmpFile);
            if (!$success) {
                throw new App_Exception(
                    'HDFSへの格納に失敗しました。',
                    App_Exception::APP_IO_ERROR
                );
            }
            $results['user_data_id']    = $userDataId;
            $results['register_status'] = App_Const::SUCCESS_CODE;
            $this->getAdapter()->commit();
        } catch (Exception $exception) {
            $this->getAdapter()->rollBack();
            throw $exception;
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * データソースリストの取得。
     *
     * @param integer $dataTypeId データ種別ID
     * @return array データソースリスト
     */
    public function getDataSources($dataTypeId)
    {
        $results = array();
        try {
            $xml = $this->getSourceListXml($dataTypeId);
            if ($xml === false) {
                throw new App_Exception(
                    'データソース定義XMLファイルのアクセスに失敗しました。',
                    App_Exception::APP_IO_ERROR
                );
            }
            $results = $this->getXmlModuleModel('met-xml')
                            ->getDataResources($xml);
        } catch (Exception $exception) {
            throw $exception;
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * リージョンリストの取得。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return array リージョンリスト
     */
    public function getRegions($input)
    {
        $results = array();
        try {
            $dataTypeId = $input->getEscaped('data_type_id');
            $sourceId   = $input->getEscaped('source_id');
            $xml = $this->getStationListXml($dataTypeId, $sourceId);
            if ($xml === false) {
                throw new App_Exception(
                    'ステーション定義XMLファイルのアクセスに失敗しました。',
                    App_Exception::APP_IO_ERROR
                );
            }
            $results = $this->getXmlModuleModel('met-xml')
                            ->getRegions($input, $xml);
        } catch (Exception $exception) {
            throw $exception;
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * ステーションリストの取得。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @return array ステーションリスト
     */
    public function getStations($input)
    {
        $results = array();
        try {
            $libId      = $input->getEscaped('lib_id');
            $dataTypeId = $input->getEscaped('data_type_id');
            $sourceId   = $input->getEscaped('source_id');

            $climateDataDef = array();
            if (!App_Utils::isEmpty($libId)) {
                $climateDataDef = $this->getModuleModel('library')
                                       ->getClimateDataDef($libId);
            }
            $xml = $this->getStationListXml($dataTypeId, $sourceId);
            if ($xml === false) {
                throw new App_Exception(
                    'ステーション定義XMLファイルのアクセスに失敗しました。',
                    App_Exception::APP_IO_ERROR
                );
            }

            $results = $this->getXmlModuleModel('met-xml')
                            ->getStations($input, $xml, $climateDataDef);
        } catch (Exception $exception) {
            throw $exception;
        }
        return $results;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザデータファイルをHDFSに格納する。
     *
     * @param string $userDataId ユーザデータID
     * @param string $tmpFile アップロード一時ファイルパス
     * @return boolean 真：HDFSに格納成功
     */
    public function storeUserDataToHdfs($userDataId, $tmpFile)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsPutScript();

        $hdfsFile = $hadoopCmdApi->getUserDataFilePath($userDataId);
        if (!$hdfsFile) {
            return false;
        }

        /*
         *  HDFS格納コマンドの引数をセット
         *  - 第1引数：ローカルファイルパス
         *  - 第2引数：HDFS格納パス
         */
        array_push($args, $tmpFile);
        array_push($args, $hdfsFile);
        $hadoopCmdApi->setArgs($args);

        $results = $hadoopCmdApi->openScript();
        if (is_array($results)) {
            $line = array_shift($results);
            if (preg_match('/^SUCCESS/', $line)) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエストパラメータファイルをHDFSに格納する。
     *
     * @param string $libId ライブラリID
     * @param string $requestId リクエストID
     * @param string $paramJson リクエストパラメータJSON
     * @return boolean 真：HDFSに格納成功
     */
    public function storeRequestParamToHdfs($libId, $requestId, $paramJson)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsPutScript();

        $config = $this->getConfig();
        $tmpDir = $config['tmp_dir'];
        if (!is_dir($tmpDir)) {
            return false;
        }

        $paramFile = $tmpDir . '/' . App_Utils::generateUniqueId() . '.json';
        try {
            $tmp     = App_Json::decode($paramJson);
            $libInfo = $this->getModuleModel('library')->find($libId);
            $tmp['library'] = array(
                'lib_id'   => $libId,
                'lib_name' => $libInfo['lib_name'],
            );
            file_put_contents(
                $paramFile,
                App_Json::prettyPrint(App_Json::encode($tmp))
            );
        } catch (Exception $exception) {
            $this->errorLog($exception);
            return false;
        }

        $hdfsFile = $hadoopCmdApi->getRequestParamFilePath($requestId);
        if (!$hdfsFile) {
            return false;
        }

        /*
         *  HDFS格納コマンドの引数をセット
         *  - 第1引数：ローカルファイルパス
         *  - 第2引数：HDFS格納パス
         */
        array_push($args, $paramFile);
        array_push($args, $hdfsFile);
        $hadoopCmdApi->setArgs($args);

        $results = $hadoopCmdApi->openScript();
        if (is_array($results)) {
            $line = array_shift($results);
            if (preg_match('/^SUCCESS/', $line)) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFSのリクエストパラメータファイルを削除する。
     *
     * @param string $requestId リクエストID
     * @return boolean 真：削除成功
     */
    public function removeRequestParamFromHdfs($requestId)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsRemoveScript();

        $hdfsFile = $hadoopCmdApi->getRequestParamFilePath($requestId);
        if (!$hdfsFile) {
            return false;
        }

        /*
         *  HDFSファイル削除コマンドの引数をセット
         *  - 第1引数：HDFS格納パス
         */
        array_push($args, $hdfsFile);
        $hadoopCmdApi->setArgs($args);

        $results = $hadoopCmdApi->openScript();
        if (is_array($results)) {
            $line = array_shift($results);
            if (preg_match('/^SUCCESS/', $line)) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * リクエスト結果MetXMLをHDFSから取得する。
     *
     * @param string $requestId リクエストID
     * @return string MetXML文字列
     */
    public function getResultXml($requestId)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsCatScript();

        $hdfsFile = $hadoopCmdApi->getResultDataFilePath($requestId);
        if (!$hdfsFile) {
            return false;
        }

        /*
         *  HDFS上のファイル読み込みコマンドの引数をセット
         *  - 第1引数：HDFS格納パス
         */
        array_push($args, $hdfsFile);
        $hadoopCmdApi->setArgs($args);

        $results = $hadoopCmdApi->openScript();
        if (is_array($results)) {
            $line = $results[0];
            if (!preg_match('/^FAILURE/', $line)) {
                return implode('', $results);
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * データソース定義XMLをHDFSから取得する。
     *
     * @param integer $dataTypeId データ種別ID
     * @return string データソース定義XML文字列
     */
    public function getSourceListXml($dataTypeId)
    {
        $xml     = null;
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();

        $hdfsFile = $hadoopCmdApi->getSourceDataFilePath($dataTypeId);
        if (!$hdfsFile) {
            return false;
        }

        // キャッシュから取得
        $cache   = $this->getHdfsCache($hdfsFile);
        $cacheId = 'dataTypeId_'
                 . $dataTypeId
                 . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', basename($hdfsFile));
        $xml = $cache->load($cacheId);
        if ($xml) {
            return $xml;
        } else {
            /*
             *  HDFS上のファイル読み込みコマンドの引数をセット
             *  - 第1引数：HDFS格納パス
             */
            array_push($args, $hdfsFile);
            $hadoopCmdApi->setArgs($args);
            $hadoopCmdApi->setHdfsCatScript();

            $results = $hadoopCmdApi->openScript();
            if (is_array($results)) {
                $line = $results[0];
                if (!preg_match('/^FAILURE/', $line)) {
                    $xml = implode('', $results);
                    $cache->save($xml, $cacheId);
                    return $xml;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * ステーション定義XMLをHDFSから取得する。
     *
     * @param integer $dataTypeId データ種別ID
     * @param integer $sourceId データソースID
     * @return string ステーション定義XML文字列
     */
    public function getStationListXml($dataTypeId, $sourceId)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();

        $hdfsFile = $hadoopCmdApi->getStationDataFilePath(
            $dataTypeId,
            $sourceId
        );
        if (!$hdfsFile) {
            return false;
        }

        // キャッシュから取得
        $cache   = $this->getHdfsCache($hdfsFile);
        $cacheId = 'dataTypeId_' . $dataTypeId
                 . '_sourceId_'  . preg_replace('/[^A-Za-z0-9_]/', '_', $sourceId)
                 . '_' . preg_replace('/[^A-Za-z0-9_]/', '_', basename($hdfsFile));
        $xml = $cache->load($cacheId);

        if ($xml) {
            return $xml;
        } else {
            /*
             *  HDFS上のファイル読み込みコマンドの引数をセット
             *  - 第1引数：HDFS格納パス
             */
            array_push($args, $hdfsFile);
            $hadoopCmdApi->setArgs($args);
            $hadoopCmdApi->setHdfsCatScript();

            $results = $hadoopCmdApi->openScript();
            if (is_array($results)) {
                $line = $results[0];
                if (!preg_match('/^FAILURE/', $line)) {
                    $xml = implode('', $results);
                    $cache->save($xml, $cacheId);
                    return $xml;
                }
            }
        }

        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * アップロードされたユーザデータがMetXML形式かどうかチェック。
     *
     * @param string $xmlFile XMLファイル
     * @return boolean 真：XMLSchemaによる妥当性チェックOK
     */
    public function validateUploadMetXml($xmlFile)
    {
        // XMLSchemaで妥当性チェック
        $xsdConfig = $this->getXmlSchemaConfig();
        $xsdFile   = $xsdConfig['metxml'];
        $verified  = App_Xml::validateXmlFile($xmlFile, $xsdFile);
        return $verified;
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFSファイル用キャッシュの取得
     *
     * @param string $hdfsFile 設定ファイルパス
     * @return Zend_Cache
     */
    public function getHdfsCache($hdfsFile)
    {
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $cachConfig   = $this->getCacheConfig();

        // キャッシュフロントエンド
        $frontendOptions = array(
            'automatic_serialization' => true,
            'lifetime'                => $cachConfig['lifetime'],
            'master_file'             => $hdfsFile,
        );
        $frontend = new App_Cache_Frontend_Hdfs();
        $frontend->setHadoopCmdApi($hadoopCmdApi);
        $frontend->setAlwaysUseCache($cachConfig['always_use_cache']);
        while (list($name, $value) = each($frontendOptions)) {
            $frontend->setOption($name, $value);
        }

        // キャッシュバックエンド
        $backendOptions = array(
            'cache_dir' => $cachConfig['store_path']
        );
        $backend  = new Zend_Cache_Backend_File($backendOptions);

        // キャッシュ生成
        $cache = Zend_Cache::factory($frontend, $backend);

        return $cache;
    }

    // ------------------------------------------------------------------ //

    /**
     * XML+XSLファイルアーカイブの生成
     *
     * @param string $xml XML文字列
     * @return Zipデータ
     */
    public function getResultArchives($requestId, $xml)
    {
        $zip = new ZipArchive();
        $config    = $this->getConfig();
        $tmpZipDir = $config['tmp_dir'];
        if (!is_dir($tmpZipDir)) {
            return false;
        }

        $zipFile = $tmpZipDir . '/' . App_Utils::generateUniqueId() . '.zip';
        try {
            if ($zip->open($zipFile, ZipArchive::CREATE)) {
                $xmlFile = $requestId . '.xml';
                $zip->addFromString($xmlFile, $xml);

                $xsltConfig   = $this->getXsltConfig();
                $htmlXslFile  = $xsltConfig['html'];
                $csvXslFile   = $xsltConfig['csv'];
                $chartXslFile = $xsltConfig['chart'];
                $zip->addFile($htmlXslFile,  basename($htmlXslFile));
                $zip->addFile($csvXslFile,   basename($csvXslFile));
                $zip->addFile($chartXslFile, basename($chartXslFile));
                $zip->close();

                if (file_exists($zipFile)) {
                    $result = file_get_contents($zipFile);
                    unlink($zipFile);
                    return $result;
                }
            }
        } catch (Exception $exception) {
            if (file_exists($zipFile)) {
                unlink($zipFile);
            }
            throw $exception;
        }
    }
}

