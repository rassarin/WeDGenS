<?php
/**
 * library/App/Test/PHPUnit/DbTableTestCase.php
 *
 * DBテーブルモデル単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: DbTableTestCase.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Test_PHPUnit_DatabaseTestCase
*/

/**
 * App_Test_PHPUnit_DbTableTestCase クラス
 *
 * DBテーブルモデル単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Test_PHPUnit_DbTableTestCase extends PHPUnit_Extensions_Database_TestCase
{
    // ---------------------------------------------------------------------- //

    const DB_SCHEMA = 'public';

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
        $this->bootstrap->bootstrap('multidb');

        parent::setUp();
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

    // ---------------------------------------------------------------------- //

    /**
     * ZFデータベースアダプタの取得
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

    // ---------------------------------------------------------------------- //

    /**
     * PHPUnit用DBアダプタの取得
     *
     * @param string $dbName DB設定名
     * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection PHPUnit用DBアダプタ
    */
    public function getConnection($dbName = null)
    {
        $db = $this->getDbAdapter($dbName);
        return $this->createDefaultDBConnection(
            $db->getConnection(),
            self::DB_SCHEMA
        );
    }

    // ---------------------------------------------------------------------- //

    /**
     * データセットの取得
     *
     * @return PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet データセットインスタンス
    */
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(
            APP_ROOT_PATH .'/tests/fixture/db_table-seed.xml'
        );
    }

    // ---------------------------------------------------------------------- //

    /**
     * データセットの初期化
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation データセットの初期化インスタンス
    */
    protected function getSetUpOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT(true);
    }
}
