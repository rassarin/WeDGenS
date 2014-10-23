package jp.go.affrc.naro.wgs.util;

import java.util.UUID;

/**
 * リクエストIDユーティリティクラス。
 * 本来PostgreSQLの関数で生成するため、検証用のデータ生成以外では利用しない。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class RequestIdUtils {

    /** サイトID */
    private static final String SITE_ID = "001";

    /** サービス名 */
    private static final String SERVICE_NAME = "wgs";

    /**  */
    private static final String NAME = SERVICE_NAME + SITE_ID;

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private RequestIdUtils() {
    }

    /**
     * リクエストID生成。
     * サイト名、サービス名、システム時間からリクエストIDをUUIDで作成する。
     * @return リクエストID
     */
    public static String genRequestId() {
        String uuidName = NAME + System.currentTimeMillis();
        UUID requestId = UUID.nameUUIDFromBytes(uuidName.getBytes());
        return requestId.toString();
    }

}
