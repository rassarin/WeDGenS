<?php
/**
 * library/App/Controller/Action/Helper/Validator.php
 *
 * バリデータヘルパー。
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Validator.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action_Helper_Abstract
*/

/**
 * App_Controller_Action_Helper_Validator クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Controller_Action_Helper_Validator extends Zend_Controller_Action_Helper_Abstract
{
    // ------------------------------------------------------------------ //

    /**
     * ヘルパーデフォルトメソッド
     *
     * @return App_Controller_Action_Helper_Validator バリデータヘルパー
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
        return 'Validator';
    }

    // ------------------------------------------------------------------ //

    /**
     * バリデータのセット
     *
     * helpers/Validator/コントローラ名Validator.php から
     * "onアクション名"のメソッドを呼び出す。
     *
     * @param mixed $query ユーザ入力値
     * @return $validator Zend_Filter_Inputインスタンス
    */
    public function getFilterInput($query = null)
    {
        $checked = true;
        $action  = App_Utils::dashToCamelCase(
            $this->getRequest()->getActionName()
        );
        $validatorMethod =  'setValidatorOn' . $action;
        if (method_exists($this, $validatorMethod)) {
            $validator = call_user_func_array(
                array($this, $validatorMethod),
                array($query)
            );
        }
        return $validator;
    }
}
