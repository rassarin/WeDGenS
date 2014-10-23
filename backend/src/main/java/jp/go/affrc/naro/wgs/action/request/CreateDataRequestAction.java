package jp.go.affrc.naro.wgs.action.request;

import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import javax.annotation.Resource;

import jp.go.affrc.naro.wgs.entity.common.TRequest;
import jp.go.affrc.naro.wgs.form.request.CreateDataRequestForm;
import jp.go.affrc.naro.wgs.util.RequestIdUtils;
import net.arnx.jsonic.JSON;

import org.seasar.extension.jdbc.JdbcManager;
import org.seasar.framework.log.Logger;
import org.seasar.struts.annotation.ActionForm;
import org.seasar.struts.annotation.Execute;

/**
 * CreateDataRequestActionクラス。
 * TODO:データ生成動作確認用のデータ生成要求アクションクラス。動作確認用のため後で削除する。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestAction.java 1267 2014-02-13 07:58:20Z watabe $
 */
public class CreateDataRequestAction {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(CreateDataRequestAction.class);

    /** CligenのジェネレータID */
    private static final int CLIGEN_GENERATOR_ID = 1;

    /** CdfdmのジェネレータID */
    private static final int CDFDM_GENERATOR_ID = 2;

    /** アクションフォーム */
    @Resource
    @ActionForm
    protected CreateDataRequestForm createDataRequestForm;

    /** JDBCマネージャ */
    @Resource
    protected JdbcManager jdbcManager;

    // ---------- Cligen、Cdfdm共通のリクエスト生成処理 ----------
    /**
     * リクエストのポイント情報を生成するテスト用のメソッド。
     * @return ポイント情報のリスト
     */
    private List<Map<String, Object>> createPointList() {
        List<Map<String, Object>> pointList = new ArrayList<Map<String, Object>>();
        pointList.add(new LinkedHashMap<String, Object>());
        pointList.get(0).put("region_id", "08");
        pointList.get(0).put("station_id", "40336");

        return pointList;
    }

    /**
     * リクエストのエリア情報を生成するテスト用のメソッド。
     * @return エリア情報
     */
    private Map<String, Object> createArea() {
        Map<String, Object> area = new LinkedHashMap<String, Object>();
        area.put("nw_lat", "36.05666732788087");
        area.put("nw_lon", "140.124");
        area.put("se_lat", "36.05666732788086");
        area.put("se_lon", "140.125");

        return area;
    }

