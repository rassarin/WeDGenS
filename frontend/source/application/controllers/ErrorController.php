<?php
/**
 * applicaiton/controllers/ErrorController.php
 *
 * error コントローラ。
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ErrorController.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_ErrorAction
*/

/**
 * ErrorController クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class ErrorController extends App_Controller_ErrorAction
{
    // ---------------------------------------------------------------------- //

    /**
     * 400 Bad Requestエラー時のヘッダータイトル
     */
    const HEADER_TITLE_BAD_REQUEST = '400 Bad Request';

    /**
     * 403 Forbiddenエラー時のヘッダータイトル
     */
    const HEADER_TITLE_FORBIDDEN   = '403 Forbidden';

    /**
     * 404 Not Foundエラー時のヘッダータイトル
     */
    const HEADER_TITLE_NOT_FOUND   = '404 Not Found';

    /**
     * その他エラー時のヘッダータイトル
     */
    const HEADER_TITLE_APP_ERROR   = '500 Application Error';

    /**
     * その他エラー時の見出し
     */
    const MSG_APP_ERROR = 'アプリケーションエラー';

    /**
     * 400 Bad Requestエラー時のビュー
     */
    const VIEW_400_BAD_REQUEST = 'error/400.phtml';

    /**
     * 403 Forbiddenエラー時のビュー
     */
    const VIEW_403_FORBIDDEN   = 'error/403.phtml';

    /**
     * 404 Not Foundエラー時のビュー
     */
    const VIEW_404_NOT_FOUND   = 'error/404.phtml';

    // ------------------------------------------------------------------ //

    /**
     * error アクション。
     *
     * @return void
     */
    public function errorAction()
    {
        /**
         * 本アクションではアクセス制限チェックは行わない。
         */

        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 未定義のコントローラ、アクションの場合
                $this->_notFoundView($errors);
                break;
            default:
                $exception = $errors->exception;
                if (!empty($exception)) {
                    $code = $errors->exception->getCode();
                    switch($code) {
                        case 400:
                            $this->_badRequestView($errors);
                            break;
                        case 403:
                            $this->_forbiddenView($errors);
                            break;
                        default:
                            $this->_serverErrorView($errors);
                            break;
                    }
                } else {
                    $this->_serverErrorView($errors);

                }
                break;
        }

        // 例外詳細の表示
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    // ------------------------------------------------------------------ //

    /**
     * エラー画面(400 Bad Requestビュー)を出力する。
     *
     * @return void
     */
    private function _badRequestView($errors)
    {
        $this->getResponse()->setHttpResponseCode(400);
        $this->_helper->viewRenderer->renderScript(self::VIEW_400_BAD_REQUEST);
        $this->view->headTitle(self::HEADER_TITLE_BAD_REQUEST);

        // エラーログ出力
        $this->warningLog($errors->exception);
    }

    // ------------------------------------------------------------------ //

    /**
     * エラー画面(403 Forbiddenビュー)を出力する。
     *
     * @return void
     */
    private function _forbiddenView($errors)
    {
        $this->getResponse()->setHttpResponseCode(403);
        $this->_helper->viewRenderer->renderScript(self::VIEW_403_FORBIDDEN);
        $this->view->headTitle(self::HEADER_TITLE_FORBIDDEN);

        // エラーログ出力
        $this->warningLog($errors->exception);
    }

    // ------------------------------------------------------------------ //

    /**
     * エラー画面(404 Not foundビュー)を出力する。
     *
     * @return void
     */
    private function _notFoundView($errors)
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->_helper->viewRenderer->renderScript(self::VIEW_404_NOT_FOUND);
        $this->view->headTitle(self::HEADER_TITLE_NOT_FOUND);

        // エラーログ出力
        $this->warningLog($errors->exception);
    }

    // ------------------------------------------------------------------ //

    /**
     * エラー画面(errorビュー)を出力する。
     *
     * @return void
     */
    private function _serverErrorView($errors)
    {
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->headTitle(self::HEADER_TITLE_APP_ERROR);
        $this->view->message   = self::MSG_APP_ERROR;

        // エラーログ出力
        $this->criticalLog($errors->exception);
    }
}

