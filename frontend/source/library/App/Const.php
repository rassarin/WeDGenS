<?php
/**
 * library/App/Const.php
 *
 * 共通定数定義クラス。
 *
 * @category    App
 * @package     Constant
 * @author      Mitsubishi Space Software Co.,Ltd.
 * @version     SVN: $Id: Const.php 83 2014-03-18 12:49:18Z nobu $
 * @since       File available since Release 1.0.0
*/

/**
 * App_Const クラス
 *
 * 共通定数定義クラス。
 *
 * @category    App
 * @package     Constant
 * @author      Mitsubishi Space Software Co.,Ltd.
*/
class App_Const
{
    // ------------------------------------------------------------------ //

    /**
     * ステータス値：有効
     */
    const STAT_UNKNOWN = 0;

    /**
     * ステータス値：有効
     */
    const STAT_ENABLE  = 1;

    /**
     * ステータス値：無効
     */
    const STAT_DISABLE = 2;

    /**
     * ステータス値：保護
     */
    const STAT_LOCKED  = 3;

    /**
     * ステータス値：削除
     */
    const STAT_DELETE  = 4;

    // ------------------------------------------------------------------ //

    /**
     * タイムゾーン
     */
    const TIME_ZONE = 'Asia/Tokyo';

    /**
     * ロケール
     */
    const DEFAULT_LOCALE = 'ja';

    /**
     * 整数上限値(INT4 = 2147483647 + 1 =  2147483648)
     */
    const INT_LIMIT = 2147483648;

    // ------------------------------------------------------------------ //

    /**
     * コンテンツタイプ:JSON
     */
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * コンテンツタイプ:XML
     */
    const CONTENT_TYPE_XML  = 'application/xml';

    /**
     * コンテンツタイプ:HTML
     */
    const CONTENT_TYPE_HTML = 'text/html';

    /**
     * コンテンツタイプ:JavaScript
     */
    const CONTENT_TYPE_JS   = 'text/javascript';

    /**
     * コンテンツタイプ:プレーンテキスト
     */
    const CONTENT_TYPE_TEXT = 'text/plain';

    /**
     * コンテンツタイプ:CSV
     */
    const CONTENT_TYPE_CSV = 'text/csv';

    /**
     * コンテンツタイプ:Zip
     */
    const CONTENT_TYPE_ZIP = 'application/zip';

    /**
     * コンテンツタイプ:application/octet-stream
     */
    const CONTENT_TYPE_OCTET_STREAM = 'application/octet-stream';

    // ------------------------------------------------------------------ //

    /**
     * APIレスポンス形式:JSON
     */
    const RESPONSE_FORMAT_JSON = 'json';

    /**
     * APIレスポンス形式:XML
     */
    const RESPONSE_FORMAT_XML  = 'xml';

    // ------------------------------------------------------------------ //

    /**
     * XML/JSONレスポンス用ステータスコード：成功
     */
    const SUCCESS_CODE = 'SUCCESS';

    /**
     * XML/JSONレスポンス用ステータスコード：失敗
     */
    const FAILURE_CODE = 'FAILURE';

    // ------------------------------------------------------------------ //

    /**
     * データ種別：ユーザ投入データ
     */
    const DATA_TYPE_USER_DATA  = 1;

    /**
     * データ種別：MetBrokerデータ
     */
    const DATA_TYPE_MET_BROKER = 2;

    // ------------------------------------------------------------------ //

    /**
     * 地点選択範囲種別：地点
     */
    const RANGE_TYPE_POINT = 'point';

    /**
     * 地点選択範囲種別：エリア
     */
    const RANGE_TYPE_AREA = 'area';

    /**
     * 地点選択範囲種別：メッシュ
     */
    const RANGE_TYPE_MESH = 'mesh';

    /**
     * 地点選択範囲種別：ユーザ登録データ
     */
    const RANGE_TYPE_USER = 'user';

    // ------------------------------------------------------------------ //

    /**
     * リクエストステータス：リクエスト受付
     */
    const REQUEST_STAT_ACCEPT = 'ACCEPT';

    /**
     * リクエストステータス：実行待ち
     */
    const REQUEST_STAT_WAITING = 'WAITING';

    /**
     * リクエストステータス：実行中
     */
    const REQUEST_STAT_RUNNING = 'RUNNING';

    /**
     * リクエストステータス：実行完了
     */
    const REQUEST_STAT_FINISHED = 'FINISHED';

    /**
     * リクエストステータス：実行キャンセル
     */
    const REQUEST_STAT_CANCEL = 'CANCEL';

    /**
     * リクエストステータス：強制終了
     */
    const REQUEST_STAT_TERMINATED = 'TERMINATED';

    /**
     * リクエストステータス：エラー
     */
    const REQUEST_STAT_ERROR = 'ERROR';

    // ------------------------------------------------------------------ //

    /**
     * ジェネレータステータス：利用可能
     */
    const GENERATOR_STAT_AVAILABLE = 'AVAILABLE';

    /**
     * ジェネレータステータス：利用不能
     */
    const GENERATOR_STAT_UNAVAILABLE = 'UNAVAILABLE';

    // ------------------------------------------------------------------ //

    /**
     * 出力形式:HTML
     */
    const RESULT_FORMAT_HTML = 'html';

    /**
     * 出力形式:XML
     */
    const RESULT_FORMAT_XML  = 'xml';

    /**
     * 出力形式:CSV
     */
    const RESULT_FORMAT_CSV  = 'csv';

    /**
     * 出力形式:グラフ
     */
    const RESULT_FORMAT_CHART  = 'chart';

    /**
     * 出力形式:ZIP
     */
    const RESULT_FORMAT_ZIP  = 'zip';

    // ------------------------------------------------------------------ //

    /**
     * ライブラリ種別:CLIGEN
     */
    const LIBRARY_TYPE_CLIGEN = 1;

    /**
     * ライブラリ種別:cdfdm
     */
    const LIBRARY_TYPE_CDFDM  = 2;

    // ------------------------------------------------------------------ //

    /**
     * リージョンID:リージョン定義なし
     */
    const REGION_NONE = 'none';

    // ------------------------------------------------------------------ //

    /**
     * デュレーション種別:日別
     */
    const DURATION_TYPE_DAILY = 'daily';

    /**
     * デュレーション種別:時別
     */
    const DURATION_TYPE_HOURLY = 'hourly';
}
