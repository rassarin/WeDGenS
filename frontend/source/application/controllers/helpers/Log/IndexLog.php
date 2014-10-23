<?php
/**
 * applicaiton/controllers/helpers/Log/IndexLog.php
 *
 * Defaultモジュール indexコントローラ ログ出力ヘルパー
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: IndexLog.php 15 2014-02-26 02:34:53Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Log
*/

/**
 * Helper_Log_IndexLog クラス
 *
 * Defaultモジュール indexコントローラ ログ出力ヘルパー定義クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Log_IndexLog extends App_Controller_Action_Helper_Log
{
    // ------------------------------------------------------------------ //

    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'IndexLog';
    }
}
