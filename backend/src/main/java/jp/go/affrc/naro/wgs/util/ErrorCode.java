package jp.go.affrc.naro.wgs.util;

import org.seasar.struts.util.MessageResourcesUtil;

/**
 * エラーコード 列挙型クラス。
 *
 *  10000～10999 ... WebAPI 通常ログ
 *      10999 ... デバッグログ
 *  20000～20999 ... Webアプリ 通常ログ
 *      20999 ... デバッグログ
 *  90000～90999 ... WebAPI アプリケーションエラーログ
 *      90200～90299 ... I/Oエラーログ
 *      90300～90399 ... DBエラーログ
 *      90400～90499 ... バリデーションエラーログ
 *      90500～90599 ... Generatorライブラリエラーログ
 *  91000～91999 ... Webアプリ アプリケーションエラーログ
 *      91200～91299 ... I/Oエラーログ
 *      91300～91399 ... DBエラーログ
 *      91400～91499 ... バリデーションエラーログ
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public enum ErrorCode {

    // WebAPI 通常ログ
    /** リクエスト受付け成功 */
    REQUEST_NORMAL_END("10000", Priority.INFO),

    /** データ生成成功 */
    CREATE_NORMAL_END("10101", Priority.INFO),

    /** データ生成タスク処理開始 */
    START_TASK("10102", Priority.INFO),

    /** データ生成タスク処理終了 */
    END_TASK("10103", Priority.INFO),

    /** 実行待ちのリクエスト情報なし */
    ACCEPT_REQUEST_NOT_FOUND("10104", Priority.INFO),

    /** データ生成ライブラリ処理開始 */
    START_LIB("10105", Priority.INFO),

    /** データ生成ライブラリ処理終了 */
    END_LIB("10106", Priority.INFO),

    /** プロセス終了 */
    PROCESS_EXIT_VALUE("10107", Priority.INFO),

    /** デバッグログ */
    DEBUG("10999", Priority.DEBUG),

    // WebAPI アプリケーションエラー
    /** タスク追加エラー(タスク追加はできなかったが、現在実行中のタスクが追加できなかったタスクのリクエスト処理を行う。) */
    ADD_TASK_ERROR("90000", Priority.WARN),

    /** 気象データエラー */
    WEATHER_DATA_ERROR("90001", Priority.ERR),

    // I/Oエラー
    /** ローカルファイルアクセスエラー */
    LOCAL_FILE_ACCESS_ERROR("90200", Priority.ERR),

    /** ローカルファイルクローズエラー */
    LOCAL_FILE_CLOSE_ERROR("90201", Priority.WARN),

    /** HDFSファイルアクセスエラー */
    HDFS_ACCESS_ERROR("90202", Priority.ERR),

    /** HDFSファイルクローズエラー */
    HDFS_CLOSE_ERROR("90203", Priority.WARN),

    /** ローカルファイル未作成エラー */
    LOCAL_FILE_NOT_FOUND("90204", Priority.WARN),

    /** 一時結果データファイル削除エラー */
    TEMP_FILE_DELETE_ERROR("90205", Priority.WARN),

    /** ステーションデータリスト取得エラー */
    STATION_LIST_FILE_NOT_FOUND("90206", Priority.ERR),

    /** 気象データ取得エラー */
    WEATHER_DATA_FILE_NOT_FOUND("90207", Priority.WARN),

    // DBエラー
    /** DBアクセスエラー */
    DB_ACCESS_ERROR("90300", Priority.ERR),

    /** 実行ログのDB登録エラー */
    EXECUTE_LOG_ERROR("90399", Priority.ERR),

    // バリデーションエラー
    /** リクエスト情報なし */
    REQUEST_NOT_FOUND("90400", Priority.WARN),

    /** 利用可能ジェネレータ情報なし */
    GENERATOR_NOT_FOUND("90401", Priority.WARN),

    /** 利用可能ライブラリ情報なし */
    LIB_NOT_FOUND("90402", Priority.WARN),

    /** 日付変換エラー */
    PARSE_DATE_ERROR("90403", Priority.WARN),

    /** リージョン取得エラー */
    REGION_NOT_FOUND("90404", Priority.ERR),

    /** ステーション取得エラー */
    STATION_NOT_FOUND("90405", Priority.ERR),

    /** サブエレメント取得エラー */
    SUBELEMENT_NOT_FOUND("90406", Priority.ERR),

    /** 地点種別不正エラー */
    RANGE_TYPE_ERROR("90407", Priority.ERR),

    /** 観測年不正エラーエラー */
    DATA_YEAR_ERROR("90408", Priority.ERR),

    // Generatorライブラリエラー
    /** データ生成失敗 */
    GENERATOR_EXEC_ERROR("90500", Priority.ERR),

    /** 処理可能なライブラリ情報なし */
    WGS_LIB_NOT_FOUND("90501", Priority.ERR),

    /** プロセス実行エラー */
    PROCESS_EXECUTE_ERROR("90502", Priority.ERR),

    /** プロセス強制終了 */
    KILL_PROCESS("90503", Priority.ERR),

    // その他のエラー
    /** 予期しない実行エラー */
    SYSTEM_ERROR("90999", Priority.ERR);

    /** エラーコード */
    private String errorCode = "0";

    /** ログプライオリティ */
    private Priority errorPriority = null;

    /**
     * コンストラクタ。
     * @param code エラーコード
     * @param priority ログプライオリティ
     */
    private ErrorCode(String code, Priority priority) {
        this.errorCode = code;
        this.errorPriority = priority;
    }

    /**
     * エラーコードを取得する。
     * @return エラーコード
     */
    public String getErrorCode() {
        return this.errorCode;
    }

    /**
     * エラーメッセージを取得する。
     * @param args メッセージ置換文字列
     * @return エラーメッセージ
     */
    public String getErrorMess(Object... args) {
        return MessageResourcesUtil.getMessage("execute_log." + this.errorCode, args);
    }

    /**
     * ログプライオリティを取得する。
     * @return ログプライオリティ
     */
    public Priority getPriority() {
        return errorPriority;
    }
}
