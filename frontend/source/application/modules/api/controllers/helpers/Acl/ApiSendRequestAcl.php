<?php
/**
 * applicaiton/modules/api/controllers/helpers/Acl/ApiSendRequestAcl.php
 *
 * Apiモジュール send-requestコントローラ アクセス制御ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiSendRequestAcl.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Acl
*/

/**
 * Helper_Api_Acl_ApiSendRequestAcl クラス
 *
 * Apiモジュール send-requestコントローラ アクセス制御ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Acl_ApiSendRequestAcl extends App_Controller_Action_Helper_Acl
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiSendRequestAcl';
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
