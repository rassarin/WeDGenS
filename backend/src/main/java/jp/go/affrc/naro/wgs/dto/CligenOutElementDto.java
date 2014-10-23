package jp.go.affrc.naro.wgs.dto;

import jp.go.affrc.narc.metxml.Value;

import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;

/**
 * Cligen出力したファイル（WEPP形式）の気象データDTOクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "cligenOutElementDto")
public class CligenOutElementDto {

    /** 年の開始位置 */
    private static final int YEAR_COLUMN_START = 7;
    /** 年の終了位置 */
    private static final int YEAR_COLUMN_END = 11;

    /** 月の開始位置 */
    private static final int MONTH_COLUMN_START = 4;
    /** 月の終了位置 */
    private static final int MONTH_COLUMN_END = 6;

    /** 日の開始位置 */
    private static final int DAY_COLUMN_START = 1;
    /** 日の終了位置 */
    private static final int DAY_COLUMN_END = 3;

    /** 降水量の開始位置 */
    private static final int PRCP_COLUMN_START = 11;
    /** 降水量の終了位置 */
    private static final int PRCP_COLUMN_END = 17;

    /** 降雨継続時間の開始位置 */
    private static final int DUR_COLUMN_START = 17;
    /** 降雨継続時間の終了位置 */
    private static final int DUR_COLUMN_END = 23;

    /** 最高気温の開始位置 */
    private static final int TMAX_COLUMN_START = 35;
    /** 最高気温の終了位置 */
    private static final int TMAX_COLUMN_END = 41;

    /** 最低気温の開始位置 */
    private static final int TMIN_COLUMN_START = 41;
    /** 最低気温の終了位置 */
    private static final int TMIN_COLUMN_END = 47;

    /** 日射量の開始位置 */
    private static final int RAD_COLUMN_START = 47;
    /** 日射量の終了位置 */
    private static final int RAD_COLUMN_END = 52;

    /** 風速の開始位置 */
    private static final int WVL_COLUMN_START = 52;
    /** 風速の終了位置 */
    private static final int WVL_COLUMN_END = 57;

    /** 風向の開始位置 */
    private static final int WDIR_COLUMN_START = 57;
    /** 風向の終了位置 */
    private static final int WDIR_COLUMN_END = 63;

    /** 露点温度の開始位置 */
    private static final int TDEW_COLUMN_START = 63;
    /** 露点温度の終了位置 */
    private static final int TDEW_COLUMN_END = 69;

    /** 年(yy)。 */
    public String year = "";

    /** 月(MM)。 */
    public String month = "";

    /** 日(dd)。 */
    public String day = "";

    /** 降水量（mm） */
    public String prcp = "";

    /** 降雨継続時間（h） */
    public String dur = "";

    /** 最高気温（℃） */
    public String tmax = "";

    /** 最低気温（℃） */
    public String tmin = "";

    /** 日射量（Langleys/day） */
    public String rad = "";

    /** 風速（m/s） */
    public String wvl = "";

    /** 風向（Deg） */
    public String wdir = "";

    /** 露点温度（℃） */
    public String tdew = "";


    /**
     * コンストラクタ。
     * Cligen出力したファイル（WEPP形式）の気象データの行の文字列を元に気象データを生成する。
     * @param lineStr 気象データの行の文字列
     */
    public CligenOutElementDto(String lineStr) {
        this.year = lineStr.substring(YEAR_COLUMN_START, YEAR_COLUMN_END).trim();
        this.month = lineStr.substring(MONTH_COLUMN_START, MONTH_COLUMN_END).trim();
        this.day = lineStr.substring(DAY_COLUMN_START, DAY_COLUMN_END).trim();
        this.prcp = lineStr.substring(PRCP_COLUMN_START, PRCP_COLUMN_END).trim();
        this.dur = lineStr.substring(DUR_COLUMN_START, DUR_COLUMN_END).trim();
        this.tmax = lineStr.substring(TMAX_COLUMN_START, TMAX_COLUMN_END).trim();
        this.tmin = lineStr.substring(TMIN_COLUMN_START, TMIN_COLUMN_END).trim();
        this.rad = lineStr.substring(RAD_COLUMN_START, RAD_COLUMN_END).trim();
        this.wvl = lineStr.substring(WVL_COLUMN_START, WVL_COLUMN_END).trim();
        this.wdir = lineStr.substring(WDIR_COLUMN_START, WDIR_COLUMN_END).trim();
        this.tdew = lineStr.substring(TDEW_COLUMN_START, TDEW_COLUMN_END).trim();
    }

    /**
     * 観測日(年月日)の文字列を取得する。
     * @return 観測日(年月日)の文字列
     */
    public String getDateStr() {
        return this.year + "/" + this.month + "/" + this.day;
    }

    /**
     * 降水量（mm）のMetMXL用の値を取得する。
     * @return 降水量（mm）
     */
    public Value getRainTotalValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(prcp);

        return value;
    }

    /**
     * 降雨継続時間（h）のMetMXL用の値を取得する。
     * @return 降雨継続時間（h）
     */
    public Value getRainDurationValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(dur);

        return value;
    }

    /**
     * 最高気温（℃）のMetMXL用の値を取得する。
     * @return 最高気温（℃）
     */
    public Value getMaxTempValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(tmax);

        return value;
    }

    /**
     * 最低気温（℃）のMetMXL用の値を取得する。
     * @return 最低気温（℃）
     */
    public Value getMinTempValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(tmin);

        return value;
    }

    /**
     * 日射量（Langleys/day）のMetMXL用の値を取得する。
     * @return 日射量（Langleys/day）
     */
    public Value getSolarRadiationValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(rad);

        return value;
    }

    /**
     * 風速（m/s）のMetMXL用の値を取得する。
     * @return 風速（m/s）
     */
    public Value getWindSpeedValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(wvl);

        return value;
    }

    /**
     * 風向（Deg）のMetMXL用の値を取得する。
     * @return 風向（Deg）
     */
    public Value getWindDirectionValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(wdir);

        return value;
    }

    /**
     * 露点温度（℃）のMetMXL用の値を取得する。
     * @return 露点温度（℃）
     */
    public Value getDewPointTempValue() {
        Value value = new Value();

        value.setDate(getDateStr());
        value.setContent(tdew);

        return value;
    }

}