    // ---------- Cligen用のリクエスト生成処理 ----------
    /**
     * Cligenのリクエスト(point)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCligenPointParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "point");
        ((Map<String, Object>) params.get("data")).put("duration", "daily");
        ((Map<String, Object>) params.get("data")).put("begin_year", "2000");
        ((Map<String, Object>) params.get("data")).put("end_year", "2000");
        ((Map<String, Object>) params.get("data")).put("source_id", "amedas");
        ((Map<String, Object>) params.get("data")).put("point", this.createPointList());

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("start_year", "2015");
        ((Map<String, Object>) params.get("params")).put("num_of_year", "1");
        List<String> climateIdList = new ArrayList<String>();
//        climateIdList.add("rain");
        climateIdList.add("airtemperature");
//        climateIdList.add("wind");
//        climateIdList.add("radiation");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cligenのリクエスト(point)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCligenPointRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 2;
        tRequest.params = this.createCligenPointParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CLIGEN_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    /**
     * Cligenのリクエスト(area)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCligenAreaParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "area");
        ((Map<String, Object>) params.get("data")).put("duration", "daily");
        ((Map<String, Object>) params.get("data")).put("begin_year", "2000");
        ((Map<String, Object>) params.get("data")).put("end_year", "2000");
        ((Map<String, Object>) params.get("data")).put("source_id", "amedas");
        ((Map<String, Object>) params.get("data")).put("area", this.createArea());

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("start_year", "2015");
        ((Map<String, Object>) params.get("params")).put("num_of_year", "1");
        List<String> climateIdList = new ArrayList<String>();
        climateIdList.add("rain");
        climateIdList.add("airtemperature");
        climateIdList.add("wind");
        climateIdList.add("radiation");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cligenのリクエスト(area)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCligenAreaRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 2;
        tRequest.params = this.createCligenAreaParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CLIGEN_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    /**
     * Cligenのリクエスト(user)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCligenUserParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "user");
//        ((Map<String, Object>) params.get("data")).put("user", "5f9145e0-d241-3521-ba00-5cbb58322897"); //つくば(40336) 2006/1/1 - 2006/12/31
//        ((Map<String, Object>) params.get("data")).put("user", "e3c34432-a059-35bc-90c3-9e88970f001f"); //土浦(40341) 2000/1/1 - 2001/1/2
        ((Map<String, Object>) params.get("data")).put("user", "4795dc1f-cfc5-3436-afba-3c090499c577"); //つくば(40336) 2006/1/1 - 2006/12/31、土浦(40341) 2000/1/1 - 2001/1/2

//        ((Map<String, Object>) params.get("data")).put("user", "ad97df2c-ae93-11e3-869f-9782309ee8cd");

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("start_year", "2014");
        ((Map<String, Object>) params.get("params")).put("num_of_year", "2");
        List<String> climateIdList = new ArrayList<String>();
        climateIdList.add("rain");
        climateIdList.add("airtemperature");
        climateIdList.add("wind");
        climateIdList.add("radiation");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cligenのリクエスト(user)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCligenUserRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 1;
        tRequest.params = this.createCligenUserParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CLIGEN_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    // ---------- Cdfdm用のリクエスト生成処理 ----------
    /**
     * Cdfdmのリクエスト(point)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCdfdmPointParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "point");
        ((Map<String, Object>) params.get("data")).put("duration", "daily");
        ((Map<String, Object>) params.get("data")).put("begin_year", "2000");
        ((Map<String, Object>) params.get("data")).put("end_year", "2000");
        ((Map<String, Object>) params.get("data")).put("source_id", "amedas");
        ((Map<String, Object>) params.get("data")).put("point", this.createPointList());

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("model_id", "10-month-lead");
        ((Map<String, Object>) params.get("params")).put("set_id", "en1");
        ((Map<String, Object>) params.get("params")).put("start_year", "2010");
        ((Map<String, Object>) params.get("params")).put("stop_year", "2013");
        List<String> climateIdList = new ArrayList<String>();
        climateIdList.add("rain");
        climateIdList.add("airtemperature");
//        climateIdList.add("wind");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cdfdmのリクエスト(point)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCdfdmPointRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 2;
        tRequest.params = this.createCdfdmPointParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CDFDM_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    /**
     * Cdfdmのリクエスト(area)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCdfdmAreaParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "area");
        ((Map<String, Object>) params.get("data")).put("duration", "daily");
        ((Map<String, Object>) params.get("data")).put("begin_year", "2000");
        ((Map<String, Object>) params.get("data")).put("end_year", "2000");
        ((Map<String, Object>) params.get("data")).put("source_id", "amedas");
        ((Map<String, Object>) params.get("data")).put("area", this.createArea());

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("model_id", "10-month-lead");
        ((Map<String, Object>) params.get("params")).put("set_id", "en1");
        ((Map<String, Object>) params.get("params")).put("start_year", "2013");
        ((Map<String, Object>) params.get("params")).put("stop_year", "2013");
        List<String> climateIdList = new ArrayList<String>();
//        climateIdList.add("rain");
        climateIdList.add("airtemperature");
//        climateIdList.add("wind");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cdfdmのリクエスト(area)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCdfdmAreaRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 2;
        tRequest.params = this.createCdfdmAreaParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CDFDM_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    /**
     * Cdfdmのリクエスト(user)のJSON形式のパラメータ文字列を生成するテスト用のメソッド。
     * @return JSON形式のパラメータ文字列
     */
    private String createCdfdmUserParams() {
        Map<String, Object> params = new LinkedHashMap<String, Object>();
        params.put("data", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("data")).put("range_type", "user");
        ((Map<String, Object>) params.get("data")).put("user", "5f9145e0-d241-3521-ba00-5cbb58322897");

        params.put("params", new LinkedHashMap<String, Object>());
        ((Map<String, Object>) params.get("params")).put("model_id", "10-month-lead");
        ((Map<String, Object>) params.get("params")).put("set_id", "en1");
        ((Map<String, Object>) params.get("params")).put("start_year", "2010");
        ((Map<String, Object>) params.get("params")).put("stop_year", "2013");
        List<String> climateIdList = new ArrayList<String>();
        climateIdList.add("rain");
        climateIdList.add("airtemperature");
        climateIdList.add("wind");
        ((Map<String, Object>) params.get("params")).put("climate_id", climateIdList);

        return JSON.encode(params);
    }

