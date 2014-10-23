<?php
/**
 * library/App/Test/PHPUnit/ServiceTestCase.php
 *
 * サービス単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ServiceTestCase.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Test_PHPUnit_DatabaseTestCase
*/

/**
 * App_Test_PHPUnit_ServiceTestCase クラス
 *
 * サービス単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Test_PHPUnit_ServiceTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    // ------------------------------------------------------------------ //

    /**
     * DB接続
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_connection;

    /**
     * サービスインスタンス
     * @var App_Service
     */
    private $_service = null;

    // ---------------------------------------------------------------------- //

    /**
     * 初期設定
     *
     * @return void
     */
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        parent::setUp();

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->setParam(
            'bootstrap',
            $this->bootstrap->getBootstrap()
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * 終了処理
     *
     * @return void
     */
    public function tearDown()
    {
        $defaultAdapter = Zend_Db_Table::getDefaultAdapter();
        $defaultAdapter->closeConnection();
        $this->bootstrap = null;
    }

    // ------------------------------------------------------------------ //

    /**
     * サービスインスタンスのセット
     *
     * @param App_Service $service サービスインスタンス
     * @return void
     */
    public function setService($service)
    {
        $this->_service = $service;
    }

    // ------------------------------------------------------------------ //

    /**
     * サービスインスタンスの取得
     *
     * @return App_Service サービスインスタンス
     */
    public function getService()
    {
        return $this->_service;
    }

    // ---------------------------------------------------------------------- //

    /**
     * データベースアダプタの取得
     *
     * @param string $dbName DB設定名
     * @return Zend_Db_Adapter データベースアダプタ
    */
    public function getDbAdapter($dbName = null)
    {
        $bootstrap = $this->bootstrap->getBootstrap();
        $resource  = $bootstrap->getPluginResource('multidb');
        if (!is_null($dbName)) {
            return $resource->getDb($dbName);
        }
        return $resource->getDefaultDb();
    }
}
