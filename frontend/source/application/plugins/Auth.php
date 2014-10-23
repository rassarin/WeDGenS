<?php
/**
 * application/plugins/Auth.php
 *
 * Authプラグイン。
 *
 * 認証処理プラグイン。
 * アクションのディスパッチ前に認証処理をプラグインとして組み込む
 * ことにより、認証処理を一元化する。
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Plugin
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Auth.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Plugin
*/

/**
 * Plugin_Auth クラス
 *
 * 認証処理プラグイン定義クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Plugin
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Plugin_Auth extends App_Controller_Plugin
{
    // ------------------------------------------------------------------ //

    /**
     * アクセス制御処理の実行
     *
     * ディスパッチ直前イベントハンドラにアクセス制御処理を登録し、
     * 各アクションコントローラの実行前の段階でアクセス制御処理を
     * 実行する。
     *
     * @param  object $request Zend_Controller_Request_Abstract
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // 指定のHTTPメソッド以外でのアクセスは拒否
        if (!$this->_checkMethod($request)) {
            $this->_forwardForbiddden($request);
        }

        // 未認証で実行可能なアクションはスキップ
        if ($this->_isIgnore($request)) {
            return;
        }

        // セッションにログイン済と記録されているかチェック
        $service = $this->getService('auth');
        if ($service->isAuthenticated()) {
            return;
        } else {
            $this->_forwardForbiddden($request);
        }
        return;
    }

    // ------------------------------------------------------------------ //

    /**
     * 許可したHTTPメソッドかどうかチェック
     *
     * @param  object $request Zend_Controller_Request_Abstract
     * @return boolean 真：許可したHTTPメソッドである
     */
    private function _checkMethod(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isPost()) {
            return true;
        }
        if ($request->isGet()) {
            return true;
        }
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証を必要としないアクションかどうかをチェック
     *
     * @param  object $request Zend_Controller_Request_Abstract
     * @return boolean 真：認証を必要としない
     */
    private function _isIgnore(Zend_Controller_Request_Abstract $request)
    {
        $ignored    = false;
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        if (App_Utils::isEmpty($action)) {
            $action = 'index';
        }

        // アクセス拒否画面
        if (($module == 'default') && ($controller == 'auth')) {
            switch ($action) {
                case 'forbidden':
                case 'login':
                    $ignored = true;
                    break;
                default:
                    $ignored = false;
                    break;
            }
            return $ignored;
        }
        return $ignored;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証前のアクションの保持が必要かどうかをチェック
     *
     * @param  object $request Zend_Controller_Request_Abstract
     * @return boolean 真：認証を必要としない
     */
    private function _isStore(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_isIgnore($request)) {
            return false;
        }
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        if (($module == 'default') && ($controller == 'auth')) {
            return false;
        }
        return true;
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセス拒否画面へフォワード
     *
     * @param  object $request Zend_Controller_Request_Abstract
     * @return void
     */
    private function _forwardForbiddden(Zend_Controller_Request_Abstract $request)
    {
        $request->setModuleName('default');
        $request->setControllerName('auth');
        $request->setActionName('forbidden');
        return;
    }
}
