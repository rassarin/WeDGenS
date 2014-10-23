<?php
/**
 * applicaiton/modules/api/controllers/helpers/Acl/ApiGetRegionListAcl.php
 *
 * Apiモジュール get-region-listコントローラ アクセス制御ヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiGetRegionListAcl.php 22 2014-02-27 10:36:38Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Acl
*/

/**
 * Helper_Api_Acl_ApiGetRegionListAcl クラス
 *
 * Apiモジュール get-region-listコントローラ アクセス制御ヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Acl_ApiGetRegionListAcl extends App_Controller_Action_Helper_Acl
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
     */
    public function getName()
    {
        return 'ApiGetRegionListAcl';
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
