<?php
/**
 * library/App/Xml.php
 *
 * XML操作クラス
 *
 * @category    App
 * @package     Xml
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Xml.php 88 2014-03-19 11:15:13Z nobu $
 * @since       File available since Release 1.0.0
 * @see         SimpleXMLElement
*/

/**
 * App_Xml クラス
 *
 * XML操作クラス
 *
 * @category    App
 * @package     Xml
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Xml
{
    /**
     * xsi:schemaLocation
     */
    const SCEMA_LOCATION = 'http://pc105.narc.affrc.go.jp/wgs/schema/wgsxml.xsd';

    /**
     * 名前空間：xsi
     */
    const NS_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    /**
     * WGSレスポンスXMLルートタグ
     */
    const WGS_ROOT_TAG = 'wgs';

    // ------------------------------------------------------------------ //

    /**
     * SimpleXMLドキュメント
     *
     * @var SimpleXMLElement
     */
    protected $_simplexml_document = null;

    // ------------------------------------------------------------------ //

    /**
     * XSLTProcessorインスタンス
     *
     * @var XSLTProcessor
     */
    protected $_xsltProcessor = null;

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param string $xml XMLドキュメント
     * @param string $ns 名前空間
     * @return void
     */
    public function __construct($xml = null, $ns = null)
    {
        if (!is_null($xml)) {
            $this->setXml($xml, $ns);
        }

        // 初期化メソッド
        $this->init();
    }

    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     * ：抽象メソッド
     *
     * @return void
     */
    abstract public function init();

    // ------------------------------------------------------------------ //

    /**
     * SimpleXMLドキュメントのセット
     *
     * @param SimpleXMLElement $value SimpleXMLドキュメント
     * @return void
     */
    public function setSimpleXMLDocument($value)
    {
        $this->_simplexml_document = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * SimpleXMLドキュメントの取得
     *
     * @return SimpleXMLElement SimpleXMLドキュメント
     */
    public function getSimpleXMLDocument()
    {
        return $this->_simplexml_document;
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLのセット
     *
     * @param string $xml XML文字列
     * @param string $ns 名前空間
     * @return void
     */
    public function setXml($xml, $ns = null)
    {
        $xmlDoc = self::_genSimpleXMLElelemnt($xml, $ns);
        $this->setSimpleXMLDocument($xmlDoc);
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLの取得
     *
     * @return string XML文字列
     */
    public function getXml()
    {
        $domDocument = DOMDocument::loadXML($this->getSimpleXMLDocument()->asXML());
        $domDocument->formatOutput = true;
        $domDocument->preserveWhiteSpace = false;
        $domDocument->encoding = "UTF-8";
        return $domDocument->saveXML();
    }

    // ------------------------------------------------------------------ //

    /**
     * SimpleXMLオブジェクトからDOMDocumentオブジェクトを取得
     *
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public function getDOM()
    {
        return dom_import_simplexml($this->getSimpleXMLDocument());
    }

    // ------------------------------------------------------------------ //

    /**
     * XSLTProcessorインスタンスのセット
     *
     * @return void
     */
    public function setXSLTProcessor()
    {
        $this->_xsltProcessor = new XSLTProcessor();
    }

    // ------------------------------------------------------------------ //

    /**
     * XSLTProcessorインスタンスの取得
     *
     * @return XSLTProcessor XSLTProcessorインスタンス
     */
    public function getXSLTProcessor()
    {
        if (is_null($this->_xsltProcessor)) {
            $this->setXSLTProcessor();
        }
        return $this->_xsltProcessor;
    }

    // ------------------------------------------------------------------ //

    /**
     * XSLTProcessorインスタンスの取得
     *
     * @param string $xslFile XSLTスタイルシートファイル
     * @return void
     */
    public function setStyleSheet($xslFile)
    {
        $xsl = new DOMDocument();
        $xsl->load($xslFile);
        $this->getXSLTProcessor()->importStylesheet($xsl);
    }

    // ------------------------------------------------------------------ //

    /**
     * XSLT適用後文字列の取得
     *
     * @param DOMDocument $xmlDoc DOMDocumentオブジェクト
     * @return String XSLT適用後文字列
     */
    public function transform($xmlDoc)
    {
        return $this->getXSLTProcessor()->transformToXml($xmlDoc);
    }

    // ------------------------------------------------------------------ //

    /**
     * XPathクエリーの実行
     *
     * @param string $value XPathクエリー
     * @return mixed XPath検索結果
     */
    public function searchXpath($value)
    {
        return $this->getSimpleXMLDocument()->xpath($value);
    }

    // ------------------------------------------------------------------ //

    /**
     * XML文字列からDOMDocumentオブジェクトの取得
     *
     * @param string $xml XML文字列
     * @param string $ns 名前空間
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public static function getDOMDocumentFromXml($xml, $ns = null)
    {
        $simple = self::_genSimpleXMLElelemnt($xml, $ns);
        return dom_import_simplexml($simple);
    }

    // ------------------------------------------------------------------ //

    /**
     * DOMDocumentオブジェクトの生成
     *
     * @return string $rootTag ルートノードのタグ
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public static function genDOMDocument($rootTag)
    {
        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $domDocument->formatOutput       = true;
        $domDocument->preserveWhiteSpace = true;

        $rootNode    = $domDocument->createElement($rootTag);
        $domDocument->appendChild($rootNode);
        return $domDocument;
    }

    // ------------------------------------------------------------------ //

    /**
     * DOMDocumentオブジェクト(名前空間使用)の生成
     *
     * @return string $rootTag ルートノードのタグ
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public static function genDOMDocumentNS($rootTag)
    {
        $attrs = array(
            'xmlns:xsi' => self::NS_XSI,
        );

        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $domDocument->formatOutput       = true;
        $domDocument->preserveWhiteSpace = true;

        $rootNode = $domDocument->createElement($rootTag);
        $rootNode->setAttributeNS(
            self::NS_XSI,
            'xsi:noNamespaceSchemaLocation',
            self::SCEMA_LOCATION
        );

        $domDocument->appendChild($rootNode);
        return $domDocument;
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLSchemaによるXMLファイルの妥当性チェック
     *
     * @param string $xmlFile XMLファイルパス
     * @param string $xsdFile XMLSchemaファイルパス
     * @return boolean 真：XMLSchemaによる妥当性チェックOK
     */
    public static function validateXmlFile($xml, $xsdFile)
    {
        try {
            $document = DOMDocument::load($xml);
            $verified = $document->schemaValidate($xsdFile);
            return $verified;
        } catch (Exception $exception) {
            return false;
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLSchemaによるバリデーション
     *
     * @param DOMDocument $dom DOMDocumentオブジェクト
     * @param string $xsdFile XSDファイル
     * @return boolean バリーデート結果
     */
    public static function validateXmlDom($dom, $xsdFile)
    {
        try {
            $document = DOMDocument::loadXML($dom->saveXML());
            $verified = $document->schemaValidate($xsdFile);
            return $verified;
        } catch (Exception $exception) {
            return false;
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLSchemaによるバリデーション
     *
     * @param string $xml XML文字列
     * @param string $xsdFile XSDファイル
     * @return boolean バリーデート結果
     */
    public static function validateXml($xml, $xsdFile)
    {
        try {
            $document = DOMDocument::loadXML($xml);
            $verified = $document->schemaValidate($xsdFile);
            return $verified;
        } catch (Exception $exception) {
            return false;
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * XMLの整形
     *
     * @return string $xml XML文字列
     * @return boolean $trim 真：XML宣言を除去する
     * @return string XML文字列(整形後)
     */
    public static function formatXml($xml, $trim = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $xml;
        $domDocument = DOMDocument::loadXML($xml);
        $domDocument->formatOutput = true;
        $domDocument->preserveWhiteSpace = true;
        $domDocument->encoding = "UTF-8";
        if ($trim) {
            return preg_replace(
                '/^<\?xml version=\"1\.0\" encoding=\"UTF\-8\"\?>/u',
                '',
                $domDocument->saveXML()
            );
        }
        return $domDocument->saveXML();
    }

    // ------------------------------------------------------------------ //

    /**
     * 通知XML DOMの生成
     *
     * @param string $message 通知メッセージ
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public static function notifyXml($message)
    {
        $rootTag  = self::WGS_ROOT_TAG;
        $dom      = self::genDOMDocumentNS($rootTag);
        $docRoot  = $dom->documentElement;
        $response = $dom->createElement('response');
        $response->appendChild($dom->createTextNode($message));
        $docRoot->appendChild($response);
        return $dom;
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーXML DOMの生成
     *
     * @param string $message エラーメッセージ
     * @return DOMDocument DOMDocumentオブジェクト
     */
    public static function errorXml($message)
    {
        $rootTag = self::WGS_ROOT_TAG;
        $dom     = self::genDOMDocumentNS($rootTag);
        $docRoot = $dom->documentElement;
        $error   = $dom->createElement('error');
        $error->appendChild($dom->createTextNode($message));
        $docRoot->appendChild($error);
        return $dom;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモデルクラスを生成する
     *
     * @param  string $modelName モデル名
     * @return App_Xml XMLモデルクラス
     */
    public static function getXmlModelClass($modelName)
    {
        // モデル名からテーブルモデルクラスを生成する。
        $className     = self::getXmlModelClassName($modelName);
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からXMLクラス名を取得する
     *
     * @param  string $dataName モデル名
     * @return string XMLクラス名
     */
    public static function getXmlModelClassName($dataName)
    {
        $className = 'Model_Xml_'
                   . ucfirst(App_Utils::dashToCamelCase($dataName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールモデルクラスを生成する
     *
     * @param  string $moduleName モジュール名
     * @param  string $modelName モデル名
     * @return App_Xml XMLモデルクラス
     */
    public static function getXmlModuleModelClass($moduleName, $modelName)
    {
        // モデル名からモジュールモデルクラスを生成する。
        $className     = self::getXmlModuleModelClassName($moduleName, $modelName);
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールXMLクラス名を取得する
     *
     * @param  string $moduleName モジュール名
     * @param  string $dataName モデル名
     * @return string XMLクラス名
     */
    public static function getXmlModuleModelClassName($moduleName, $dataName)
    {
        $className = ucfirst(strtolower($moduleName))
                   . '_Model_Xml_'
                   . ucfirst(App_Utils::dashToCamelCase($dataName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * SimpleXMLElementオブジェクトの生成
     *
     * @param string $xml XML文字列
     * @param string $ns 名前空間
     * @return SimpleXMLElement SimpleXMLElementオブジェクト
     */
    private static function _genSimpleXMLElelemnt($xml, $ns = null)
    {
        if (!is_null($ns)) {
            $xmlDoc = new SimpleXMLElement(
                $xml, LIBXML_NOCDATA, null, $ns
            );
        } else {
            $xmlDoc = new SimpleXMLElement($xml, LIBXML_NOCDATA);
        }
        return $xmlDoc;
    }
}
