<?php
/**
 * library/App/Controller/Action/Helper/ModuleService.php
 *
 * モジュールサービスヘルパー。
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: ModuleService.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Controller_Action_Helper_Abstract
*/

/**
 * App_Controller_Action_Helper_ModuleService クラス
 *
 * @category    App
 * @package     Controller
 * @subpackage  ActionHelper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Controller_Action_Helper_ModuleService extends Zend_Controller_Action_Helper_Abstract
{
    // ------------------------------------------------------------------ //

    /**
     * @see Zend_Controller_Action_Helper_Abstract::getName()
     */
    public function getName()
    {
        return 'ModuleService';
    }

    // ------------------------------------------------------------------ //

    /**
     * 「モジュール名_Service_サービス名」クラスのインスタンス取得
     *
     * @see Zend_Controller_Action_Helper_Abstract::direct()
     * @param string $serviceName サービス名
     * @return object
     */
    public function direct($serviceName)
    {
        $moduleName = $this->getRequest()->getModuleName();
        return App_Utils::getModuleService($serviceName, $moduleName);
    }

    // ------------------------------------------------------------------ //

    /**
     * @param string $serviceName サービス名
     * @return object
     */
    public function __get($serviceName)
    {
        return $this->direct($serviceName);
    }
}
