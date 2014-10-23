<?php
/**
 * applicaiton/modules/api/services/Auth.php
 *
 * 認証サービスクラス
 *
 * 認証処理。
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Auth.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Service_Api
*/

/**
 * Api_Service_Auth クラス
 *
 * 認証サービスクラス
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Service_Auth extends Service_Api
{
    // ------------------------------------------------------------------ //
    /**
     * セッションタイムアウト[s]
     */
    const SESSION_TIMEOUT = 3600;

    // ------------------------------------------------------------------ //

    /**
     * 認証アダプタ
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter = null;

    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // 初期化処理
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証アダプタの初期化
     *
     * @return void
     */
    private function _setAuthAdapter()
    {
        $dbAdapter   = $this->getAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            $dbAdapter,
            't_user',
            'user_id',
            'password',
            'MD5(?)'
        );
        $this->_authAdapter = $authAdapter;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証アダプタの取得
     *
     * @return Zend_Auth_Adapter_DbTable 認証アダプタ
     */
    public function getAuthAdapter()
    {
        if (is_null($this->_authAdapter)) {
            $this->_setAuthAdapter();
        }
        return $this->_authAdapter;
    }

    // ------------------------------------------------------------------ //

    /**
     * Zend_Authインスタンスの取得
     *
     * @return object Zend_Auth
     */
    public function getAuthInstance()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(
            new Zend_Auth_Storage_Session(
                App_Session_Namespace_AppUser::getAppNamespace(),
                'userId'
            )
        );
        return $auth;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザ情報の取得
     *
     * @param string $userId ユーザID
     * @param string $password パスワード
     * @return Zend_Db_Table_Row_Abstract ユーザ情報
     */
    public function authenticate($userId, $password)
    {
        $authAdapter = $this->getAuthAdapter()
                            ->setIdentity($userId)
                            ->setCredential($password);

        $auth   = $this->getAuthInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            $this->_allowLogin($auth);
        }
        return $result;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証済みフラグの取得
     *
     * @return boolean 真：認証済み
     */
    public function isAuthenticated()
    {
        return $this->getSession()->isAuthenticated();
    }

    // ------------------------------------------------------------------ //

    /**
     * セッションIDの取得
     *
     * @return string セッションID
     */
    public function getCurrentSessionId()
    {
        return Zend_Session::getId();
    }

    // ------------------------------------------------------------------ //

    /**
     * 非ログインユーザ(ゲスト)チェックフラグの取得
     *
     * @return boolean 真：非ログインユーザ(ゲスト)である
     */
    public function isGuest()
    {
        return $this->getSession()->isGuest();
    }

    // ------------------------------------------------------------------ //

    /**
     * 非ログインユーザ(ゲスト)フラグをセット
     *
     * @return void
     */
    public function setGuest()
    {
        $this->getSession()->setGuest();
    }

    // ------------------------------------------------------------------ //

    /**
     * ログアウト処理
     *
     * @return string ログアウトしたユーザID
     */
    public function logout()
    {
        $userId = $this->getLoginUserId();

        // セッション変数のクリア
        $this->getAuthInstance()->clearIdentity();
        $this->getSession()->clear();

        // 有効期限切れのセッションクッキーを送信し、
        // クライアント側でセッションクッキーを削除させる
        App_Session::expireSessionCookie();

        return $userId;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザセッションの取得
     *
     * @param string $name セッション名
     * @return App_Session_Namespace ユーザセッション
     */
    public function getSession($name = 'user')
    {
        return App_Utils::getSession($name);
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザIDの取得
     *
     * @return string ユーザID
     */
    public function getLoginUserId()
    {
        $session = $this->getSession();
        if (!$session->isAuthenticated()) {
            return null;
        }

        $userId = $session->getUserId();
        if (preg_match('/^$/u', $userId)) {
            return null;
        }

        return $userId;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証前にリクエストされたアクションを保持。
     *
     * @param  string $module モジュール名
     * @param  string $controller コントローラ名
     * @param  string $action アクション名
     * @return void
     */
    public function setPreviousAction($module, $controller, $action)
    {
        $this->getSession()->setCurrentModule($module);
        $this->getSession()->setCurrentController($controller);
        $this->getSession()->setCurrentAction($action);
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証前にリクエストされたアクションを保持。
     *
     * @return array 認証前にリクエストされたアクション
     */
    public function getPreviousAction()
    {
        $previous = array();
        $previous['module']     = $this->getSession()->getCurrentModule();
        $previous['controller'] = $this->getSession()->getCurrentController();
        $previous['action']     = $this->getSession()->getCurrentAction();

        return $previous;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証前にリクエストされたアクションを保持を解除。
     *
     * @return void
     */
    public function clearPreviousAction()
    {
        $this->getSession()->setCurrentModule(null);
        $this->getSession()->setCurrentController(null);
        $this->getSession()->setCurrentAction(null);
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証失敗時の後処理。
     *
     * @return void
     */
    public function postAuthFailure()
    {
        $this->getSession()->clearAuthenticated();
        $this->getSession()->clearUserTicket();
        $this->getSession()->setUserId(null);
    }

    // ------------------------------------------------------------------ //

    /**
     * ログインの許可
     *
     * @param string $auth Zend_Auth
     */
    private function _allowLogin($auth)
    {
        $userId  = $auth->getIdentity();
        $session = $this->getSession();
        if (!$session->isAuthenticated()) {
            // セッションIDを再発行
            App_Session::regenerateId();
            $session->setAuthenticated();
            $session->setUserId($userId);
            $session->createUserTicket($userId);
            $session->setExpirationSeconds(self::SESSION_TIMEOUT);
        }
    }
}
