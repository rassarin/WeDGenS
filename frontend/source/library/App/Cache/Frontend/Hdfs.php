<?php
/**
 * library/App/Cache/Frontend/Hdfs.php
 *
 * HDFSファイルキャッシュフロントエンドクラス
 *
 * @category    App
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Hdfs.php 72 2014-03-13 05:57:14Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Cache_Frontend_File
*/

/**
 * App_Cache_Frontend_Hdfs クラス
 *
 * HDFSファイルキャッシュフロントエンドクラス
 *
 * @category    App
 * @package     Base
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Cache_Frontend_Hdfs extends Zend_Cache_Frontend_File
{
    // ------------------------------------------------------------------ //

    /**
     * hadoopコマンドAPI ラッパーインスタンス
     *
     * @var App_Wrapper_Script
     */
    protected $_hadoopCmdApi = null;

    // ------------------------------------------------------------------ //

    /**
     * 常にキャッシュを使用するかどうかのフラグ
     *
     * @var boolean
     */
    protected $_alwaysCacheUse = false;

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     * (Zend_Cache_Frontend_File::__constructをオーバーライド)
     *
     * @param  array $options キャッシュ設定
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (array_key_exists('always_cache_use', $options)) {
            if ($options['always_cache_use']) {
                $this->_alwaysCacheUse = true;
            }
        }
        while (list($name, $value) = each($options)) {
            $this->setOption($name, $value);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * マスターファイルのセット
     * (Zend_Cache_Frontend_File::setMasterFilesをオーバーライド)
     *
     * @param array $masterFiles マスターファイルリスト
     * @return void
     */
    public function setMasterFiles(array $masterFiles)
    {
        $this->_specificOptions['master_file']  = null; // to keep a compatibility
        $this->_specificOptions['master_files'] = null;
        $this->_masterFile_mtimes = array();

        clearstatcache();
        $i = 0;
        foreach ($masterFiles as $masterFile) {
            if ($this->_alwaysCacheUse) {
                $mtime = 1;
            } else {
                if ($this->_existsHdfsFile($masterFile)) {
                    $mtime = $this->_getMtimeHdfsFile($masterFile);
                } else {
                    $mtime = false;
                }
            }

            if (!$this->_specificOptions['ignore_missing_master_files'] && !$mtime) {
                Zend_Cache::throwException('Unable to read master_file : ' . $masterFile);
            }

            $this->_masterFile_mtimes[$i] = $mtime;
            $this->_specificOptions['master_files'][$i] = $masterFile;
            if ($i === 0) { // to keep a compatibility
                $this->_specificOptions['master_file'] = $masterFile;
            }

            $i++;
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * マスターファイルのセット
     * (Zend_Cache_Frontend_File::testをオーバーライド)
     *
     * @param  string $id キャッシュID
     * @return integer mtime
     */
    public function test($id)
    {
        $grandParent  = get_parent_class(get_parent_class($this));
        $lastModified = $grandParent::test($id);
        if ($lastModified) {
            if ($this->_alwaysCacheUse) {
                return $lastModified;
            }
            if ($this->_specificOptions['master_files_mode'] == self::MODE_AND) {
                // MODE_AND
                foreach($this->_masterFile_mtimes as $masterFileMTime) {
                    if ($masterFileMTime) {
                        if ($lastModified > $masterFileMTime) {
                            return $lastModified;
                        }
                    }
                }
            } else {
                // MODE_OR
                $res = true;
                foreach($this->_masterFile_mtimes as $masterFileMTime) {
                    if ($masterFileMTime) {
                        if ($lastModified <= $masterFileMTime) {
                            return false;
                        }
                    }
                }
                return $lastModified;
            }
        }
        return false;
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
     * 常にキャッシュを使用するかどうかのフラグのセット
     *
     * @param boolean $value 常にキャッシュを使用するかどうかのフラグ
     * @return void
     */
    public function setAlwaysUseCache($value)
    {
        $this->_alwaysCacheUse = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * 逐一ファイルのタイムスタンプを確認するかどうかのフラグ取得
     *
     * @return boolean 真：常にキャッシュを使用する
     */
    public function getAlwaysUseCache()
    {
        return $this->_alwaysCacheUse;
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のファイル存在チェック。
     *
     * @param string $file HDFSファイルパス
     * @return boolean 真：存在する
     */
    private function _existsHdfsFile($file)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsExistsScript();

        /*
         *  HDFS上のファイル存在チェックコマンドの引数をセット
         *  - 第1引数：HDFS格納パス
         */
        array_push($args, $file);
        $hadoopCmdApi->setArgs($args);

        try {
            $results = $hadoopCmdApi->openScript();
            if (is_array($results)) {
                $line = $results[0];
                if (!preg_match('/^FAILURE/', $line)) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new App_Exception(
                'HDFSファイルのアクセスに失敗しました。',
                App_Exception::APP_IO_ERROR
            );
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のファイルのmtimeを取得する。
     *
     * @param string $file HDFSファイルパス
     * @return string mtime
     */
    private function _getMtimeHdfsFile($file)
    {
        $results = array();
        $args    = array();
        $hadoopCmdApi = $this->getHadoopCmdApi();
        $hadoopCmdApi->setHdfsMtimeScript();

        /*
         *  HDFS上のファイルのstat取得コマンドの引数をセット
         *  - 第1引数：HDFS格納パス
         */
        array_push($args, $file);
        $hadoopCmdApi->setArgs($args);

        try {
            $results = $hadoopCmdApi->openScript();
            if (is_array($results)) {
                $line = $results[0];
                if (!preg_match('/^FAILURE/', $line)) {
                    return strtotime($line);
                }
            }
            throw new App_Exception(
                'HDFSファイルのアクセスに失敗しました。',
                App_Exception::APP_IO_ERROR
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}

