<?php
/**
 * library/App/Controller/DefaultAction.php
 *
 * デフォルトモジュール基底アクションコントローラ。
 *
 * デフォルトモジュール各アクションコントローラの共通処理を実装する基底クラス。
 * デフォルトモジュール各アクションコントローラは本クラスを継承する。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: DefaultAction.php 11 2014-02-25 11:49:12Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Controller_Action
*/

/**
 * App_Controller_DefaultAction クラス
 *
 * デフォルトモジュール基底アクションコントローラ定義クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  Action
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Controller_DefaultAction extends App_Controller_Action
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
        $this->_helper->layout()->setLayout('default');

        // HTTPメソッドチェック
        if (!$this->getRequest()->isPost() &&
            !$this->getRequest()->isGet()  &&
            !$this->getRequest()->isXmlHttpRequest()) {
            return $this->forward404();
        }

        // アクセス権チェック
        if (!$this->hasAccessPermission()) {
            return $this->forwardForbidden();
        }
    }
}
