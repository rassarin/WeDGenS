<?php
/**
 * library/App/Utils.php
 *
 * 共通関数定義クラス。
 *
 * @category    App
 * @package     Utils
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Utils.php 38 2014-03-06 06:38:16Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Utils クラス
 *
 * 共通関数定義クラス。
 *
 * @category    App
 * @package     Utils
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Utils
{
    // ---------------------------------------------------------------------- //

    /**
     * キャッシュ有効期限
     */
    const CONFIG_CACHE_LIFETIME = 7200;

    /**
     * 初期パスワードの長さ
     */
    const PASSWORD_LEN = 8;

    // ---------------------------------------------------------------------- //

    /**
     * データベースリソースの取得
     *
     * @return Zend_Application_Resource_ResourceAbstract データベースリソース
    */
    public static function getDbResource()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bootstrap = $frontController->getParam('bootstrap');
        $resource  = $bootstrap->getPluginResource('multidb');
        return $resource;
    }

    // ------------------------------------------------------------------ //

    /**
     * 設定ファイルの読み込み
     *
     * @param string $configFile 設定ファイルパス
     * @param string $section セクション
     * @return Zend_Config
     */
    public static function loadConfig($configFile, $section = null)
    {
        if (!file_exists($configFile)) {
            throw new App_Exception(
                '設定ファイルの読み込みに失敗しました。',
                App_Exception::APP_IO_ERROR
            );
        }
        return new Zend_Config_Ini(
            $configFile,
            $section
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * 設定ファイル用キャッシュの取得
     *
     * @param string $configFile 設定ファイルパス
     * @param integer $lifeTime キャッシュ有効期間
     * @return Zend_Cache
     */
    public static function getConfigCache($configFile, $lifeTime = self::CONFIG_CACHE_LIFETIME)
    {
        $frontendOptions = array(
            'lifetime'                => $lifeTime,
            'automatic_serialization' => true,
            'master_file'             => $configFile,
        );
        $backendOptions = array(
            'cache_dir' => APP_ROOT_PATH . "/var/cache/config"
        );
        $cache = Zend_Cache::factory(
            'File',
            'File',
            $frontendOptions,
            $backendOptions
        );

        return $cache;
    }

    // ------------------------------------------------------------------ //

    /**
     * Zend_Dateインスタンスの取得
     *
     * @return Zend_Date
     */
    public static function getZendDate()
    {
        self::_initDateSetting();
        Zend_Date::setOptions(array('format_type' => 'php'));
        return new Zend_Date();
    }

    // ------------------------------------------------------------------ //

    /**
     * 指定日時、指定フォーマットでのZend_Dateインスタンスの取得
     *
     * @param string $date 日時
     * @param string $format フォーマット
     * @param string $locale ロケール
     * @return Zend_Date
     */
    public static function setZendDate(
        $date, $format = null, $locale = App_Const::DEFAULT_LOCALE
    ) {
        self::_initDateSetting();
        Zend_Date::setOptions(array('format_type' => 'php'));
        return new Zend_Date($date, $format, $locale);
    }

    // ------------------------------------------------------------------ //

    /**
     * 現在の会計年度の取得
     *
     * @return integer 会計年度(YYYY)
     */
    public static function getCurrentNendo()
    {
        $date  = self::getZendDate();
        $month = $date->get(Zend_Date::MONTH);

        if (($month >= 1) && ($month <= 3)) {
           $date->sub(1, Zend_Date::YEAR);
        }

        return $date->get(Zend_Date::YEAR);
    }

    // ------------------------------------------------------------------ //

    /**
     * 現在の年の取得
     *
     * @return integer 年(YYYY)
     */
    public static function getCurrentYear()
    {
        $date  = self::getZendDate();
        return $date->toString('Y');
    }

    // ------------------------------------------------------------------ //

    /**
     * 現在の日時を取得
     *
     * @return stirng YYYY-MM-DD hh:mm:ss
     */
    public static function getCurrentDate()
    {
        $date  = self::getZendDate();
        return $date->toString('Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------ //

    /**
     * 今日の日付を取得
     *
     * @return stirng YYYY-MM-DD
     */
    public static function getToday()
    {
        $date  = self::getZendDate();
        return $date->toString('Y-m-d');
    }

    // ------------------------------------------------------------------ //

    /**
     * $timeStamp1が$timeStamp2より新しいかどうか比較
     *
     * @return boolean 真：$timeStamp1が$timeStamp2より新しい
     */
    public static function isLater($timeStamp1, $timeStamp2)
    {
        $date1   = self::setZendDate($timeStamp1);
        $date2   = self::setZendDate($timeStamp2);
        $compare = $date1->compare($date2);
        if ($compare > 0) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * 現在時点からnか月後の日時を取得
     *
     * @param integer $n nか月
     * @return Zend_Date
     */
    public static function selectedMonthLaterDate($n)
    {
        $currentDate = self::getZendDate();
        $currentDate->add($n, Zend_Date::MONTH);

        return $currentDate->toString('Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------ //

    /**
     * 現在時点からnか月前の日時を取得
     *
     * @param integer $n nか月
     * @return Zend_Date
     */
    public static function selectedMonthAgoDate($n)
    {
        $currentDate = self::getZendDate();
        $currentDate->sub($n, Zend_Date::MONTH);

        return $currentDate->toString('Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------ //

    /**
     * 指定日付からn日後の日時を取得
     *
     * @param string $date YYYY-MM-DD
     * @param integer $n n日
     * @return stirng YYYY-MM-DD hh:mm:ss
     */
    public static function selectedDayLaterDate($date, $n = 1)
    {
        $date = self::setZendDate($date);
        $date->add($n, Zend_Date::DAY_SHORT);

        return $date->toString('Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------ //

    /**
     * 指定日付からn日前の日時を取得
     *
     * @param string $date YYYY-MM-DD
     * @param integer $n n日
     * @return stirng YYYY-MM-DD hh:mm:ss
     */
    public static function selectedDayBeforeDate($date, $n = 7)
    {
        $date = self::setZendDate($date);
        $date->sub($n, Zend_Date::DAY_SHORT);

        return $date->toString('Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------ //

    /**
     * 今日から１か月前の日付を取得
     *
     * @return stirng YYYY-MM-DD
     */
    public static function getOneMonthAgo()
    {
        $currentDate = self::getZendDate();
        $currentDate->sub(1, Zend_Date::MONTH);

        return $currentDate->toString('Y-m-d');
    }

    // ------------------------------------------------------------------ //

    /**
     * 今日からn日前の日付を取得
     *
     * @param integer $n n日
     * @return stirng YYYY-MM-DD
     */
    public static function getSpecifiedDaysAgo($n)
    {
        $currentDate = self::getZendDate();
        $currentDate->sub($n, Zend_Date::DAY);

        return $currentDate->toString('Y-m-d');
    }

    // ------------------------------------------------------------------ //

    /**
     * 指定日付の月の日数の取得
     *
     * @param string $date YYYY-MM-DD
     * @return integer 指定日付の月の日数
     */
    public static function getDaysOfMonth($date)
    {
        $date = self::setZendDate($date);
        return $date->get(Zend_Date::MONTH_DAYS);
    }

    // ------------------------------------------------------------------ //

    /**
     * 年開始日時を取得
     *
     * @param integer $nendo 年度
     * @return string 年開始日時(YYYY-MM-DD hh:mm:ss)
    */
    public static function getYearBeginDate($year)
    {
        return $year . '-01-01 00:00:00';
    }

    // ------------------------------------------------------------------ //

    /**
     * 年終了日時を取得
     *
     * @param integer $nendo 年度
     * @return string 年度終了日時(YYYY-MM-DD hh:mm:ss)
    */
    public static function getYearEndDate($year)
    {
        $year = $year + 1;
        return $year . '-01-01 00:00:00';
    }

    // ------------------------------------------------------------------ //

    /**
     * 年度開始日時を取得
     *
     * @param integer $nendo 年度
     * @return string 年度開始日時(YYYY-MM-DD hh:mm:ss)
    */
    public static function getNendoBeginDate($nendo)
    {
        return $nendo . '-04-01 00:00:00';
    }

    // ------------------------------------------------------------------ //

    /**
     * 年度終了日時を取得
     *
     * @param integer $nendo 年度
     * @return string 年度終了日時(YYYY-MM-DD hh:mm:ss)
    */
    public static function getNendoEndDate($nendo)
    {
        $nendo = $nendo + 1;
        return $nendo . '-04-01 00:00:00';
    }

    // ------------------------------------------------------------------ //

    /**
     * 年度終了日時を取得
     *
     * @param integer $nendo 年度
     * @return string 年度終了日時(YYYY-MM-DD hh:mm:ss)
    */
    public static function getNendoEndDate2($nendo)
    {
        $nendo = $nendo + 1;
        return $nendo . '-03-31 00:00:00';
    }

    // ------------------------------------------------------------------ //

    /**
     * foo-bar -> FooBar 変換
     *
     * @param string $value 変換する文字列
     * @return string 変換後の文字列
    */
    public static function dashToCamelCase($value)
    {
        $inflector = new Zend_Filter_Inflector(':value');
        $inflector->setRules(
            array(
                ':value' => array('Word_DashToCamelCase'),
            )
        );
        return $inflector->filter(
            array(
                'value' => $value,
            )
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * FOO_BAR -> FooBar 変換
     *
     * @param string $value 変換する文字列
     * @return string 変換後の文字列
    */
    public static function underscoreToCamelCase($value)
    {
        $inflector = new Zend_Filter_Inflector(':value');
        $inflector->setRules(
            array(
                ':value' => array(
                    'StringToLower',
                    'Word_UnderscoreToCamelCase'
                )
            )
        );
        return $inflector->filter(
            array(
                'value' => $value,
            )
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * FooBar -> foo_bar 変換
     *
     * @param string $value 変換する文字列
     * @return string 変換後の文字列
    */
    public static function camelCaseToUnderscore($value)
    {
        $inflector = new Zend_Filter_Inflector(':value');
        $inflector->setRules(
            array(
                ':value' => array(
                    'Word_CamelCaseToUnderscore',
                    'StringToLower'
                )
            )
        );
        return $inflector->filter(
            array(
                'value' => $value,
            )
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * FooBar -> Foo_Bar 変換
     *
     * @param string $value 変換する文字列
     * @return string 変換後の文字列
    */
    public static function camelCaseToClassName($value)
    {
        $inflector = new Zend_Filter_Inflector(':value');
        $inflector->setRules(
            array(
                ':value' => array(
                    'Word_CamelCaseToUnderscore',
                )
            )
        );
        return $inflector->filter(
            array(
                'value' => $value,
            )
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * UTF-8 Byte Order Markの除去
     *
     * @param string $str 対象文字列
     * @return string 除去後文字列
     */
    public static function deleteBOM($str)
    {
        if (is_null($str)) {
            return $str;
        }
        if (!isset($str)) {
            return $str;
        }
        if (!is_string($str)) {
            return $str;
        }
        if (App_Utils::isEmpty($str)) {
            return $str;
        }
        if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
            $str = substr($str, 3);
        }
        return $str;
    }

    // ------------------------------------------------------------------ //

    /**
     * 一意IDの生成
     *
     * @return string 一意ID
     */
    public static function generateUniqueId()
    {
        return sha1(microtime() . mt_rand());
    }

    // ------------------------------------------------------------------ //

    /**
     * アクセスキーの生成
     *
     * @param string $mailAddress メールアドレス
     * @param string $date 日時(YYYY-MM-DD hh:mm:ss)
     * @return string アクセスキー
     */
    public static function genAccessKey($mailAddress, $date = null)
    {
        $pieces = array();
        if (App_Utils::isEmpty($date)) {
            $date = App_Utils::getCurrentDate();
        }

        $pieces[] = hash('md5', $mailAddress);
        $pieces[] = hash('md5', $date);
        $pieces[] = self::generateUniqueId();

        return implode('-', $pieces);
    }

    // ------------------------------------------------------------------ //

    /**
     * 日時設定の初期化
     *
     * @return void
     */
    private static function _initDateSetting()
    {
        date_default_timezone_set(App_Const::TIME_ZONE);
    }

    // ------------------------------------------------------------------ //

    /**
     * エスケープ処理
     *
     * @param string $str 対象文字列
     * @return string エスケープ後文字列
     */
    public static function escape($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // ------------------------------------------------------------------ //

    /**
     * 空値チェック
     *
     * @param mixed $value チェック対象変数
     * @return  boolean TRUE:空値である
     */
    public static function isEmpty($value)
    {
        if (is_array($value)) {
            if (empty($value)) {
                return true;
            }
        } else {
            if (is_null($value)) {
                return true;
            }
            if (preg_match('/^$/', $value)) {
                return true;
            }
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * UTF-8文字列を1文字ずつ分割
     *
     * @param string $string UTF-8文字列
     * @return array 分割した文字列
     */
    public static function mbStringToArray($string)
    {
        $splited = array();
        while ($strLength = mb_strlen($string, 'UTF-8')) {
            array_push($splited, mb_substr($string, 0, 1, 'UTF-8'));
            $string = mb_substr($string, 1, $strLength, 'UTF-8');
        }
        return $splited;
    }

    // ------------------------------------------------------------------ //

    /**
     * UTF-8文字列の文字数を取得
     *
     * @param string $string UTF-8文字列
     * @return array 文字数
     */
    public static function mbStringLength($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    // ------------------------------------------------------------------ //

    /**
     * UTF-8->SJIS-win、改行除去
     *
     * @param stirng $string 変換前文字列
     * @return string 変換後文字列
     */
    public static function utf2sjiswin($string)
    {
        $string = preg_replace("/(\r\n|\r|\n)/u", '', $string);
        return mb_convert_encoding($string, "SJIS-win", "UTF-8");
    }

    // ------------------------------------------------------------------ //

    /**
     * SJIS-win->UTF-8、改行除去
     *
     * @param stirng $string 変換前文字列
     * @return string 変換後文字列
     */
    public static function sjiswin2utf8($string)
    {
        return preg_replace(
            "/(\r\n|\r|\n)/u",
            '',
            mb_convert_encoding($string, "UTF-8", "SJIS-win")
        );
    }

    // ------------------------------------------------------------------ //

    /**
     * 「コンポーネント名_クラス名」クラスのインスタンス取得
     *
     * @see Zend_Controller_Action_Helper_Abstract::direct()
     * @param string $prefix コンポーネント名
     * @param string $name クラス名
     * @return object
     */
    public static function getReflectionClass($prefix, $name)
    {
        $className = ucfirst($prefix) . '_'
                   . ucfirst(App_Utils::camelCaseToClassName($name));
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * 「Service_サービス名」クラスのインスタンス取得
     *
     * @param string $name サービス名
     * @return App_Service インスタンス
     */
    public static function getService($name)
    {
        return self::getReflectionClass('service', $name);
    }

    // ------------------------------------------------------------------ //

    /**
     * 「モジュール名_Service_サービス名」クラスのインスタンス取得
     *
     * @param string $name サービス名
     * @param string $module モジュール名
     * @return App_Service インスタンス
     */
    public static function getModuleService($name, $module)
    {
        $prefix   = ucfirst(strtolower($module)) . '_Service';
        $instance = self::getReflectionClass($prefix, $name);
        $instance->setModuleName($module);
        return $instance;
    }

    // ------------------------------------------------------------------ //

    /**
     * 「Form_フォーム名」クラスのインスタンス取得
     *
     * @see Zend_Controller_Action_Helper_Abstract::direct()
     * @param string $name フォーム名
     * @return object
     */
    public static function getForm($name, $module)
    {
        if ($module == 'default') {
            return self::getReflectionClass('form', $name);
        } else {
            return self::getModuleForm($name, $module);
        }
    }

    // ------------------------------------------------------------------ //

    /**
     * 「モジュール名_Form_フォーム名」クラスのインスタンス取得
     *
     * @param string $name フォーム名
     * @param string $module モジュール名
     * @return App_Form インスタンス
     */
    public static function getModuleForm($name, $module)
    {
        $prefix   = ucfirst(strtolower($module)) . '_Form';
        $instance = self::getReflectionClass($prefix, $name);
        $instance->setModuleName($module);
        return $instance;
    }

    // ------------------------------------------------------------------ //

    /**
     * 「App_Session_Namespace_Appセッション名」クラスのインスタンス取得
     *
     * @param string $name セッション名
     * @return App_Session_Namespace インスタンス
     */
    public static function getSession($name)
    {
        $className = 'App_Session_Namespace_App' . ucfirst(strtolower($name));
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * ランダムなパスワードを生成する。
     *
     * @return string
     */
    public static function genPassword(){
        $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
        mt_srand();
        $passwd = '';
        for($i = 0; $i < self::PASSWORD_LEN; $i++) {
            $passwd .= $charList[mt_rand(0, strlen($charList) - 1)];
        }
        return $passwd;
    }

    // ------------------------------------------------------------------ //

    /**
     * error_log()を使用してデバッグ出力する。
     *
     * @param mixed $data 出力データ
     * @param string $label ラベル
     * @param boolean $echo 真：標準出力に出力する
     * @return void
     */
    public static function dump($data, $label = null, $echo = false)
    {
        error_log(Zend_Debug::dump($data, $label, $echo));
    }

    // ------------------------------------------------------------------ //

    /**
     * UUIDの生成
     *
     * @return string UUID
     */
    public static function generateUUID()
    {
        $uuid = null;
        try {
            $db   = Zend_Db_Table::getDefaultAdapter();
            $uuid = $db->fetchOne('select uuid_generate_v1mc()');
        } catch (Exception $exception) {
            return null;
        }
        return $uuid;
    }
}
