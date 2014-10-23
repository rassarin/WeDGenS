<?php
/**
 * applicaiton/modules/api/controllers/GetRegionListController.php
 *
 * get-region-list コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: GetRegionListController.php 28 2014-03-03 08:53:59Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_GetRegionListController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_GetRegionListController extends App_Controller_ApiAction
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

            // リージョンリストの取得
            $results = $this->getApiService('request')
                            ->getRegions($input);

            // XML/JSONへ変換
            $response = $this->formatResponse($results);
            $this->getLogHelper()->getRegionListSuccess();
        } catch (App_Exception_Validate $exception) {
            $response = $this->errorResponse('不正なパラメータです。');
        } catch (Exception $exception) {
            $response = $this->errorResponse('エラーが発生しました。');
            $this->getLogHelper()->getRegionListFailure($exception);
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
        return $this->forward('index', 'get-region-list', 'api', $params);
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
        return $this->forward('index', 'get-region-list', 'api', $params);
    }
}

