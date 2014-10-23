<?php
/**
 * applicaiton/controllers/IndexController.php
 *
 * index コントローラ。
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: IndexController.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * IndexController クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class IndexController extends App_Controller_DefaultAction
{
    // ---------------------------------------------------------------------- //

    /**
     * コントローラの初期化。
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    // ---------------------------------------------------------------------- //

    /**
     * index アクション。
     *
     * @return void
     */
    public function indexAction()
    {
        return $this->_forward('index', 'ui');
    }

    // ---------------------------------------------------------------------- //

    /**
     * forbidden アクション。
     *
     * @return void
     */
    public function forbiddenAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        $this->view->headTitle('403 Forbidden');
        return;
    }
}

