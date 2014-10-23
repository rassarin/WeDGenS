<?php
/**
 * applicaiton/modules/api/controllers/AuthController.php
 *
 * auth コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: AuthController.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_AuthController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_AuthController extends App_Controller_ApiAction
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
        return $this->forwardForbidden();
    }

    // ---------------------------------------------------------------------- //

    /**
     * login アクション。
     *
     * @return void
     */
    public function loginAction()
    {
        /**
         * @todo 将来の拡張用。現在未使用。
         */
        $service = $this->getApiService('auth');
        if ($service->isAuthenticated()) {
            return $this->sendSuccessResponse();
        }

        try {
            // 入力チェック
            $input = $this->getFilterInput();
            if ($input->isValid()) {
                // 認証
                $auth = $service->authenticate(
                    $input->getUnescaped('user_id'),
                    $input->getUnescaped('password')
                );

                // 認証成功
                if ($auth->isValid()) {
                    if ($service->isAuthenticated()) {
                        $this->getLogHelper()->loginMessage(
                            $service->getLoginUserId()
                        );
                        return $this->sendSuccessResponse();
                    }
                }
            }
            $this->getLogHelper()->loginDeniedMessage(
                $input->getUnescaped('user_id')
            );
        } catch (Exception $exception) {
            $service->postAuthFailure();
            $this->getLogHelper()->exceptionMessage($exception);
        }

        return $this->sendFailureResponse();
    }

    // ------------------------------------------------------------------ //

    /**
     * logout アクション。
     *
     * @return void
    */
    public function logoutAction()
    {
        /**
         * @todo 将来の拡張用。現在未使用。
         */
        if (!$this->hasAccessPermission()) {
            return $this->forwardForbidden();
        }

        $service = $this->getApiService('auth');
        $userId  = $service->logout();

        $this->getLogHelper()->logoutMessage($userId);
        return $this->sendSuccessResponse();
    }
}

