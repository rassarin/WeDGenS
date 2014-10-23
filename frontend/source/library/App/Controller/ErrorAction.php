<?php
/**
 * library/App/Controller/ErrorAction.php
 *
 * エラー処理基底アクションコントローラ。
 *
 * エラー処理各アクションコントローラの共通処理を実装する基底クラス。
 * エラー処理各アクションコントローラは本クラスを継承する。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ErrorAction.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action
*/

/**
 * App_Controller_ErrorAction クラス
 *
 * エラー処理基底アクションコントローラ定義クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @subpackage  Controller
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Controller_ErrorAction extends App_Controller_Action
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

        $this->initViewSetting();
        $this->view->headScript()->setFile(null);
        $this->_helper->layout()->setLayout('error');
    }
}
