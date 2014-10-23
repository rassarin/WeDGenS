<?php
/**
 * applicaiton/modules/api/controllers/IndexController.php
 *
 * index コントローラ。
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: IndexController.php 8 2014-02-25 06:27:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_DefaultAction
*/

/**
 * Api_IndexController クラス
 *
 * @category    Api
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_IndexController extends App_Controller_ApiAction
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
        return $this->forwardForbidden();
    }
}

