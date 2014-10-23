<?php
/**
 * applicaiton/controllers/helpers/Validator/IndexValidator.php
 *
 * Defaultモジュール indexコントローラ バリデータヘルパー
 *
 * @category    Member
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: IndexValidator.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Validator
*/

/**
 * Helper_Validator_IndexValidator クラス
 *
 * Defaultモジュール indexコントローラ バリデータヘルパー定義クラス
 *
 * @category    Member
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Validator_IndexValidator extends App_Controller_Action_Helper_Validator
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'IndexValidator';
    }
}
