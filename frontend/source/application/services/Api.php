<?php
/**
 * applicaiton/services/Api.php
 *
 * APIサービスクラス
 *
 * APIサービス。
 *
 * @category    Default
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Api.php 91 2014-03-26 04:31:05Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Service_Search
*/

/**
 * Service_Api クラス
 *
 * APIサービスクラス
 *
 * @category    Default
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Service_Api extends Service_Search
{
    // ------------------------------------------------------------------ //

    /**
     * WGSジェネレータWebAPI ラッパーインスタンス
     *
     * @var App_Wrapper_Web
     */
    protected $_wgsGeneratorWebApi = null;

    /**
     * hadoopコマンドAPI ラッパーインスタンス
     *
     * @var App_Wrapper_Script
     */
    protected $_hadoopCmdApi = null;

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

    // ------------------------------------------------------------------ //

    /**
     * WGSジェネレータWebAPI ラッパークラスのセット
     *
     * @param App_Wrapper_Web $value WGSジェネレータWebAPIラッパークラス
     * @return void
     */
    public function setWgsGeneratorWebApi($value)
    {
        $this->_wgsGeneratorWebApi = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * WGSジェネレータWebAPI ラッパークラスの取得
     *
     * @return App_Wrapper_Web WGSジェネレータWebAPIラッパークラス
     */
    public function getWgsGeneratorWebApi()
    {
        return $this->_wgsGeneratorWebApi;
    }

    // ------------------------------------------------------------------ //

    /**
     * hadoopコマンドAPI ラッパークラスのセット
     *
     * @param App_Wrapper_Script $value hadoopコマンドAPI ラッパークラス
     * @return void
     */
    public function setHadoopCmdApi($value)
    {
        $this->_hadoopCmdApi = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * hadoopコマンドAPI ラッパークラスの取得
     *
     * @return App_Wrapper_Script hadoopコマンドAPI ラッパークラス
     */
    public function getHadoopCmdApi()
    {
        return $this->_hadoopCmdApi;
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLSchema設定の取得
     *
     * @return array XMLSchema設定
     */
    public function getXmlSchemaConfig()
    {
        $config    = $this->getConfig();
        $xsdConfig = $config['xsd'];
        return $xsdConfig;
    }

    // ------------------------------------------------------------------ //

    /**
     * XSLT設定の取得
     *
     * @return array XSLT設定
     */
    public function getXsltConfig()
    {
        $config    = $this->getConfig();
        $xslConfig = $config['xslt'];
        return $xslConfig;
    }

    // ------------------------------------------------------------------ //

    /**
     * ジェネレータ設定の取得
     *
     * @return array ジェネレータ設定
     */
    public function getGenratorConfig()
    {
        $config = $this->getConfig();
        $genratorConfig = $config['generator'];
        return $genratorConfig;
    }

    // ------------------------------------------------------------------ //

    /**
     * キャッシュ設定の取得
     *
     * @return array キャッシュ設定
     */
    public function getCacheConfig($key = 'hdfs')
    {
        $config      = $this->getConfig();
        $cacheConfig = $config['cache'][$key];
        return $cacheConfig;
    }

    // ------------------------------------------------------------------ //

    /**
     * レスポンスがWGS XML形式かどうかチェック。
     *
     * @param DOMDocument $dom XML DOMオブジェクト
     * @return boolean 真：XMLSchemaによる妥当性チェックOK
     */
    public function validateResponseXml($dom)
    {
        // XMLSchemaで妥当性チェック
        $xsdConfig = $this->getXmlSchemaConfig();
        $xsdFile   = $xsdConfig['wgsxml'];
        $verified  = App_Xml::validateXmlDom($dom, $xsdFile);
        return $verified;
    }
}

