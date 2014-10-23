<?php
/**
 * library/App/Json.php
 *
 * JSON操作クラス
 *
 * @category    App
 * @package     Json
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Json.php 30 2014-03-03 09:45:09Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Zend_Json
 */

/**
 * App_Json クラス
 *
 * JSON操作クラス
 *
 * @category    App
 * @package     Json
 * @author      Mitsubishi Space Software Co.,Ltd.
 */
abstract class App_Json extends Zend_Json
{
    /**
     * JSON形式エンコード対象データ
     */
    private $_data;

    // ------------------------------------------------------------------ //

    /**
     * ステータス部
     */
    const JSON_ID_RESPONSE = 'response';

    // ------------------------------------------------------------------ //

    /**
     * コンストラクタ
     *
     * @param  array $data JSON形式で送信するデータ
     * @return void
     */
    public function __construct($data = array()) {
        $this->_data = $data;
    }

    // ------------------------------------------------------------------ //

    /**
     * JSON送信データの生成
     *
     * ステータス部とデータ部を生成する。
     *
     * @return $sendData JSON形式で送信するデータ
    */
    public function createSendJsonData()
    {
        $sendData = array(
           self::JSON_ID_RESPONSE => $this->_data
        );
        return $sendData;
    }

    // ------------------------------------------------------------------ //

    /**
     * 送信用JSONデータの取得
     *
     * @return string|false $sendDate 成功：JSON形式データ、失敗：false
    */
    public function getSendJsonData()
    {
        $sendData = $this->createSendJsonData();
        if (!empty($sendData)) {
            return self::encode($sendData);
        }
        return false;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモデルクラスを生成する
     *
     * @param  string $modelName モデル名
     * @return App_Json Jsonモデルクラス
     */
    public static function getJsonModelClass($modelName)
    {
        // モデル名からテーブルモデルクラスを生成する。
        $className     = self::getJsonModelClassName($modelName);
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からJsonクラス名を取得する
     *
     * @param  string $dataName モデル名
     * @return string Jsonクラス名
     */
    public static function getJsonModelClassName($dataName)
    {
        $className = 'Model_Json_'
                   . ucfirst(App_Utils::dashToCamelCase($dataName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールモデルクラスを生成する
     *
     * @param  string $moduleName モジュール名
     * @param  string $modelName モデル名
     * @return App_Jsonl JSONモデルクラス
     */
    public static function getJsonModuleModelClass($moduleName, $modelName)
    {
        // モデル名からモジュールモデルクラスを生成する。
        $className     = self::getJsonModuleModelClassName($moduleName, $modelName);
        $reflectionObj = new ReflectionClass($className);
        return $reflectionObj->newInstance();
    }

    // ------------------------------------------------------------------ //

    /**
     * モデル名からモジュールXMLクラス名を取得する
     *
     * @param  string $moduleName モジュール名
     * @param  string $dataName モデル名
     * @return string JSONクラス名
     */
    public static function getJsonModuleModelClassName($moduleName, $dataName)
    {
        $className = ucfirst(strtolower($moduleName))
                   . '_Model_Json_'
                   . ucfirst(App_Utils::dashToCamelCase($dataName));

        return $className;
    }

    // ------------------------------------------------------------------ //

    /**
     * 通知JSONの生成
     *
     * @param string $message 通知メッセージ
     * @return string JSON文字列
     */
    public static function notifyJson($message)
    {
        $response = array(
            'response' => $message
        );
        return App_Json::encode($response);
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーJSONの生成
     *
     * @param string $message エラーメッセージ
     * @return string JSON文字列
     */
    public static function errorJson($message)
    {
        $response = array(
            'error' => $message
        );
        return App_Json::encode($response);
    }
}
