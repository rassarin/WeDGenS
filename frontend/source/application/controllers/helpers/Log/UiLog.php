<?php
/**
 * applicaiton/controllers/helpers/Log/UiLog.php
 *
 * Defaultモジュール uiコントローラ ログ出力ヘルパー
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: UiLog.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Log_UiLog クラス
 *
 * Defaultモジュール uiコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Log_UiLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'UiLog';
    }
}
