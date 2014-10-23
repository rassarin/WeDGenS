<?php
/**
 * applicaiton/controllers/helpers/Acl/IndexAcl.php
 *
 * Defaultモジュール indexコントローラ アクセス制御ヘルパー
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: IndexAcl.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Acl
*/

/**
 * Helper_Acl_IndexAcl クラス
 *
 * Defaultモジュール indexコントローラ アクセス制御ヘルパー定義クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Acl_IndexAcl extends App_Controller_Action_Helper_Acl
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'IndexAcl';
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
