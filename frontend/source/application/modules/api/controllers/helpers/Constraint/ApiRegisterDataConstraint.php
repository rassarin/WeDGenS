<?php
/**
 * applicaiton/modules/api/controllers/helpers/Constraint/ApiRegisterDataConstraint.php
 *
 * Apiモジュール register-dataコントローラ バリデータヘルパー
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ApiRegisterDataConstraint.php 11 2014-02-25 11:49:12Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action_Helper_Constraint
*/

/**
 * Helper_Api_Constraint_ApiRegisterDataConstraint クラス
 *
 * Apiモジュール register-dataコントローラ バリデータヘルパー定義クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Helper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Helper_Api_Constraint_ApiRegisterDataConstraint extends App_Controller_Action_Helper_Constraint
{
    /**
     * ヘルパー名の取得
     *
     * @return string ヘルパー名
    */
    public function getName()
    {
        return 'ApiRegisterDataConstraint';
    }

    // ------------------------------------------------------------------ //

    /**
     * index アクション バリデータ設定
     *
     * @param mixed $query ユーザ入力値
     * @return Zend_Filter_Input バリデータ
    */
    // ------------------------------------------------------------------ //

    /**
     * indexアクション 制約チェック設定
     *
     * @param App_FileTransfer $fileTransfer 制約チェックに用いるデータ
     * @return boolean 真：違反あり
     */
    public function onIndex($fileTransfer)
    {
        /**
         * ファイルがアップロードされているかどうかチェック
         */
        $uploadFiles = $fileTransfer->getUploadFiles();
        if (count($uploadFiles) < 1) {
            $this->_messages = 'ファイルをアップロードしてください。';
            return true;
        }
        return false;
    }
}
