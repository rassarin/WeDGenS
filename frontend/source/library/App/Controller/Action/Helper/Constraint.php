<?php
/**
 * library/App/Controller/Action/Helper/Constraint.php
 *
 * データ制約チェックヘルパー。
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Constraint.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action_Helper_Abstract
*/

/**
 * App_Controller_Action_Helper_Constraint クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Controller_Action_Helper_Constraint extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 制約チェックエラーメッセージ
     *
     * @var string
     */
    protected $_messages = null;

    /**
     * 制約チェック違反有無フラグ
     *
     * @var boolean
     */
    protected $_violation = null;

    // ------------------------------------------------------------------ //

    /**
     * ヘルパーデフォルトメソッド
     *
     * @return App_Controller_Action_Helper_Constraint 制約チェックヘルパー
    */
    public function direct()
    {
        return $this;
    }

    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'Constraint';
    }

    // ------------------------------------------------------------------ //

    /**
     * 制約チェックエラーメッセージの取得
     *
     * @return string 制約チェックエラーメッセージ
    */
    public function getMessages()
    {
        return $this->_messages;
    }

    // ------------------------------------------------------------------ //

    /**
     * 制約チェックの実行
     *
     * helpers/Constraint/コントローラ名Constraint.php から
     * "onアクション名"のメソッドを呼び出す。
     *
     * @param array $data 妥当性チェック後のデータ
     * @return boolean 真：違反した
     */
    public function checkViolation($data)
    {
        $checked = false;
        $action  = App_Utils::dashToCamelCase(
            $this->getRequest()->getActionName()
        );
        $constraintMethod =  'on' . $action;
        if (method_exists($this, $constraintMethod)) {
            $checked = call_user_func_array(
                array($this, $constraintMethod),
                array($data)
            );
        }
        $this->_violation = $checked;
        return $checked;
    }

    // ------------------------------------------------------------------ //

    /**
     * 制約チェックに違反したかどうか
     *
     * @return boolean 真：違反あり
    */
    public function hasViolation()
    {
        return $this->_violation;
    }

    // ------------------------------------------------------------------ //

    /**
     * サービスの取得
     *
     * @see Zend_Controller_Action_Helper_Abstract::direct()
     * @param string $serviceName サービス名
     * @return object
     */
    public function getService($serviceName, $moduleName = 'default')
    {
        $service = null;
        switch($moduleName) {
            case 'default':
                $service = App_Utils::getService($serviceName);
                break;
            default:
                $service = App_Utils::getModuleService($serviceName, $moduleName);
                break;
        }
        return $service;
    }
}
