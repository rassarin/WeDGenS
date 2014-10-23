<?php
/**
 * applicaiton/modules/api/services/Xml.php
 *
 * XMLレスポンスサービスクラス
 *
 * XMLレスポンスサービス。
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Xml.php 91 2014-03-26 04:31:05Z nobu $
 * @since       File available since Release 1.0.0
 * @see         Service_Search
*/

/**
 * Api_Service_Xml クラス
 *
 * XMLレスポンスサービスクラス
 *
 * @category    Api
 * @package     Service
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class Api_Service_Xml extends Service_Api
{
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
     * 通知XMLの生成。
     *
     * @param string $message エラーメッセージ
     * @return string XML文字列
     */
    public function notifyResponse($message)
    {
        $dom = $this->getXmlModuleModel('notification')
                    ->notifyXml($message);

        return $dom->saveXML();
    }

    // ------------------------------------------------------------------ //

    /**
     * エラーレスポンスXMLの生成。
     *
     * @param string $message エラーメッセージ
     * @return string XML文字列
     */
    public function errorResponse($message)
    {
        $dom = $this->getXmlModuleModel('notification')
                    ->errorXml($message);

        return $dom->saveXML();
    }

    // ------------------------------------------------------------------ //

    /**
     * 成功レスポンスXMLの生成。
     *
     * @return string XML文字列
     */
    public function successResponse()
    {
        return $this->notifyResponse('SUCCESS');
    }

    // ------------------------------------------------------------------ //

    /**
     * 失敗レスポンスXMLの生成。
     *
     * @return string XML文字列
     */
    public function failureResponse()
    {
        return $this->notifyResponse('FAILURE');
    }

    // ------------------------------------------------------------------ //

    /**
     * 成功レスポンスXMLの生成。
     *
     * @param mixed $results 結果
     * @param string $controller 実行コントローラ名
     * @return string XML文字列
     */
    public function formatResponse($results, $controller)
    {
        $dom = $this->getXmlModuleModel($controller)
                    ->formatToXml($results);

        $verified  = $this->validateResponseXml($dom);
        if (!$verified) {
            $lastError = error_get_last();
            $message   = 'XMLスキーマのチェックで不正と判定されました。';
            if (!App_Utils::isEmpty($lastError)) {
                $message .= '：' . $lastError['message'];
            }
            throw new App_Exception_Xml(
                $message,
                App_Exception::APP_VALIDATE_ERROR
            );
        }

        return $dom->saveXML();
    }
}

