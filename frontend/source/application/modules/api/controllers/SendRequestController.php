<?php
/**
 * applicaiton/modules/api/controllers/SendRequestController.php
 *
 * send-request コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: SendRequestController.php 86 2014-03-19 09:03:41Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_SendRequestController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_SendRequestController extends App_Controller_ApiAction
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
            // データ指定入力チェック
            $dataInput = $this->getFilterInput();
            if (!$dataInput->isValid()) {
                $this->getLogHelper()->validateMessage($dataInput);
                throw new App_Exception_Validate(
                    'バリーデートエラーが発生しました。',
                    App_Exception::APP_VALIDATE_ERROR
                );
            }

            // レスポンス形式のセット
            $format = $dataInput->getEscaped('response_format');
            $this->setResponseFormat($format);

            // 制約チェック
            if ($this->checkViolation($dataInput)) {
                throw new App_Exception_Constraint(
                    '制約エラーが発生しました。',
                    App_Exception::APP_VIOLATION_ERROR
                );
            }

            // パラメータ指定入力チェック
            $libId      = $dataInput->getEscaped('lib_id');
            $paramDef   = $this->getApiService('request')->getLibraryParams($libId);
            $paramInput = $this->getValidatorHelper()
                               ->getLibraryParamFilterInput($paramDef);
            if (!$paramInput->isValid()) {
                $this->getLogHelper()->validateMessage($paramInput);
                throw new App_Exception_Validate(
                    'バリーデートエラーが発生しました。',
                    App_Exception::APP_VALIDATE_ERROR
                );
            }

            // リクエストの送信
            $results = $this->getApiService('request')
                            ->sendRequest($dataInput, $paramInput);
            if (array_key_exists('request_id', $results)) {
                $requestId = $results['request_id'];
            }

            // XML/JSONへ変換
            $response  = $this->formatResponse($results);
            $this->getLogHelper()->sendRequestSuccess($requestId);
        } catch (App_Exception_Validate $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (App_Exception_Constraint $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (App_Exception_Xml $exception) {
            $response = $this->errorResponse('該当するステーションがありません。');
        } catch (Exception $exception) {
            $response = $this->errorResponse('エラーが発生しました。');
            $this->getLogHelper()->sendRequestFailure($requestId, $exception);
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
        return $this->forward('index', 'send-request', 'api', $params);
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
        return $this->forward('index', 'send-request', 'api', $params);
    }
}

