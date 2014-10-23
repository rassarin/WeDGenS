<?php
/**
 * applicaiton/controllers/helpers/Acl/UiAcl.php
 *
 * Defaultモジュール indexコントローラ アクセス制御ヘルパー
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: UiAcl.php 69 2014-03-12 12:39:48Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Acl
*/

/**
 * Helper_Acl_UiAcl クラス
 *
 * Defaultモジュール indexコントローラ アクセス制御ヘルパー定義クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Acl_UiAcl extends App_Controller_Action_Helper_Acl
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'UiAcl';
    }

    // ------------------------------------------------------------------ //

    /**
     * index アクション アクセス制御設定セット
     *
     * @param mixed $query アクセス権チェックに用いるデータ
     * @return boolean 真：許可
     */
    public function onIndex($query = null)
    {
        /**
         * アクセス制限なし。
         */
        return true;
    }
}
