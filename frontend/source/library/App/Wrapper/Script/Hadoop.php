<?php
/**
 * library/App/Wrapper/Script/Hadoop.php
 *
 * hadoopコマンド実行スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @subpackage  Script
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Hadoop.php 86 2014-03-19 09:03:41Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Wrapper_Script
*/

/**
 * App_Wrapper_Script_Hadoop クラス
 *
 * hadoopコマンド実行スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @subpackage  Script
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Wrapper_Script_Hadoop extends App_Wrapper_Script
{
    // ------------------------------------------------------------------ //

    /**
     * 初期化メソッド
     *
     * @return void
     */
    public function init()
    {
        /* no implements */
    }

    // ------------------------------------------------------------------ //

    /**
     * hdfs putコマンド実行シェルスクリプトのセット
     *
     * @return void
     */
    public function setHdfsPutScript()
    {
        $config     = $this->getHdfsConfig();
        $scriptPath = $config['cmd_path']['put'];
        $this->setScript($scriptPath);
    }

    // ------------------------------------------------------------------ //

    /**
     * hdfs catコマンド実行シェルスクリプトのセット
     *
     * @return void
     */
    public function setHdfsCatScript()
    {
        $config     = $this->getHdfsConfig();
        $scriptPath = $config['cmd_path']['cat'];
        $this->setScript($scriptPath);
    }

    // ------------------------------------------------------------------ //

    /**
     * hdfs removeコマンド実行シェルスクリプトのセット
     *
     * @return void
     */
    public function setHdfsRemoveScript()
    {
        $config     = $this->getHdfsConfig();
        $scriptPath = $config['cmd_path']['remove'];
        $this->setScript($scriptPath);
    }

    // ------------------------------------------------------------------ //

    /**
     * hdfs testコマンド実行シェルスクリプトのセット
     *
     * @return void
     */
    public function setHdfsExistsScript()
    {
        $config     = $this->getHdfsConfig();
        $scriptPath = $config['cmd_path']['exists'];
        $this->setScript($scriptPath);
    }

    // ------------------------------------------------------------------ //

    /**
     * hdfs statコマンド実行シェルスクリプトのセット
     *
     * @return void
     */
    public function setHdfsMtimeScript()
    {
        $config     = $this->getHdfsConfig();
        $scriptPath = $config['cmd_path']['mtime'];
        $this->setScript($scriptPath);
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のユーザデータファイル格納パスの取得のセット
     *
     * @param string $userDataId ユーザデータID
     * @return string HDFS上のユーザデータファイル格納パス
     */
    public function getUserDataFilePath($userDataId)
    {
        return $this->_getHdfsDataFilePath($userDataId, 'user_data');
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のリクエスト実行結果ファイル格納パスの取得のセット
     *
     * @param string $requestId リクエストID
     * @return string HDFS上のリクエスト実行結果ファイル格納パス
     */
    public function getResultDataFilePath($requestId)
    {
        return $this->_getHdfsDataFilePath($requestId, 'result_data');
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のリクエストパラメータファイル格納パスの取得のセット
     *
     * @param string $requestId リクエストID
     * @return string HDFS上のリクエストパラメータファイル格納パス
     */
    public function getRequestParamFilePath($requestId)
    {
        return $this->_getHdfsDataFilePath($requestId, 'param_data');
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のデータソース定義XMLファイル格納パスの取得
     *
     * @param integer $dataTypeId データ種別ID
     * @return string HDFS上のデータソース定義XMLファイル格納パス
     */
    public function getSourceDataFilePath($dataTypeId)
    {
        $tmp    = array();
        $config = $this->getHdfsConfig();
        $dataTypeKey = $this->_getDataTypeKey($dataTypeId);

        if (array_key_exists('url', $config)) {
            array_push($tmp, preg_replace('/\/$/u', '', $config['url']));
        }
        if (array_key_exists('root_path', $config) && is_array($config['root_path'])) {
            if (array_key_exists($dataTypeKey, $config['root_path'])) {
                if (!App_Utils::isEmpty($config['root_path'][$dataTypeKey])) {
                    array_push(
                        $tmp,
                        preg_replace('/\/$/u', '', $config['root_path'][$dataTypeKey])
                    );
                }
            }
        }
        if (App_Utils::isEmpty($tmp)) {
            return null;
        }
        array_push($tmp, '/' . $config['file_name']['source_data']);

        return implode('', $tmp);
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のステーション定義XMLファイル格納パスの取得
     *
     * @param integer $dataTypeId データ種別ID
     * @param integer $sourceId データソースID
     * @return string HDFS上のステーション定義XMLファイル格納パス
     */
    public function getStationDataFilePath($dataTypeId, $sourceId)
    {
        $tmp    = array();
        $config = $this->getHdfsConfig();
        $dataTypeKey = $this->_getDataTypeKey($dataTypeId);

        if (array_key_exists('url', $config)) {
            array_push($tmp, preg_replace('/\/$/u', '', $config['url']));
        }
        if (array_key_exists('root_path', $config) && is_array($config['root_path'])) {
            if (array_key_exists($dataTypeKey, $config['root_path'])) {
                if (!App_Utils::isEmpty($config['root_path'][$dataTypeKey])) {
                    array_push(
                        $tmp,
                        preg_replace('/\/$/u', '', $config['root_path'][$dataTypeKey])
                    );
                }
            }
        }
        if (App_Utils::isEmpty($tmp)) {
            return null;
        }
        array_push($tmp, '/' . $sourceId);
        array_push($tmp, '/' . $config['file_name']['station_data']);

        return implode('', $tmp);
    }

    // ------------------------------------------------------------------ //

    /**
     * コマンドライン生成メソッド
     *
     * @return void
     */
    public function createCommandLine()
    {
        $tmp = array();
        $commandPath = $this->getScript();
        if (!is_executable($commandPath)) {
            throw new App_Exception(
                'hadoopコマンドの実行に失敗しました。',
                App_Exception::APP_IO_ERROR
            );
        }
        array_push($tmp, $commandPath);
        array_push($tmp, $this->createOptions());
        array_push($tmp, $this->createArgs());

        return implode(' ', $tmp);
    }

    // ------------------------------------------------------------------ //

    /**
     * オプション生成メソッド
     *
     * @return string オプション
     */
    public function createOptions()
    {
        $tmp = array();

        if (is_array($this->getOptions())) {
            foreach($this->getOptions() as $key => $value) {
                if (is_null($value)) {
                    array_push($tmp, $key);
                } else {
                    if (!preg_match('/^\s*$/', $key)) {
                        array_push($tmp, "$key $value");
                    }
                }
            }
            return implode(' ', $tmp);
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * 引数生成メソッド
     *
     * @return string 引数
     */
    public function createArgs()
    {
        if (is_array($this->getArgs())) {
            return implode(' ', $this->getArgs());
        }
        return null;
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS設定の取得
     *
     * @return array HDFS設定
     */
    public function getHdfsConfig()
    {
        $config = $this->getConfig();
        return $config->hdfs->toArray();
    }

    // ------------------------------------------------------------------ //

    /**
     * HDFS上のデータファイル格納パスの取得
     *
     * @param string $userDataId ユーザデータID
     * @param string $key キー名
     * @return string HDFS上のデータファイル格納パス
     */
    private function _getHdfsDataFilePath($userDataId, $key)
    {
        $tmp    = array();
        $config = $this->getHdfsConfig();

        if (array_key_exists('url', $config)) {
            array_push($tmp, preg_replace('/\/$/u', '', $config['url']));
        }
        if (array_key_exists('root_path', $config) && is_array($config['root_path'])) {
            if (array_key_exists($key, $config['root_path'])) {
                if (!App_Utils::isEmpty($config['root_path'][$key])) {
                    array_push(
                        $tmp,
                        preg_replace('/\/$/u', '', $config['root_path'][$key])
                    );
                }
            }
        }
        if (App_Utils::isEmpty($tmp)) {
            return null;
        }
        array_push($tmp, '/' . $userDataId);
        array_push($tmp, '/' . $config['file_name'][$key]);

        return implode('', $tmp);
    }

    // ------------------------------------------------------------------ //

    /**
     * データ種別IDからキー名を取得する。
     *
     * @param integer $dataTypeId データ種別ID
     * @return string データ種別キー名
     */
    private function _getDataTypeKey($dataTypeId)
    {
        $config = $this->getHdfsConfig();
        $map    = $config['dataTypeIdMapping'];
        if (array_key_exists($dataTypeId, $map)) {
            return $map[$dataTypeId];
        }
        return null;
    }
}

