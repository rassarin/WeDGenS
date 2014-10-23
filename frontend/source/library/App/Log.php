<?php
/**
 * library/App/Log.php
 *
 * アプリケーションログ取得クラス。
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Log.php 12 2014-02-26 02:23:33Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Log
*/

/**
 * App_Log クラス
 *
 * アプリケーションログ取得クラス
 *
 * @category    App
 * @package     Log
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Log extends Zend_Log
{
    /**
     * ログID
     */
    const LOG_ITEM_LOG_ID = 'logId';

    /**
     * アクセス元IPアドレス
     */
    const LOG_ITEM_IP_ADDR = 'ipAddr';

    /**
     * ユーザエージェント
     */
    const LOG_ITEM_USER_AGENT = 'userAgent';

    /**
     * リファラー
     */
    const LOG_ITEM_REFERER = 'referer';

    /**
     * 実行ステータスコード
     */
    const LOG_ITEM_CODE = 'code';

    /**
     * ユーザID
     */
    const LOG_ITEM_USER_ID = 'userId';

    /**
     * タイムスタンプ
     */
    const LOG_ITEM_TIMESTAMP = 'timestamp';

    /**
     * ログ出力メッセージ
     */
    const LOG_ITEM_MESSAGE = 'message';

    /**
     * ログ出力プライオリティ
     */
    const LOG_ITEM_PRIORITY = 'priority';

    /**
     * ログ出力プライオリティ名
     */
    const LOG_ITEM_PRIORITY_NAME = 'priorityName';

    /**
    * 呼び出し元
    */
    const LOG_ITEM_CALLER_NAME = 'caller';

    /**
     * モデル名
     */
    const LOG_ITEM_MODEL_NAME = 'model';

    /**
     * モジュール名
     */
    const LOG_ITEM_MODULE_NAME = 'module';

    /**
     * コントローラ名
     */
    const LOG_ITEM_CONTROLLER_NAME = 'controller';

    /**
     * アクション名
     */
    const LOG_ITEM_ACTION_NAME = 'action';

    /**
     * エラーファイル名
     */
    const LOG_ITEM_ERR_FILE = 'errorFile';

    /**
     * エラー発生行番号
     */
    const LOG_ITEM_ERR_LINE = 'errorLine';

    /**
     * セッションID
     */
    const LOG_ITEM_SESSION_ID = 'sessionId';

    /**
     * 空値の場合の表記
     */
    const DEFAULT_FOR_EMPTY = '-';

    /**
     * ログ番号：デフォルト
     */
    const CODE_DEFAULT = 20000;

    /**
     * ログ番号：デバッグ情報
     */
    const CODE_DEBUG   = 20999;

    // ------------------------------------------------------------------ //

    /**
     * @var boolean
     */
    protected $_ignoreRobot = false;

    // ------------------------------------------------------------------ //

    /**
     * ロボットからのアクセスを記録しない
     *
     * @return void
     */
    public function ignoreRobot()
    {
        $this->_ignoreRobot = true;
    }

    // ------------------------------------------------------------------ //

    /**
     * ロボットからのアクセスを記録するかどうか判定
     *
     * @return void
     */
    public function isIgnoreRobot()
    {
        return $this->_ignoreRobot;
    }

    // ------------------------------------------------------------------ //

    /**
     * メッセージ部のフィルタリング
     *
     * @param  string フィルタ前
     * @return string フィルタ後
     */
    public static function messageFilter($message)
    {
        $newLineFilter = new Zend_Filter_StripNewlines(
            array('encoding' => 'UTF-8')
        );

        $filterChain = new Zend_Filter();
        $filterChain->addFilter($newLineFilter);

        return $filterChain->filter($message);
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセス元IPアドレスの取得
     *
     * @return string アクセス元IPアドレス
     */
    public static function getRemoteAddress()
    {
        $ipAddr = null;

        // PROXYを経由しているか
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            if (preg_match('/,/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipAddresses = preg_split('/\s*,\s*/', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ipAddr      = array_shift($ipAddresses);
            } else {
                $ipAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            if (self::isValidRemoteAddress($ipAddr)) {
                return $ipAddr;
            }
        }

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $ipAddr = $_SERVER['REMOTE_ADDR'];
            if (self::isValidRemoteAddress($ipAddr)) {
                return $ipAddr;
            }
        }

        return 'invalid remote address';
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセス元IPアドレスのチェック
     *
     * @param string $ipAddr IPアドレス
     * @return boolean 真：妥当なIPアドレス
     */
    public static function isValidRemoteAddress($ipAddr)
    {
        // IPアドレスのバリデータ
        $valids = array(
            'ip_addr' => array(
                App_Validate_Common::setIpAddrValidator(
                    '不正なIPアドレスです。'
                ),
            ),
        );
        $filters = array(
            '*' => App_Validate_Common::setParamDefaultFilter()
        );
        $input = App_Validate_Common::createDefaultFilterInput(
            $filters,
            $valids,
            array('ip_addr' => $ipAddr)
        );
        return $input->isValid();
    }

    // ------------------------------------------------------------------ //

    /**
     * ユーザエージェントの取得
     *
     * @return string ユーザエージェント
     */
    public static function getUserAgent()
    {
        $userAgent = null;
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        return $userAgent;
    }

    // ------------------------------------------------------------------ //

    /**
     * リファラーの取得
     *
     * @return string リファラー
     */
    public static function getHttpReferer()
    {
        $referer = null;
        if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            $referer = $_SERVER['HTTP_REFERER'];
        }

        return $referer;
    }

    // ------------------------------------------------------------------ //

    /**
     * ログに記録するユーザ識別子の取得
     *
     * @return string ログに記録するユーザ識別子
    */
    public static function getLoginUserId()
    {
        // 通常はログイン中ユーザの個人ID
        try {
            $userSession = App_Utils::getSession('user');
            $userId = $userSession->getUserId();
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            $userId = 'invalid session';
        }
        return $userId;
    }

    // ------------------------------------------------------------------ //

    /**
     * ログに記録するセッションIDの取得
     *
     * @return string ログに記録するセッションID
     */
    public static function getSessionId()
    {
        return Zend_Session::getId();
    }

    // ------------------------------------------------------------------ //

    /**
     * 空値の場合、代わりに出力する文字列をセット
     *
     * @param  mixed $value   空値チェック対象
     * @param  mixed $default 代わりに出力する文字列
     * @return mixed
     */
    public static function setDefaultValue($value, $default = self::DEFAULT_FOR_EMPTY)
    {
        if (is_null($value)) {
            return $default;
        }
        if (preg_match('/^$/', $value)) {
            return $default;
        }
        return $value;
    }

    // ------------------------------------------------------------------ //

    /**
     * ログへの書き込み
     *
     * Zend_Log::log()のタイムスタンプ出力フォーマットを変更する
     * ため、Zend_Log::log()をもとに改良。
     *
     * @param  string $message ログ出力メッセージ
     * @param  integer $priority  ログ出力プライオリティ
     * @return void
     * @throws App_Log_Exception
     */
    protected function _toCustomLog($message, $priority)
    {
        if (empty($this->_writers)) {
            throw new App_Log_Exception(
                'ログへの出力に失敗しました。'
            );
        }

        if (!isset($this->_priorities[$priority])) {
            throw new App_Log_Exception(
                '不正なログ出力プライオリティです。'
            );
        }

        $event = array_merge(
            array(
                self::LOG_ITEM_TIMESTAMP     => date('Y-m-d H:i:s'),
                self::LOG_ITEM_MESSAGE       => $message,
                self::LOG_ITEM_PRIORITY      => $priority,
                self::LOG_ITEM_PRIORITY_NAME => $this->_priorities[$priority]
            ),
            $this->_extras
        );

        foreach ($this->_filters as $filter) {
            if (! $filter->accept($event)) {
                return;
            }
        }

        foreach ($this->_writers as $writer) {
            $writer->write($event);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * ロボットからのアクセスかどうかの判定
     *
     * @return boolean 真：ロボットからのアクセスである
     */
    public static function isRobot($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = self::getUserAgent();
        }
        $robot = '/('
               . 'ICC-Crawler|Teoma|Y!J-BSC|Pluggd\/Nutch|psbot|CazoodleBot|'
               . 'Googlebot|Antenna|BlogPeople|AppleWebKitOpenbot|NaverBot|PlantyNet|livedoor|'
               . 'msnbot|FlashGet|WebBooster|MIDown|moget|InternetLinkAgent|Wget|InterGet|WebFetch|'
               . 'WebCrawler|ArchitextSpider|Scooter|WebAuto|InfoNaviRobot|httpdown|Inetdown|Slurp|'
               . 'Spider|^Iron33|^fetch|^PageDown|^BMChecker|^Jerky|^Nutscrape|Baiduspider|TMCrawler'
               .')/um';

        if (preg_match($robot, $userAgent) || ereg($robot, $userAgent)) {
            return true;
        }
        return false;
    }
}
