<?php
/**
 * library/App/Controller/Action/Helper/Acl.php
 *
 * ACLヘルパー。
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Acl.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action_Helper_Abstract
*/

/**
 * App_Controller_Action_Helper_Acl クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    // ------------------------------------------------------------------ //

    /**
     * 認証サービスインスタンス
     *
     * @var Service_Auth
     */
    protected $_auth = null;

    /**
     * 実行許可チェックエラーメッセージ
     *
     * @var string
     */
    protected $_messages = null;

    // ------------------------------------------------------------------ //

    /**
     * @see Zend_Controller_Action_Helper_Abstract::getName()
     */
    public function getName()
    {
        return 'Acl';
    }

    // ------------------------------------------------------------------ //

    /**
     * 権限のチェック
     *
     * @see Zend_Controller_Action_Helper_Abstract::direct()
     * @return App_Controller_Action_Helper_Acl
     */
    public function direct()
    {
        return $this;
    }

    // ------------------------------------------------------------------ //

    /**
     * 実行許可チェックエラーメッセージの取得
     *
     * @return string 実行許可チェックエラーメッセージ
    */
    public function getMessages()
    {
        return $this->_messages;
    }

    // ------------------------------------------------------------------ //

    /**
     * 実行許可チェックの実行
     *
     * helpers/Acl/コントローラ名Acl.php から
     * "onアクション名"のメソッドを呼び出す。
     *
     * @param array $data 妥当性チェック後のデータ
     * @return boolean|string 真：許可
    */
    public function checkPermission($data = null)
    {
        $checked = true;
        $action  = App_Utils::dashToCamelCase(
            $this->getRequest()->getActionName()
        );
        $aclMethod =  'on' . $action;
        if (method_exists($this, $aclMethod)) {
            $checked = call_user_func_array(
                array($this, $aclMethod),
                array($data)
            );
        }
        return $checked;
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証サービスの取得
     *
     * @return App_Session_Namespace_AppUser ユーザセッション
    */
    public function getAuth()
    {
        if (is_null($this->_auth)) {
            $this->_auth = App_Utils::getService('auth');
        }
        return $this->_auth;
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザセッションの取得
     *
     * @return App_Session_Namespace_AppUser ユーザセッション
    */
    public function getSession()
    {
        return $this->getAuth()->getSession();
    }

    // ------------------------------------------------------------------ //

    /**
     * 認証チェック
     *
     * @return boolean 真：認証済みである
     */
    public function isAuthenticated()
    {
        return $this->getAuth()->isAuthenticated();
    }

    // ------------------------------------------------------------------ //

    /**
     * ログインユーザIDの取得
     *
     * @return string ログインユーザID
     */
    public function getLoginUserId()
    {
        return $this->getAuth()->getLoginUserId();
    }

    // ------------------------------------------------------------------ //

    /**
     * CSRF対策用トークンの生成
     *
     * @return string CSRF対策用トークン
     */
    public function generateCsrfToken()
    {
        return $this->getSession()->generateCsrfToken();
    }
}
