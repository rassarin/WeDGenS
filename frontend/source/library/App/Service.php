<?php
/**
 * library/App/Service.php
 *
 * サービス抽象クラス
 *
 * @category    App
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Service.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Base
*/

/**
 * App_Service クラス
 *
 * サービス抽象クラス
 *
 * @category    App
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Service extends App_Base
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        // 共通初期化処理を記述
    }
}

