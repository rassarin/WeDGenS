<?php
/**
 * applicaiton/modules/api/controllers/helpers/Acl/ApiAuthAcl.php
 *
 * Apiモジュール authコントローラ アクセス制御ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiAuthAcl.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Acl
*/

/**
 * Helper_Api_Acl_ApiAuthAcl クラス
 *
 * Apiモジュール authコントローラ アクセス制御ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Acl_ApiAuthAcl extends App_Controller_Action_Helper_Acl
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiAuthAcl';
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
         * アクセス拒否。
         */
        $this->_messages = 'アクセス権限がありません。';
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * login アクション アクセス制御設定セット
     *
     * @param mixed $query アクセス権チェックに用いるデータ
     * @return boolean 真：許可
     */
    public function onLogin($query = null)
    {
        /**
         * 将来の機能拡張時までアクセス拒否。
         */
        $this->_messages = 'アクセス権限がありません。';
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * logout アクション アクセス制御設定セット
     *
     * @param mixed $query アクセス権チェックに用いるデータ
     * @return boolean 真：許可
     */
    public function onLogout($query = null)
    {
        /**
         * 将来の機能拡張時までアクセス拒否。
         */
        $this->_messages = 'アクセス権限がありません。';
        return false;
    }
}
