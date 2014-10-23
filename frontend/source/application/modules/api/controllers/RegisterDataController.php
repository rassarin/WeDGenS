<?php
/**
 * applicaiton/modules/api/controllers/RegisterDataController.php
 *
 * register-data コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: RegisterDataController.php 65 2014-03-12 12:32:31Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_RegisterDataController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_RegisterDataController extends App_Controller_ApiAction
{
    // ---------------------------------------------------------------------- //

    /**
     * コントローラの初期化。
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    // ---------------------------------------------------------------------- //

    /**
     * index アクション。
     *
     * @return void
     */
    public function indexAction()
    {
        $response = '';

        try {
            // 入力チェック
            $input = $this->getFilterInput();
            if (!$input->isValid()) {
                $this->getLogHelper()->validateMessage($input);
                throw new App_Exception_Validate(
                    'バリーデートエラーが発生しました。',
                    App_Exception::APP_VALIDATE_ERROR
                );
            }

            // レスポンス形式のセット
            $format = $input->getEscaped('response_format');
            $this->setResponseFormat($format);

            // アップロードファイルの取得
            $fileTransfer = $this->isValidUploadFiles('xml_file');
            if ($fileTransfer === false) {
                throw new App_Exception_File(
                    'アップロードエラーが発生しました。',
                    App_Exception::APP_VALIDATE_ERROR
                );
            }

            // 制約チェック
            if ($this->checkViolation($fileTransfer)) {
                throw new App_Exception_Constraint(
                    '制約エラーが発生しました。',
                    App_Exception::APP_VIOLATION_ERROR
                );
            }

            // 一時ファイルパスの取得
            $fileId  = $this->storeXmlFile('xml_file', $fileTransfer);
            $tmpFile = $fileTransfer->getTmpFilePath($fileId);
            if (!$tmpFile) {
                throw new App_Exception(
                    'システムエラーが発生しました。',
                    App_Exception::APP_SYSTEM_ERROR
                );
            }

            // ユーザデータの登録
            $results = $this->getApiService('request')
                            ->registerUserData($input, $tmpFile);

            // XML/JSONへ変換
            $response = $this->formatResponse($results);
            $this->getLogHelper()->registerDataSuccess();
        } catch (App_Exception_File $exception) {
            $response = $this->errorResponse('不正なファイルです。');
        } catch (App_Exception_Validate $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (App_Exception_Constraint $exception) {
            $response = $this->errorResponse('ファイルアップロードが失敗しました。');
        } catch (App_Exception_Xml $exception) {
            $response = $this->errorResponse('MetXML形式のXMLファイルではありません。');
            $this->getLogHelper()->registerDataFailure($exception);
        } catch (Exception $exception) {
            $response = $this->errorResponse('エラーが発生しました。');
            $this->getLogHelper()->registerDataFailure($exception);
        }
        return $this->sendResponse($response);
    }

    // ---------------------------------------------------------------------- //

    /**
     * xml アクション。
     *
     * @return void
     */
    public function xmlAction()
    {
        $params = $this->getRequest()->getParams();
        $params['response_format'] = App_Const::RESPONSE_FORMAT_XML;
        return $this->forward('index', 'register-data', 'api', $params);
    }

    // ---------------------------------------------------------------------- //

    /**
     * json アクション。
     *
     * @return void
     */
    public function jsonAction()
    {
        $params = $this->getRequest()->getParams();
        $params['response_format'] = App_Const::RESPONSE_FORMAT_JSON;
        return $this->forward('index', 'register-data', 'api', $params);
    }
}

