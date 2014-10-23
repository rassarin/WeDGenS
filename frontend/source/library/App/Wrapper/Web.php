<?php
/**
 * library/App/Wrapper/Web.php
 *
 * Webサービスラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Web.php 2 2014-02-20 09:37:39Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Wrapper, Zend_Http_Client
*/

/**
 * App_Wrapper_Web クラス
 *
 * Webサービスラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Wrapper_Web extends App_Wrapper
{

    // ------------------------------------------------------------------ //

    /**
     * Httpクライアント
     *
     * @var Zend_Http_Client
     */
    protected $_client = null;

    // ------------------------------------------------------------------ //

    /**
     * Webサービスアクセスメソッド
     *
     * @return mixed レスポンス
     */
    abstract public function access($url, $params);

    // ------------------------------------------------------------------ //

    /**
     * Httpクライアントのセット
     *
     * @param Zend_Http_Client $value Httpクライアント
     * @return void
     */
    public function setClient($value)
    {
        $this->_client = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * Httpクライアントの取得
     *
     * @return Zend_Http_Client Httpクライアント
     */
    public function getClient()
    {
        if (is_null($this->_client)) {
            $this->_client = new Zend_Http_Client();
        }
        return $this->_client;
    }

    // ------------------------------------------------------------------ //

    /**
     * 直近のリクエストの取得
     *
     * @return string 直近のリクエスト
     */
    public function getLastRequest()
    {
        return $this->getClient()->getLastRequest();
    }
}

