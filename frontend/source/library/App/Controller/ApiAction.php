<?php
/**
 * library/App/Controller/ApiAction.php
 *
 * WebAPI基底アクションコントローラ。
 *
 * WebAPIの各アクションコントローラの共通処理を実装する基底クラス。
 * WebAPIの各アクションコントローラは本クラスを継承する。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiAction.php 82 2014-03-18 07:01:22Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action
*/

/**
 * App_Controller_ApiAction クラス
 *
 * WebAPI基底アクションコントローラ定義クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Controller_ApiAction extends App_Controller_Action
{
    // ------------------------------------------------------------------ //

    /**
     * レスポンス形式
     *
     * @var string
     */
    protected $_responseFormat = App_Const::RESPONSE_FORMAT_JSON;


    // ---------------------------------------------------------------------- //

    /**
     * コントローラの初期化。
     *
     * @return void
    */
    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();

        // HTTPメソッドチェック
        if (!$this->getRequest()->isPost() &&
            !$this->getRequest()->isGet()  &&
            !$this->getRequest()->isXmlHttpRequest()) {
            return $this->forward404();
        }

        // アクセス権チェック
        if (!$this->hasAccessPermission()) {
            return $this->forwardForbidden();
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * apiモジュールサービスの取得
     *
     * @param  string $name サービス名
     * @return App_Service apiモジュールサービス
    */
    public function getApiService($name)
    {
        $config  = $this->getApiConfig($name);
        $service = $this->getModuleService($name);
        $service->setConfig($config);

        // WGSジェネレータWebAPI ラッパークラスのセット
        $wgsGeneratorApi = $this->createWgsGeneratorApi();
        $service->setWgsGeneratorWebApi($wgsGeneratorApi);

        // hadoopコマンドAPI ラッパークラスのセット
        $hadoopCmdApi = $this->createHadoopCmdApi();
        $service->setHadoopCmdApi($hadoopCmdApi);

        return $service;
    }

    // ------------------------------------------------------------------ //

    /**
     * apiモジュールサービス出力設定の取得
     *
     * @param  string $name サービス名
     * @return array apiモジュールサービス設定
     */
    public function getApiConfig($name)
    {
        $configPath = $this->getConfig('config_path');
        $config     = $this->loadPartialConfig(
            $configPath['webapi'],
            $name
        );
        return $config;
    }

    // ------------------------------------------------------------------ //

    /**
     * hadoopコマンドAPI ラッパーの生成
     *
     * @return App_Wrapper_Script_Hadoop hadoopコマンドラッパー
     */
    public function createHadoopCmdApi()
    {
        $configPath   = $this->getConfig('config_path');
        $hadoopCmdApi = new App_Wrapper_Script_Hadoop(
            $configPath['webapi'],
            'common'
        );
        return $hadoopCmdApi;
    }

    // ------------------------------------------------------------------ //

    /**
     * WGSジェネレータWebAPI ラッパーの生成
     *
     * @return App_Wrapper_Web_WgsGenerator WGSジェネレータWebAPIラッパー
     */
    public function createWgsGeneratorApi()
    {
        $configPath      = $this->getConfig('config_path');
        $generatorWebApi = new App_Wrapper_Web_WgsGenerator(
            $configPath['webapi'],
            'common'
        );
        return $generatorWebApi;
    }

    // ------------------------------------------------------------------ //

    /**
     * レスポンス形式の取得
     *
     * @return string レスポンス形式
    */
    public function getResponseFormat()
    {
        return $this->_responseFormat;
    }

    // ------------------------------------------------------------------ //

    /**
     * レスポンス形式の取得
     *
     * @param  string $value レスポンス形式
     * @return void
    */
    public function setResponseFormat($value)
    {
        $this->_responseFormat = $value;
    }

    // ---------------------------------------------------------------------- //

    /**
     * 成功レスポンスXMLの生成。
     *
     * @return void
     */
    public function successResponse()
    {
        return $this->_getResponseService()->successResponse();
    }

    // ---------------------------------------------------------------------- //

    /**
     * 失敗レスポンスXMLの生成。
     *
     * @return void
     */
    public function failureResponse()
    {
        return $this->_getResponseService()->failureResponse();
    }

    // ---------------------------------------------------------------------- //

    /**
     * エラーレスポンスの生成。
     *
     * @return string エラーレスポンス
     */
    public function errorResponse($message)
    {
        return $this->_getResponseService()->errorResponse($message);
    }

    // ---------------------------------------------------------------------- //

    /**
     * 結果レスポンスの生成。
     *
     * @param mixed $results 結果
     * @return string 結果レスポンス
     */
    public function formatResponse($results)
    {
        $controller = $this->getRequest()->getControllerName();
        return $this->_getResponseService()->formatResponse($results, $controller);
    }

    // ---------------------------------------------------------------------- //

    /**
     * 成功レスポンスXMLの送信。
     *
     * @return void
     */
    public function sendResponse($response)
    {
        $format  = $this->getResponseFormat();
        switch($format) {
            case App_Const::RESPONSE_FORMAT_XML:
                return $this->sendXml($response);
                break;
            case App_Const::RESPONSE_FORMAT_JSON:
            default:
                return $this->sendJson($response);
                break;
        }
        return null;
    }

    // ---------------------------------------------------------------------- //

    /**
     * 成功レスポンスの送信。
     *
     * @return void
     */
    public function sendSuccessResponse()
    {
        $response = $this->successResponse();
        return $this->sendResponse($response);
    }

    // ---------------------------------------------------------------------- //

    /**
     * 失敗レスポンスの送信。
     *
     * @return void
     */
    public function sendFailureResponse()
    {
        $response = $this->failureResponse();
        return $this->sendResponse($response);
    }

    // ---------------------------------------------------------------------- //

    /**
     * アップロードファイル操作クラスの取得
     *
     * @return Zend_File_Transfer_Adapter_Http アップロードファイル操作クラス
    */
    public function getFileTransfer()
    {
        $config = $this->getApiConfig('common');
        return new App_FileTransfer($config);
    }

    // ---------------------------------------------------------------------- //

    /**
     * アップロードファイル情報の取得。
     *
     * @return array $uploadParams アップロードファイルパラメータ
     * @return array アップロードファイル情報
    */
    public function recieveUploadFiles($uploadParams)
    {
        $uploadFiles  = array();
        $fileTransfer = $this->getFileTransfer();
        $fileTransfer->setOptions(
            array(
                'useByteString' => false,
                'ignoreNoFile'  => true,
            )
        );

        foreach ($uploadParams as $paramName => $type) {
            if (!$fileTransfer->checkUploadFile($paramName, $type)) {
                continue;
            }

            if ($fileTransfer->isUploaded($paramName)) {
                // 各情報を格納
                $orgFileNames = $fileTransfer->getFileName($paramName);
                $orgFileSizes = $fileTransfer->getFileSize($paramName);
                $orgFileHashs = $fileTransfer->getHash('md5', $paramName);

                if (is_array($orgFileNames)) {
                    foreach ($orgFileNames as $key => $val) {
                        $temp = $orgFileNames[$key];
                        $orgFileNames[$key] = basename($temp);
                    }
                } else {
                    $temp = $orgFileNames;
                    $orgFileNames = basename($temp);
                }

                // 一時領域にファイルを格納
                $fileIds = $fileTransfer->storeToTemporary($paramName, $type);
                if ($fileIds === false) {
                    continue;
                }
                $uploadFiles[$paramName] = array(
                    'file_id'   => $fileIds,
                    'file_name' => $orgFileNames,
                    'file_size' => $orgFileSizes,
                    'file_hash' => $orgFileHashs,
                );
            }
        }
        $fileTransfer->setUploadFiles($uploadFiles);
        return $fileTransfer;
    }

    // ---------------------------------------------------------------------- //

    /**
     * アップロードファイルのバリデートと一時保管。
     *
     * @param string $name パラメータ名
     * @return boolean 真：ファイル正常アップロード
    */
    public function isValidUploadFiles($name)
    {
        // アップロードファイルの取得
        $uploadParams = array(
            $name => 'xml',
        );

        $fileTransfer = $this->recieveUploadFiles($uploadParams);
        if ($fileTransfer->hasErrors()) {
            $this->getLogHelper()->uploadFailure($fileTransfer);
            return false;
        }
        return $fileTransfer;
    }

    // ---------------------------------------------------------------------- //

    /**
     * XMLファイルの保存
     *
     * @param string $name パラメータ名
     * @param App_FileTransfer $fileTransfer アップロードファイル
     * @return string アップロードファイルのファイルID
    */
    public function storeXmlFile($name, $fileTransfer)
    {
        $tmpFile = $fileTransfer->getTmpFile($name);
        if (!$tmpFile) {
            return false;
        }
        if (is_readable($tmpFile) && filesize($tmpFile)) {
            return basename($tmpFile);
        }
        return false;
    }

    // ---------------------------------------------------------------------- //

    /**
     * レスポンス用サービスの取得。
     *
     * @return App_Service レスポンス用サービスクラス
     */
    private function _getResponseService()
    {
        $service = null;
        $format  = $this->getResponseFormat();
        switch($format) {
            case App_Const::RESPONSE_FORMAT_XML:
                $service = $this->getApiService('xml');
                break;
            case App_Const::RESPONSE_FORMAT_JSON:
            default:
                $service = $this->getApiService('json');
                break;
        }
        return $service;
    }
}
