<?php
/**
 * applicaiton/modules/api/Bootstrap.php
 *
 * apiモジュールブートストラップ。
 *
 * apiモジュール起動処理定義クラス
 *
 * @category    Api
 * @package     Application
 * @subpackage  Bootstrap
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Bootstrap.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Application_Module_Bootstrap
*/

/**
 * Api_Bootstrap クラス
 *
 * apiモジュール起動処理定義クラス
 *
 * @category    Api
 * @package     Application
 * @subpackage  Bootstrap
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Bootstrap extends Zend_Application_Module_Bootstrap
{
    // ------------------------------------------------------------------ //

    /**
     * オートローダ初期設定
     *
     * @return void Zend_Loader_Autoloader
     */
    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => 'Api',
                'basePath'  => dirname(__FILE__)
            )
        );
        return $moduleLoader;
    }

    // ------------------------------------------------------------------ //

    /**
     * アクションヘルパー初期設定
     *
     * @return void
     */
    protected function _initActionHelper()
    {
        // アクション単位のヘルパーのセット
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/modules/api/controllers/helpers/Acl",
            'Helper_Api_Acl'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/modules/api/controllers/helpers/Constraint",
            'Helper_Api_Constraint'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/modules/api/controllers/helpers/Log",
            'Helper_Api_Log'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/modules/api/controllers/helpers/Validator",
            'Helper_Api_Validator'
        );
    }
}

