<?php
/**
 * applicaiton/controllers/helpers/Validator/UiValidator.php
 *
 * Defaultモジュール uiコントローラ バリデータヘルパー
 *
 * @category    Member
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: UiValidator.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Validator_UiValidator クラス
 *
 * Defaultモジュール uiコントローラ バリデータヘルパー定義クラス
 *
 * @category    Member
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Validator_UiValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'UiValidator';
    }
}
