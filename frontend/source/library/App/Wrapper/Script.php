<?php
/**
 * library/App/Wrapper/Script.php
 *
 * 外部スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Script.php 16 2014-02-26 07:51:24Z nobu $
 * @since       File available since Release 1.0.0
 * @see         App_Wrapper
*/

/**
 * App_Wrapper_Script クラス
 *
 * 外部スクリプトラッパークラス
 *
 * @category    App
 * @package     Wrapper
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
abstract class App_Wrapper_Script extends App_Wrapper
{
    /*
     * psコマンド
     */
    const CMD_PS = '/bin/ps';

    /*
     * grepコマンド
     */
    const CMD_GREP = '/bin/grep';

    /*
     * killコマンド
     */
    const CMD_KILL = '/bin/kill';

    // ------------------------------------------------------------------ //

    /**
     * スクリプト名
     *
     * @var string
     */
    protected $_scriptName = null;

    /**
     * オプション値
     *
     * @var array
     */
    protected $_options = null;

    /**
     * 引数値
     *
     * @var array
     */
    protected $_args = null;

    /**
     * タイムアウト値
     *
     * @var array
     */
    protected $_timeout = null;

    /**
     * チェック間隔
     *
     * @var array
     */
    protected $_checkInterval = null;

    // ------------------------------------------------------------------ //

    /**
     * コマンドライン生成メソッド
     * ：抽象メソッド
     *
     * @return void
     */
    abstract public function createCommandLine();

    // ------------------------------------------------------------------ //

    /**
     * コマンドライン実行メソッド
     *
     * @return integer|boolean PID|偽：起動失敗
     */
    public function run()
    {
        $command = $this->createCommandLine();
        $output  = $this->execBgCommand($command);
        $pid     = trim($output[0]);

        if (preg_match('/^\d+$/', $pid)) {
            return $pid;
        }

        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * スクリプト実行し、結果を取得
     *
     * @return string|boolean 実行結果|偽：起動失敗
     */
    public function openScript()
    {
        $command = $this->createCommandLine();
        $output  = $this->execOpen($command);

        if ($output === false) {
            return false;
        }
        return $output;
    }

    // ------------------------------------------------------------------ //

    /**
     * ファアグランドスクリプト実行し、結果を取得
     *
     * @return string|boolean 実行結果|偽：起動失敗
     */
    public function runFgScript()
    {
        $command   = $this->createCommandLine();
        $returnVal = $this->execFgCommand($command);

        if ($returnVal === false) {
            return false;
        }
        return $returnVal;
    }

    // ------------------------------------------------------------------ //

    /**
     * バックグラウンドスクリプト起動
     *
     * @param boolean $wait 実行完了待ちフラグ
     * @return integer|boolean PID|偽：起動失敗
     */
    public function runBgScript($wait = false)
    {
        $pid = $this->run();
        if ($pid === false) {
            return false;
        }

        if ($wait) {
            $current       = 0;
            $timeout       = $this->getTimeout();
            $checkInterval = $this->getCheckInterval();

            while( $current < $timeout ) {
                sleep($checkInterval);
                $current += $checkInterval;
                if (!$this->isRunning($pid)) {
                   return true;
                }
            }

            $this->killScript($pid);
            return false;
        }
        return $pid;
    }

    // ------------------------------------------------------------------ //

    /**
     * スクリプト実行状態チェック
     *
     * @param integer $pid プロセスID
     * @return boolean 実行中判定フラグ
     */
    public function isRunning($pid)
    {
        $grepCmd = self::CMD_PS         . " -eaf | "
                 . self::CMD_GREP       . " "
                 . escapeshellcmd($pid) . " 2> /dev/null";
        exec($grepCmd, $psTable);

        foreach ($psTable as $row) {
            $columns   = preg_split('/\s+/', $row);
            $pidColumn = $columns[1];
            if($pid == $pidColumn) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * スクリプト強制終了
     *
     * @param integer $pid プロセスID
     * @return boolean 停止処理正常終了フラグ
     */
    public function killScript($pid)
    {
        $killCmd = self::CMD_KILL . " -9 $pid";
        exec(escapeshellcmd($killCmd), $output);
        if (!$this->isRunning($pid)) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * exec関数実行(実行のみ)
     *
     * @param string $command コマンドライン
     * @return array 出力結果
     */
    public function execBgCommand($command)
    {
        $stdErrLog = $this->_getStdErrLog();
        $escaped   = escapeshellcmd($command)
                   . ' > ' . $stdErrLog . ' 2>&1 & echo $!';
        exec($escaped, $output);
        return $output;
    }

    // ------------------------------------------------------------------ //

    /**
     * exec関数フォアグランド実行(実行のみ)
     *
     * @param string $command コマンドライン
     * @return integer|boolean 戻り値
     */
    public function execFgCommand($command)
    {
        $stdErrLog = $this->_getStdErrLog();
        $escaped   = escapeshellcmd($command)
                   . ' > ' . $stdErrLog . ' 2>&1';
        exec($escaped, $output, $returnVal);
        if (!preg_match('/^\d+$/', $returnVal)) {
            return false;
        }
        return $returnVal;
    }

    // ------------------------------------------------------------------ //

    /**
     * exec関数実行(結果取得)
     *
     * @param string $command コマンドライン
     * @return array 出力結果
     */
    public function execOpen($command)
    {
        $stdErrLog = $this->_getStdErrLog();
        $escaped   = escapeshellcmd($command)
                   . ' 2> ' . $stdErrLog;
        exec($escaped, $output);
        return $output;
    }

    // ------------------------------------------------------------------ //

    /**
     * スクリプト名のセット
     *
     * @param string $value スクリプト名
     * @return void
     */
    public function setScript($value)
    {
        $this->_scriptName = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * スクリプト名の取得
     *
     * @return string スクリプト名
     */
    public function getScript()
    {
        return $this->_scriptName;
    }

    // ------------------------------------------------------------------ //

    /**
     * オプション値のセット
     *
     * @param array $value オプション値
     * @return void
     */
    public function setOptions($value)
    {
        $this->_options = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * オプション値の取得
     *
     * @return array オプション値
     */
    public function getOptions()
    {
        return $this->_options;
    }

    // ------------------------------------------------------------------ //

    /**
     * 引数値のセット
     *
     * @param array $value 引数値
     * @return void
     */
    public function setArgs($value)
    {
        $this->_args = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * 引数値の取得
     *
     * @return array 引数値
     */
    public function getArgs()
    {
        return $this->_args;
    }

    // ------------------------------------------------------------------ //

    /**
     * タイムアウト値のセット
     *
     * @param integer $value タイムアウト値
     * @return void
     */
    public function setTimeout($value)
    {
        $this->_timeout = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * タイムアウト値の取得
     *
     * @return integer タイムアウト値
     */
    public function getTimeout()
    {
        if (is_null($this->_timeout)) {
            $this->_timeout = $this->getConfig()->timeout;
        }
        return $this->_timeout;
    }

    // ------------------------------------------------------------------ //

    /**
     * チェック間隔のセット
     *
     * @param integer $value チェック間隔
     * @return void
     */
    public function setCheckInterval($value)
    {
        $this->_checkInterval = $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * チェック間隔の取得
     *
     * @return integer チェック間隔
     */
    public function getCheckInterval()
    {
        if (is_null($this->_checkInterval)) {
            $this->_checkInterval = $this->getConfig()->check_interval;
        }
        return $this->_checkInterval;
    }

    // ------------------------------------------------------------------ //

    /**
     * 標準エラー出力ログファイルパスの取得
     *
     * @return string 標準エラー出力ログファイルパス
     */
    private function _getStdErrLog()
    {
        $stdErrLog = '/dev/null';
        $config    = $this->getConfig()->toArray();
        if (array_key_exists('stderr_log_enable', $config) && $config['stderr_log_enable']) {
            $stdErrLog = APP_ROOT_PATH . "/var/logs/"
                       . App_Utils::generateUniqueId() . ".log";
        }
        return $stdErrLog;
    }
}

