<?php
/**
 * library/App/Wrapper/Web/WgsGenerator.php
 *
 * 気象データ生成WebAPIラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @subpackage  Web
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: WgsGenerator.php 49 2014-03-07 08:43:58Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Wrapper_Web
*/

/**
 * App_Wrapper_Web_WgsGenerator クラス
 *
 * 気象データ生成WebAPIラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @subpackage  Web
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Wrapper_Web_WgsGenerator extends App_Wrapper_Web
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        $config = $this->getConfig();
        $this->getClient()->setConfig($config->client);
    }

    // ------------------------------------------------------------------ //

    /**
     * Webサービスアクセスメソッド
     *
     * @param string $url URL
     * @param array $params パラメータ
     * @return mixed JSONレスポンス
     */
    public function access($url, $params)
    {
        $xmlDoc = null;
        $this->getClient()->setUri($url);
        $this->getClient()->setMethod(Zend_Http_Client::GET);
        $this->getClient()->setParameterGet($params);

        $response = $this->getClient()->request();
        if ($response->isError()) {
            throw new App_Exception(
                'Generator WebAPIのアクセスに失敗しました：' .
                $response->getStatus() . ' ' . $response->getMessage(),
                App_Exception::APP_IO_ERROR
            );
        }

        // Content-typeのチェック(text/javascript、application/jsonのみ許可)
        $contentType = array_shift(preg_split('/;/', $response->getHeader('Content-type')));
        if (!preg_match('/^(text\/javascript|application\/json)$/', $contentType)) {
            throw new App_Exception(
                '不正なGenerator WebAPIレスポンスです',
                App_Exception::APP_API_ERROR
            );
        }
        $jsonObj = App_Json::decode($response->getBody());
        $this->getClient()->resetParameters(true);

        return $jsonObj;
    }
}

