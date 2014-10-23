<?php
/**
 * library/App/Wrapper.php
 *
 * 外部スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Wrapper.php 11 2014-02-25 11:49:12Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Wrapper クラス
 *
 * 外部スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Wrapper
{
    /**
     * 設定
     *
     * @var Zend_Config_Ini
     */
    protected $_config = null;

    /**
     * 標準出力内容
     *
     * @var mixed
     */
    protected $_stdout = null;

    /**
     * 標準エラー出力内容
     *
     * @var mixed
     */
    protected $_stderr   = null;

    /**
     * リターンコード
     *
     * @var integer
     */
    protected $_returnCode = null;

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param string $configPath 設定ファイルパス
     * @param string $section セクション名
     * @return void
     */
    public function __construct($configFile, $section)
    {
        // 設定読み込み
        $this->_loadConfig($configFile, $section);

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
     * 設定のセット
     *
     * @param Zend_Config_Ini $value 設定
     * @return void
     */
    public function setConfig($value)
    {
        $this->_config = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * 設定の取得
     *
     * @return Zend_Config_Ini 設定
     */
    public function getConfig()
    {
        return $this->_config;
    }

    // ------------------------------------------------------------------ //

    /**
     * リターンコードのセット
     *
     * @param integer $value リータンコード
     * @return void
     */
    public function setReturnCode($value)
    {
        $this->_returnCode = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * リターンコードの取得
     *
     * @return integer リータンコード
     */
    public function getReturnCode()
    {
        return $this->_returnCode;
    }

    // ------------------------------------------------------------------ //

    /**
     * 標準出力内容のセット
     *
     * @param mixed $value 標準出力内容
     * @return void
     */
    public function setStdout($value)
    {
        $this->_stderr = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * 標準出力内容の取得
     *
     * @return mixed 標準出力内容
     */
    public function getStdout()
    {
        return $this->_stderr;
    }

    // ------------------------------------------------------------------ //

    /**
     * 標準エラー出力内容のセット
     *
     * @param mixed $value 標準エラー出力内容
     * @return void
     */
    public function setStderr($value)
    {
        $this->_stderr = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * 標準エラー出力内容の取得
     *
     * @return mixed 標準エラー出力内容
     */
    public function getStderr()
    {
        return $this->_stderr;
    }

    // ------------------------------------------------------------------ //

    /**
     * 設定の読み込み
     *
     * @param string $configFile 設定ファイルパス
     * @param string $section セクション名
     * @return void
     */
    private function _loadConfig($configFile, $section = null)
    {
        // 設定読み込み
        $cache   = App_Utils::getConfigCache($configFile);
        $cacheId = get_class($this)
                 .'_'
                 . preg_replace('/\./', '_', basename($configFile));
        if (!is_null($section)) {
            $cacheId .= '_' . $section;
        }

        $cacheId = preg_replace('/\-/', '_', $cacheId);

        if (!($config = $cache->load($cacheId))) {
            $config = App_Utils::loadConfig($configFile, $section);
            $cache->save($config, $cacheId);
        }
        $this->setConfig($config);
    }
}

