<?php
/**
 * applicaiton/controllers/UiController.php
 *
 * ui コントローラ。
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: UiController.php 69 2014-03-12 12:39:48Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * UiController クラス
 *
 * @category    Default
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class UiController extends App_Controller_DefaultAction
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
        $this->view->pageTitle = '気象データ生成サービス';
        return;
    }
}

