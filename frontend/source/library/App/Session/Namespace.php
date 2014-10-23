<?php
/**
 * library/App/Session/Namespace.php
 *
 * セッション名前空間操作クラス
 *
 * @category    App
 * @package     Session
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Namespace.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Session_Namespace
*/

/**
 * App_Session_Namespace クラス
 *
 * セッション名前空間操作クラス
 *
 * @category    App
 * @package     Session
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Session_Namespace extends Zend_Session_Namespace
{
    // ------------------------------------------------------------------ //

    /**
     * セッションデフォルト名前空間
     */
    const NS_SYSTEM  = 'app';

    /**
     * CSRF対策用トークン生成文字列
     */
    const TOKEN_SEED = 'csrf_token';

    /**
     * CSRF対策用トークンの保持数
     */
    const TOKEN_MAX_SAVE_COUNTS = 10;

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param  string $namespace 名前空間
     * @param  boolean $singleInstance 真：名前空間へのアクセスを単一インスタンスに制限する
     * @return void
     */
    public function __construct(
        $namespace = self::NS_SYSTEM, $singleInstance = false
    ) {
        parent::__construct($namespace, $singleInstance);
    }

    // ------------------------------------------------------------------ //

    /**
     * セッション変数のクリア
     *
     * @return void
     */
    public function clear()
    {
        Zend_Session::namespaceUnset($this->getAppNamespace());
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証済みかどうかのチェック
     *
     * @return boolean 真：認証済
    */
    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証済みフラグを立てる
     *
     * @return void
    */
    public function setAuthenticated()
    {
        $this->authenticated = true;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証済みフラグを消去
     *
     * @return void
    */
    public function clearAuthenticated()
    {
        $this->authenticated = false;
    }

    // ------------------------------------------------------------------ //

    /**
     * 非ログインユーザかどうかのチェック
     *
     * @return boolean 真：非ログインユーザ
    */
    public function isGuest()
    {
        return $this->guest;
    }

    // ------------------------------------------------------------------ //

    /**
     * 非ログインユーザフラグを立てる
     *
     * @return void
    */
    public function setGuest()
    {
        $this->guest = true;
    }

    // ------------------------------------------------------------------ //

    /**
     * 非ログインユーザフラグを初期化
     *
     * @return void
    */
    public function clearGuest()
    {
        $this->guest = null;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザチケットのセット
     *
     * @param string $ticket ユーザチケット
     * @return void
     */
    public function setUserTicket($ticket)
    {
        $this->userTicket = $ticket;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザチケットの取得
     *
     * @return string 一意の識別子
     */
    public function getUserTicket()
    {
        return $this->userTicket;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザチケットのクリア
     *
     * @return void
     */
    public function clearUserTicket()
    {
        return $this->userTicket = null;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザチケットの生成
     *
     * @param  string $userId ログインID
     * @return string 一意の識別子
     */
    public function createUserTicket($userId)
    {
        $ticket = $userId . '_' . sha1(uniqid(mt_rand(), true));
        $this->setUserTicket($ticket);

        return $ticket;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントアクション名の取得
     *
     * @return string カレントアクション名
     */
    public function getCurrentAction()
    {
        return $this->currentAction;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントアクション名のセット
     *
     * @param  string $action カレントアクション名
     * @return void
     */
    public function setCurrentAction($action)
    {
        $this->currentAction = $action;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントコントローラ名の取得
     *
     * @return string カレントコントローラ名
     */
    public function getCurrentController()
    {
        return $this->currentController;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントコントローラ名のセット
     *
     * @param  string $controller カレントコントローラ名
     * @return void
     */
    public function setCurrentController($controller)
    {
        $this->currentController = $controller;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントモジュール名の取得
     *
     * @return string カレントモジュール名
     */
    public function getCurrentModule()
    {
        return $this->currentModule;
    }

    // ------------------------------------------------------------------ //

    /**
     * カレントモジュール名のセット
     *
     * @param  string $module カレントモジュール名
     * @return void
     */
    public function setCurrentModule($module)
    {
        $this->currentModule = $module;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策用トークン格納配列の取得
     *
     * @return array CSRF対策用トークン
     */
    public function getCsrfToken()
    {
        if (!isset($this->csrfTokens)) {
            $this->clearCsrfToken();
        }
        return $this->csrfTokens;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策用トークンの生成
     *
     * @return string CSRF対策用トークン
     */
    public function generateCsrfToken()
    {
        $tokens = $this->getCsrfToken();
        if (count($tokens) > self::TOKEN_MAX_SAVE_COUNTS) {
            array_shift($tokens);
        }

        $newToken = sha1(self::TOKEN_SEED . microtime() . mt_rand());
        array_push($tokens, $newToken);
        $this->csrfTokens = $tokens;
        return $newToken;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策用トークンのチェック
     *
     * @param string $token CSRF対策用トークン
     * @return boolean 真：OK
     */
    public function checkCsrfToken($token)
    {
        $tokens = $this->getCsrfToken();
        if (false !== ($pos = array_search($token, $tokens, true))) {
            unset($tokens[$pos]);
            $this->csrfTokens = $tokens;
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策用トークンのクリア
     *
     * @return void
     */
    public function clearCsrfToken()
    {
        $this->csrfTokens = array();
    }
}
