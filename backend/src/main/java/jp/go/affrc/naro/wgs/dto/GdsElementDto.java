package jp.go.affrc.naro.wgs.dto;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.LinkedHashMap;

import jp.go.affrc.naro.wgs.util.ErrorCode;

import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;
import org.seasar.framework.log.Logger;

/**
 * GDFファイルの気象データDTOクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "gdsElementDto")
public class GdsElementDto {

    /** GDFファイル用の温度に変換する場合の倍率 */
    private static final double TEMP_SCALE_FACTOR = 10.0;

    /** GDFファイル用の降水量に変換する場合の倍率 */
    private static final double PREC_SCALE_FACTOR = 10.0;

    /** 12月の最終日 */
    private static final int DECEMBER_LAST_DAY = 31;


    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(GdsElementDto.class);

    /** 年(yy)。 */
    public String year = "";

    /** 月(MM)。 */
    public String month = "";

    /** 日(dd)。 */
    public String day = "";

    /**
     * 最高温度（℃）。
     * 5桁(整数4桁、小数点以下1桁)
     * 右詰
     * ※デフォルトは-999（欠測値）を設定。
     */
    public String maxTemp = " -999";

    /** 最低温度（℃）。
     * 5桁(整数4桁、小数点以下1桁)
     * 右詰
     * ※デフォルトは-999（欠測値）を設定。
     */
    public String minTemp = " -999";

    /** 降水量（mm）。
     * 5桁(整数3桁、小数点以下2桁)
     * 右詰
     * ※デフォルトは-999（欠測値）を設定。
     */
    public String prec = " -999";


    /**
     * MetXmlの日付を元に年月日を設定する。
     * @param date MetXmlの日付
     */
    public void setMetXmlDate(String date) {
        SimpleDateFormat format = new SimpleDateFormat("yyyy/M/d");
        Calendar currentDay = Calendar.getInstance();
        try {
            currentDay.setTime(format.parse(date));
            this.year = String.format("%1$ty", currentDay);
            this.month = String.format("%1$tm", currentDay);
            this.day = String.format("%1$td", currentDay);
        } catch (ParseException e) {
            // 日付の変換エラー
            // 日付の設定値を初期値に更新する。
            log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
            this.year = "";
            this.month = "";
            this.day = "";
        }
    }

    /**
     * MetXmlの最高温度（℃）を元にGDFファイル用の最高温度（℃）を設定する。
     * @param maxTempStr MetXmlの最高温度（℃）
     */
    public void setMetXmlMaxTemp(String maxTempStr) {
        double gdfMaxTemp = Double.parseDouble(maxTempStr);
        this.maxTemp = Integer.toString((int) (gdfMaxTemp * TEMP_SCALE_FACTOR));
    }

    /**
     * MetXmlの最低温度（℃）を元にGDFファイル用の最低温度（℃）を設定する。
     * @param minTempStr MetXmlの最低温度（℃）
     */
    public void setMetXmlMinTemp(String minTempStr) {
        double gdfMinTemp = Double.parseDouble(minTempStr);
        this.minTemp = Integer.toString((int) (gdfMinTemp * TEMP_SCALE_FACTOR));
    }

    /**
     * MetXmlの降水量（mm）を元にGDFファイル用の降水量（mm）を設定する。
     * @param precStr MetXmlの降水量（mm）
     */
    public void setMetXmlPrec(String precStr) {
        double gdfPrec = Double.parseDouble(precStr);
        this.prec = Integer.toString((int) (gdfPrec * PREC_SCALE_FACTOR));
    }

    /**
     * 開始日の1月1日から終了日の12月31までの日数分の日付を設定したGDS形式の空の気象データを生成する。
     * @param start 開始日の文字列
     * @param end 終了日の文字列
     * @return GDS形式の気象データのリスト
     */
    public static LinkedHashMap<String, GdsElementDto> createElementList(String start, String end) {
        LinkedHashMap<String, GdsElementDto> elementList = new LinkedHashMap<String, GdsElementDto>();
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
            GdsElementDto gdsElementDto = new GdsElementDto();
            gdsElementDto.setMetXmlDate(format.format(currentDay.getTime()));
            elementList.put(format.format(currentDay.getTime()), gdsElementDto);
        }

        return elementList;
    }

    /**
     * 年(yy)を取得する。
     * @return 年(yy)
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
     * 最高温度（℃）を取得する。
     * @return 最高温度（℃）
     */
    public String getMaxTemp() {
        return this.maxTemp;
    }

    /**
     * 最低温度（℃）を取得する。
     * @return 最低温度（℃）
     */
    public String getMinTemp() {
        return this.minTemp;
    }

    /**
     * 降水量（mm）を取得する。
     * @return 降水量（mm）
     */
    public String getPrec() {
        return this.prec;
    }
}