    /**
     * Cdfdmのリクエスト(user)をDBに登録するテスト用のメソッド。
     * @return リクエストID
     */
    private String insertCdfdmUserRequest() {
        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報を登録する。
        TRequest tRequest = new TRequest();
        tRequest.requestId = RequestIdUtils.genRequestId();
        tRequest.dataTypeId = 1;
        tRequest.params = this.createCdfdmUserParams();
        tRequest.userId = "user1";
        tRequest.pubFlag = 1;
        tRequest.generatorId = CDFDM_GENERATOR_ID;
        tRequest.requestStatusId = "ACCEPT";
        tRequest.registeredAt = nowTimestamp;
        int count = jdbcManager
                .insert(tRequest)
                .excludesNull()
                .execute();
        if (count == 0) {
            // リクエスト情報の登録失敗
            log.error("リクエスト情報の登録失敗");
            return null;
        }

        // 生成したリクエスト情報のリクエストIDを取得する。
        List<TRequest> tRequestList = jdbcManager
                .from(TRequest.class)
                .where("registered_at = ?", nowTimestamp)
                .getResultList();
        if (tRequestList.size() == 0) {
            // 生成したリクエスト情報の取得失敗
            log.error("生成したリクエスト情報の取得失敗");
            return null;
        }

        // リクエスト登録日時が同じレコードの先頭を取得する（テスト用のため重複は考慮しない）
        TRequest newRequest = tRequestList.get(0);
        log.info("生成したリクエスト情報 requestId=" + newRequest.requestId);
        return newRequest.requestId;
    }

    //========== アクションイベント ==========
    /**
     * アップロード画面を表示する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String index() {
        return "index.jsp";
    }

    /**
     * CligenのPoint指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCligenPointRequest() {
        createDataRequestForm.requestId = this.insertCligenPointRequest();

        return "index.jsp";
    }

    /**
     * CligenのArea指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCligenAreaRequest() {
        createDataRequestForm.requestId = this.insertCligenAreaRequest();

        return "index.jsp";
    }

    /**
     * CligenのUser指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCligenUserRequest() {
        createDataRequestForm.requestId = this.insertCligenUserRequest();

        return "index.jsp";
    }

    /**
     * CdfdmのPoint指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCdfdmPointRequest() {
        createDataRequestForm.requestId = this.insertCdfdmPointRequest();

        return "index.jsp";
    }

    /**
     * CdfdmのArea指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCdfdmAreaRequest() {
        createDataRequestForm.requestId = this.insertCdfdmAreaRequest();

        return "index.jsp";
    }

    /**
     * CdfdmのUser指定リクエストを登録する。
     *
     * @return index.jsp (テスト用のアップロード画面)
     */
    @Execute(validator = false)
    public String addCdfdmUserRequest() {
        createDataRequestForm.requestId = this.insertCdfdmUserRequest();

        return "index.jsp";
    }

    /**
     * リクエストを送信する。
     *
     * @return /createData (リクエスト要求アクション)
     */
    @Execute(validator = false)
    public String send() {

        return "/createData";
    }

}
