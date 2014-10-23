<?php
/**
 * applicaiton/Bootstrap.php
 *
 * ブートストラップ。
 *
 * アプリケーション起動処理。
 *
 * @category    Default
 * @package     Application
 * @subpackage  Bootstrap
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Bootstrap.php 6 2014-02-21 04:39:40Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Application_Bootstrap_Bootstrap
*/

/**
 * Bootstrap クラス
 *
 * アプリケーション起動処理定義クラス
 *
 * @category    Default
 * @package     Application
 * @subpackage  Bootstrap
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    // ------------------------------------------------------------------ //

    /**
     * オートローダ初期設定
     *
     * @return Zend_Loader_Autoloader
     */
    protected function _initAutoload()
    {
        require_once('Zend/Loader/Autoloader.php');
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $autoLoader->setFallbackAutoloader(true)
                   ->suppressNotFoundWarnings(true);

        $moduleLoader = new Zend_Application_Module_Autoloader(
            array(
                'namespace' => '',
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
        // 共通ヘルパーのセット
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_Service
        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_Log
        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_Acl
        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_Constraint
        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_Validator
        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new App_Controller_Action_Helper_ModuleService
        );

        // アクション単位のヘルパーのセット
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/controllers/helpers/Log",
            'Helper_Log'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/controllers/helpers/Acl",
            'Helper_Acl'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/controllers/helpers/Constraint",
            'Helper_Constraint'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . "/controllers/helpers/Validator",
            'Helper_Validator'
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * テーブルのメタデータのキャッシュ初期設定
     *
     * @return void
     */
    protected function _initDbMetadataCache()
    {
        // メタデータのキャッシュ設定
        $options      = $this->getOption('resources');
        $cacheOptions = $options['db']['meta_table_cache'];
        if ($cacheOptions['enable']) {
            $cacheDir = $cacheOptions['path'];
            $frontendOptions = array(
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir'  => $cacheDir,
            );
            $cache = Zend_Cache::factory(
                'Core',
                'File',
                $frontendOptions,
                $backendOptions
            );
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }
    }
}

