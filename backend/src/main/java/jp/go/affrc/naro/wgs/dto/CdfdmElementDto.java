package jp.go.affrc.naro.wgs.dto;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.LinkedHashMap;

import jp.go.affrc.narc.metxml.Value;
import jp.go.affrc.naro.wgs.util.ErrorCode;

import org.apache.commons.lang.StringUtils;
import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;
import org.seasar.framework.log.Logger;

/**
 * cdfdmファイルの気象データDTOクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "cdfdmElementDto")
public class CdfdmElementDto {

    /** 12月の最終日 */
    private static final int DECEMBER_LAST_DAY = 31;

    /** 年の開始位置 */
    private static final int YEAR_COLUMN_START = 0;
    /** 年の終了位置 */
    private static final int YEAR_COLUMN_END = 4;

    /** 月の開始位置 */
    private static final int MONTH_COLUMN_START = 5;
    /** 月の終了位置 */
    private static final int MONTH_COLUMN_END = 7;

    /** 日の開始位置 */
    private static final int DAY_COLUMN_START = 8;
    /** 日の終了位置 */
    private static final int DAY_COLUMN_END = 10;

    /** 年における日の開始位置 */
    private static final int DAY_OF_YEAR_COLUMN_START = 11;
    /** 年における日の終了位置 */
    private static final int DAY_OF_YEAR_COLUMN_END = 14;

    /** 気象データの値の開始位置 */
    private static final int VALUE_COLUMN_START = 15;
    /** 気象データの値の終了位置 */
    private static final int VALUE_COLUMN_END = 24;


    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(CdfdmElementDto.class);

    /** 年(yyyy)。 */
    public String year = "";

    /** 月(MM)。 */
    public String month = "";

    /** 日(dd)。 */
    public String day = "";

    /** 年における日(DDD)。 */
    public String dayOfYear = "";

    /**
     * 気象データの値。
     * 9桁(整数5桁、小数点以下3桁)
     * 右詰
     * ※デフォルトは-999.000（欠測値）を設定。
     */
    public String value = " -999.000";

    /**
     * コンストラクタ。
     */
    public CdfdmElementDto() {
    }

    /**
     * コンストラクタ。
     * cdfdmが出力したファイル（WEPP形式）の気象データの行の文字列を元に気象データを生成する。
     * @param lineStr 気象データの行の文字列
     */
    public CdfdmElementDto(String lineStr) {
        this.year = lineStr.substring(YEAR_COLUMN_START, YEAR_COLUMN_END).trim();
        this.month = lineStr.substring(MONTH_COLUMN_START, MONTH_COLUMN_END).trim();
        this.day = lineStr.substring(DAY_COLUMN_START, DAY_COLUMN_END).trim();
        this.dayOfYear = lineStr.substring(DAY_OF_YEAR_COLUMN_START, DAY_OF_YEAR_COLUMN_END).trim();
        this.value = lineStr.substring(VALUE_COLUMN_START, VALUE_COLUMN_END).trim();
    }

    /**
     * 観測日(年月日)の文字列を取得する。
     * @return 観測日(年月日)の文字列
     */
    public String getDateStr() {
        return this.year + "/" + this.month + "/" + this.day;
    }

    /**
     * 期間に含まれるか判定する。
     * 開始年、終了年と同じ年を含む期間を指定する。
     * 開始年が未設定(空)の場合はint最小値、終了年が未設定(空)の場合はint最大値で判定する。
     * @param startYearStr 開始年
     * @param endYearStr 終了年
     * @return 判定結果
     */
    public boolean isInPeriod(String startYearStr, String endYearStr) {
        int currentYear = Integer.parseInt(year);

        int startYear = Integer.MIN_VALUE; // 未指定の場合はデフォルトでintの最小値
        if (!StringUtils.isEmpty(startYearStr)) {
            startYear = Integer.parseInt(startYearStr);
        }

        int endYear = Integer.MAX_VALUE; // 未指定の場合はデフォルトでintの最大値
        if (!StringUtils.isEmpty(endYearStr)) {
            endYear = Integer.parseInt(endYearStr);
        }

        if (startYear <= currentYear && currentYear <= endYear) {
            // 期間に含まれる。
            return true;
        }

        // 期間に含まれない。
        return false;
    }

    /**
     * MetXmlの日付を元に年月日を設定する。
     * @param date MetXmlの日付
     */
    public void setMetXmlDate(String date) {
        SimpleDateFormat format = new SimpleDateFormat("yyyy/M/d");
        Calendar currentDay = Calendar.getInstance();
        try {
            currentDay.setTime(format.parse(date));
            this.year = String.format("%1$tY", currentDay);
            this.month = String.format("%1$tm", currentDay);
            this.day = String.format("%1$td", currentDay);
            this.dayOfYear = String.format("%1$tj", currentDay);
        } catch (ParseException e) {
            // 日付の変換エラー
            // 日付の設定値を初期値に更新する。
            log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
            this.year = "";
            this.month = "";
            this.day = "";
            this.dayOfYear = "";
        }
    }

    /**
     * MetXmlの気象データ値を元にcdfdm入力ファイル用の気象データ値を設定する。
     * @param valueStr MetXmlの気象データ値
     */
    public void setMetXmlValue(String valueStr) {
        double cdfdmValueStr = Double.parseDouble(valueStr);
        this.value = String.format("%.3f", cdfdmValueStr);
    }

    /**
     * 開始日の1月1日から終了日の12月31までの日数分の日付を設定したcdfdm入力ファイル形式の空の気象データを生成する。
     * @param start 開始日の文字列
     * @param end 終了日の文字列
     * @return cdfdm入力ファイル形式の気象データのリスト
     */
    public static LinkedHashMap<String, CdfdmElementDto> createElementList(String start, String end) {
        LinkedHashMap<String, CdfdmElementDto> elementList = new LinkedHashMap<String, CdfdmElementDto>();
        SimpleDateFormat format = new SimpleDateFormat("yyyy/M/d");

        Calendar currentDay = Calendar.getInstance();
        try {
            currentDay.setTime(format.parse(start));
            currentDay.set(Calendar.MONTH, Calendar.JANUARY);
            currentDay.set(Calendar.DAY_OF_MONTH, 1);
        } catch (ParseException e) {
            // 日付の変換エラー
            log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
            return null;
        }
        Calendar endDay = Calendar.getInstance();
        try {
            endDay.setTime(format.parse(end));
            endDay.set(Calendar.MONTH, Calendar.DECEMBER);
            endDay.set(Calendar.DAY_OF_MONTH, DECEMBER_LAST_DAY);
        } catch (ParseException e) {
            // 日付の変換エラー
            log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
            return null;
        }
        for (; !currentDay.after(endDay); currentDay.add(Calendar.DAY_OF_MONTH, 1)) {
            // 開始日から終了日までGDS形式の空の気象データを生成する。
            CdfdmElementDto gdsElementDto = new CdfdmElementDto();
            gdsElementDto.setMetXmlDate(format.format(currentDay.getTime()));
            elementList.put(format.format(currentDay.getTime()), gdsElementDto);
        }

        return elementList;
    }

    /**
     * 年(yyyy)を取得する。
     * @return 年(yyyy)
     */
    public String getYear() {
        return this.year;
    }

    /**
     * 月(MM)を取得する。
     * @return 月(MM)
     */
    public String getMonth() {
        return this.month;
    }

    /**
     * 日(dd)を取得する。
     * @return 日(dd)
     */
    public String getDay() {
        return this.day;
    }

    /**
     * 年における日(DDD)を取得する。
     * @return 年における日(DDD)
     */
    public String getDayOfYear() {
        return this.dayOfYear;
    }

    /**
     * 気象データの値を取得する。
     * @return 気象データの値
     */
    public String getValue() {
        return this.value;
    }

    /**
     * MetMXL用の気象データ値を取得する。
     * @return MetMXL用の値
     */
    public Value getMetXmlValue() {
        Value metXmlValue = new Value();

        metXmlValue.setDate(getDateStr());
        metXmlValue.setContent(this.value);

        return metXmlValue;
    }

}
