<?php
/**
 * library/App/Controller/Plugin.php
 *
 * プラグイン基底クラス。
 *
 * @category    App
 * @package     Controller
 * @subpackage  Plugin
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Plugin.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Plugin_Abstract
*/

/**
 * App_Controller_Plugin クラス
 *
 * プラグイン基底クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  Plugin
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    // ------------------------------------------------------------------ //

    /**
     * サービスの取得
     *
     * @param  string $className サービスクラス名
     * @return App_Service サービスクラスインスタンス
     */
    public function getService($serviceName)
    {
        // サービスクラスのインスタンスを取得
        return App_Utils::getService($serviceName);
    }
}
