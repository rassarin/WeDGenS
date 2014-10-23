<?php
/**
 * library/App/Test/PHPUnit/ControllerTestCase.php
 *
 * コントローラ単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ControllerTestCase.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Test_PHPUnit_ControllerTestCase
*/

/**
 * App_Test_PHPUnit_ControllerTestCase クラス
 *
 * コントローラ単体テスト抽象クラス。
 *
 * @category    App
 * @package     Test
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
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
    }

    // ------------------------------------------------------------------ //

    /**
     * 終了処理
     *
     * @return void
     */
    public function tearDown()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $adapter->closeConnection();
        $this->bootstrap = null;
    }
}
