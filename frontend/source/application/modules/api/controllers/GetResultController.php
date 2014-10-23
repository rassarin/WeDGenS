<?php
/**
 * applicaiton/modules/api/controllers/GetResultController.php
 *
 * get-result コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetResultController.php 83 2014-03-18 12:49:18Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_GetResultController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_GetResultController extends App_Controller_ApiAction
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
        $response  = '';
        $requestId = '';

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

            // リクエスト実行結果の取得
            $requestId = $input->getEscaped('request_id');
            $result    = $this->getApiService('request')->getResult($input);

            $checkOnlyFlag = $input->getEscaped('check_only');
            if ($checkOnlyFlag) {
                return $this->sendSuccessResponse();
            }
            // XML/JSONへ変換
            $this->getLogHelper()->getResultSuccess($requestId);
            return $this->_downloadResult($input, $result);
        } catch (App_Exception_Constraint $exception) {
            $response = $this->errorResponse($exception->getMessage());
        } catch (App_Exception_Validate $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (Exception $exception) {
            $response = $this->errorResponse('エラーが発生しました。');
            $this->getLogHelper()->getResultFailure($requestId, $exception);
        }
        return $this->forward404();
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
        return $this->forward('index', 'get-result', 'api', $params);
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
        return $this->forward('index', 'get-result', 'api', $params);
    }

    // ---------------------------------------------------------------------- //

    /**
     * リクエスト実行結果のダウンロード。
     *
     * @param Zend_Filter_Input $input ユーザ入力
     * @param string $result リクエスト実行結果
     * @return void
     */
    private function _downloadResult($input, $result)
    {
        $contentType = null;
        $fileName    = $input->getEscaped('request_id');
        $format      = $input->getEscaped('format');
        switch($format) {
            case App_Const::RESULT_FORMAT_HTML:
            case App_Const::RESULT_FORMAT_CHART:
                $contentType  = App_Const::CONTENT_TYPE_HTML;
                $fileName    .='.html';
                break;
            case App_Const::RESULT_FORMAT_CSV:
                $contentType  = App_Const::CONTENT_TYPE_CSV;
                $fileName    .='.csv';
                break;
            case App_Const::RESULT_FORMAT_ZIP:
                $contentType  = App_Const::CONTENT_TYPE_ZIP;
                $fileName    .='.zip';
                break;
            case App_Const::RESULT_FORMAT_XML:
                $contentType = App_Const::CONTENT_TYPE_XML;
                $fileName    .='.xml';
                break;
        }
        return $this->sendDownloadHttpHeader(
            $result,
            $fileName,
            $contentType
        );
    }
}

