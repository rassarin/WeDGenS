<?php
/**
 * library/App/Bootstrap/Resource/View.php
 *
 * ビューリソースプラグイン。
 *
 * ブートストラップにてビュー初期化処理を行うためのリソースプラグイン。
 *
 * @category    App
 * @package     Bootstrap
 * @subpackage  Resource
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: View.php 31 2014-03-04 11:23:16Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Application_Resource_ResourceAbstract
*/

/**
 * App_Bootstrap_Resource_View クラス
 *
 *  ビューリソースプラグイン定義クラス
 *
 * @category    App
 * @package     Bootstrap
 * @subpackage  Resource
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Bootstrap_Resource_View extends Zend_Application_Resource_ResourceAbstract
{
    // ---------------------------------------------------------------------- //

    /**
     * @var Zend_View
     */
    protected $_view;

    // ---------------------------------------------------------------------- //

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_View
     */
    public function init()
    {
        return $this->getView();
    }

    // ---------------------------------------------------------------------- //

    /**
     * ビューのセット
     *
     * @param Zend_View $view ビューインスタンス
     * @return App_Bootstrap_Resource_View
     */
    public function setView(Zend_View $view)
    {
        $this->_view = $view;
        return $this;
    }

    // ---------------------------------------------------------------------- //

    /**
     * ビューの取得
     *
     * @return Zend_View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $options = $this->getOptions();
            $view    = $this->_initView($options);
            $this->setView($view);
        }
        return $this->_view;
    }

    // ---------------------------------------------------------------------- //

    /**
     * Zend_Viewインスタンスの初期化
     *
     * @param array $options ビュー設定オプション
     * @return Zend_View
     */
    protected function _initView($options)
    {
        $zendViewOptions = $options['zend'];
        $headerOptions   = $options['header'];

        // ビュー初期化
        $view = new Zend_View($zendViewOptions);
        $view->doctype($headerOptions['doctype']);

        if (array_key_exists('charset', $headerOptions)) {
            $view->headMeta()->setCharset('UTF-8');
        }
        if (array_key_exists('x_ua_compatible', $headerOptions)) {
            $view->headMeta()->appendHttpEquiv(
                'X-UA-Compatible',
                $headerOptions['x_ua_compatible']
            );
        }
        if (array_key_exists('viewport', $headerOptions)) {
            $view->headMeta()->appendName(
                'viewport',
                $headerOptions['viewport']
            );
        }
        if (array_key_exists('content_type', $headerOptions)) {
            $view->headMeta()->appendHttpEquiv(
                'Content-Type',
                $headerOptions['content_type']
            );
        }
        if (array_key_exists('cache_control', $headerOptions)) {
            $view->headMeta()->appendHttpEquiv(
                'Cache-Control',
                $headerOptions['cache_control']
            );
        }
        if (array_key_exists('pragma', $headerOptions)) {
            $view->headMeta()->appendHttpEquiv(
                'pragma',
                $headerOptions['pragma']
            );
        }
        if (array_key_exists('title', $headerOptions)) {
            $view->headTitle()->setSeparator(' - ');
            $view->headTitle($headerOptions['title']);
        }

        // Zend_Viewのescape()メソッドは「'」をエスケープしないので
        // 独自メソッドに変更
        $view->setEscape(array('App_Utils', 'escape'));

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper_');

        // 共通フィルタ処理のセット
        $view->addFilterPath('App/Filter', 'App_Filter_');
        $view->addFilter('RemoveBOM');

        // ViewRendererに追加
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        return $view;
    }
}
