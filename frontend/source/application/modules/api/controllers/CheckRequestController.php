<?php
/**
 * applicaiton/modules/api/controllers/CheckRequestController.php
 *
 * check-request コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: CheckRequestController.php 28 2014-03-03 08:53:59Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_CheckRequestController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_CheckRequestController extends App_Controller_ApiAction
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

            // リクエストの進捗確認
            $requestId = $input->getEscaped('request_id');
            $results   = $this->getApiService('request')->checkRequest($requestId);

            // XML/JSONへ変換
            $response = $this->formatResponse($results);
            $this->getLogHelper()->checkRequestSuccess($requestId);
        } catch (App_Exception_Validate $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (Exception $exception) {
            $response = $this->errorResponse('エラーが発生しました。');
            $this->getLogHelper()->checkRequestFailure($requestId, $exception);
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
        return $this->forward('index', 'check-request', 'api', $params);
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
        return $this->forward('index', 'check-request', 'api', $params);
    }
}

