package jp.go.affrc.naro.wgs.util;

/**
 * ログプライオリティ 列挙型クラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public enum Priority {

    /** EMERG (Emergency: system is unusable) */
    EMERG (0, "Emergency"),

    /** ALERT (Alert: action must be taken immediately) */
    ALERT(1, "Alert"),

    /** CRIT (Critical: critical conditions) */
    CRIT(2, "Critical"),

    /** ERR (Error: error conditions) */
    ERR(3, "Error"),

    /** WARN (Warning: warning conditions) */
    WARN(4, "Warning"),

    /** NOTICE (Notice: normal but significant condition) */
    NOTICE(5, "Notice"),

    /** INFO (Informational: informational messages) */
    INFO(6, "Informational"),

    /** DEBUG (Debug: debug messages) */
    DEBUG(7, "Debug");

    /** ログプライオリティコード */
    private int priorityCode = 0;

    /** ログプライオリティ名 */
    private String priorityName = null;

    /**
     * コンストラクタ。
     * @param code ログプライオリティコード
     * @param name ログプライオリティ名
     */
    private Priority(int code, String name) {
        this.priorityCode = code;
        this.priorityName = name;
    }

    /**
     * ログプライオリティコードを取得する。
     * @return ログプライオリティコード
     */
    public int getCode() {
        return this.priorityCode;
    }

    /**
     * ログプライオリティ名を取得する。
     * @return ログプライオリティ名
     */
    public String getName() {
        return priorityName;
    }
}
