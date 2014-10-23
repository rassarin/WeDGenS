package jp.go.affrc.naro.wgs.util.weatherData;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import jp.go.affrc.narc.metxml.Element;
import jp.go.affrc.narc.metxml.Name;
import jp.go.affrc.narc.metxml.Subelement;
import jp.go.affrc.narc.metxml.Value;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import net.agmodel.physical.Duration;
import net.agmodel.physical.Interval;
import net.agmodel.physical.JigsawQuantity;
import net.agmodel.weatherData.AirTempMaxMinMeanImpl;
import net.agmodel.weatherData.AirTempSingleImpl;
import net.agmodel.weatherData.MetSequence;
import net.agmodel.weatherData.RainImpl;
import net.agmodel.weatherData.SunshineImpl;
import net.agmodel.weatherData.WindImpl;

import org.apache.commons.lang.StringUtils;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FSDataInputStream;
import org.apache.hadoop.fs.FSDataOutputStream;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.seasar.framework.log.Logger;

/**
 * MetSequenceユーティリティクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class MetSequenceUtils {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(MetSequenceUtils.class);

    /** 気温(日別)のサブエレメントのインデックス：最低気温 */
    private static final int SUB_INDEX_DAILY_MIN_TEMP = 0;

    /** 気温(日別)のサブエレメントのインデックス：最高気温 */
    private static final int SUB_INDEX_DAILY_MAX_TEMP = 1;

    /** 気温(日別)のサブエレメントのインデックス：平均気温 */
    private static final int SUB_INDEX_DAILY_AVE_TEMP = 2;

    /** 気温(時別)のサブエレメントのインデックス：平均気温 */
    private static final int SUB_INDEX_HOURLY_AVE_TEMP = 0;

    /** 雨量のサブエレメントのインデックス：降水量 */
    private static final int SUB_INDEX_RAIN_TOTAL = 0;

    /** 日照のサブエレメントのインデックス：日照時間 */
    private static final int SUB_INDEX_SUNSHINE_TOTAL = 0;

    /** 風のサブエレメントのインデックス：風速 */
    private static final int SUB_INDEX_WIND_SPEED = 0;

    /** 風のサブエレメントのインデックス：風向 */
    private static final int SUB_INDEX_WIND_DIRECTION = 1;

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private MetSequenceUtils() {
    }

    // ========== MetXMLオブジェクトのファイルアクセス処理 ==========
    // ---------- ローカルファイルアクセス処理 ----------
    /**
     * MetSequenceフォーマットのファイルを読み込んでJavaObjectを生成する。
     * @param metSequenceFileName MetSequenceファイル名
     * @return MetSequenceのオブジェクト
     */
    public static MetSequence readMetSequenceFile(String metSequenceFileName) {
        MetSequence result = null;
        FileInputStream fis = null;
        ObjectInputStream ois = null;
        try {
            fis = new FileInputStream(metSequenceFileName);
            ois = new ObjectInputStream(fis);
            result = (MetSequence) ois.readObject();
        } catch (IOException | ClassNotFoundException e) {
            // ローカルファイルアクセスエラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(metSequenceFileName), e);
        } finally {
            if (fis != null) {
                try {
                    fis.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(metSequenceFileName), e);
                }
            }
        }

        return result;
    }

    /**
     * MetSequenceフォーマットのファイルを読み込んでJavaObjectを生成する。
     * @param metSequenceFileName MetSequenceファイル名
     * @return MetSequenceのオブジェクトリスト
     */
    public static List<MetSequence> readMetSequenceListFile(String metSequenceFileName) {
        List<MetSequence> result = new ArrayList<MetSequence>();
        FileInputStream fis = null;
        try {
            fis = new FileInputStream(metSequenceFileName);
            ObjectInputStream ois = new ObjectInputStream(fis);
            while (true) {
                MetSequence data = (MetSequence) ois.readObject();
                if (data != null) {
                    result.add(data);
                }
            }
        } catch (IOException | ClassNotFoundException e) {
            // ローカルファイルアクセスエラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(metSequenceFileName), e);
        } finally {
            if (fis != null) {
                try {
                    fis.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(metSequenceFileName), e);
                }
            }
        }

        return result;
    }

    // ---------- HDFSファイルアクセス処理 ----------
    /**
     * MetSequenceフォーマットのHDFSファイルを読み込んでJavaObjectを生成する。
     * @param metSequenceHdfsFileName MetSequenceファイル名(HDFSファイル)
     * @return MetSequenceのオブジェクト
     */
    public static MetSequence readMetSequenceHdfsFile(String metSequenceHdfsFileName) {
        MetSequence data = null;
        FSDataInputStream fsdIn = null;
        try {
            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(metSequenceHdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            fsdIn = fs.open(hdfsPath);

            // HDFSファイルからMetSequenceのオブジェクトを読込み
            ObjectInputStream ois = new ObjectInputStream(fsdIn);
            data = (MetSequence) ois.readObject();
        } catch (IOException | ClassNotFoundException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(metSequenceHdfsFileName), e);
        } finally {
            if (fsdIn != null) {
                try {
                    fsdIn.close();
                } catch (IOException e) {
                    // HDFSファイルクローズエラー
                    log.warn(ErrorCode.HDFS_CLOSE_ERROR.getErrorMess(metSequenceHdfsFileName), e);
                }
            }
        }
        return data;
    }

    /**
     * MetSequenceのオブジェクトをMetSequenceフォーマットのHDFSファイルに出力する。
     * @param obj MetSequenceのオブジェクト
     * @param metSequenceHdfsFileName MetSequenceファイル名(HDFSファイル)
     * @return 出力結果
     */
    public static boolean writeMetSequenceHdfsFile(Object obj, String metSequenceHdfsFileName) {
        boolean result = true;
        FSDataOutputStream fsdOut = null;
        try {
            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(metSequenceHdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            fs.setVerifyChecksum(true);
            fsdOut = fs.create(hdfsPath);

            // HDFSファイルにMetSequenceを書き込む
            ObjectOutputStream oos = new ObjectOutputStream(fsdOut);
            oos.writeObject(obj);
        } catch (IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(metSequenceHdfsFileName), e);
            result = false;
        } finally {
            if (fsdOut != null) {
                try {
                    fsdOut.close();
                } catch (IOException e) {
                    // HDFSファイルクローズエラー
                    log.warn(ErrorCode.HDFS_CLOSE_ERROR.getErrorMess(metSequenceHdfsFileName), e);
                }
            }
        }
        return result;
    }


    // ========== MetSequenceからMetXMLへの変換関連処理 ==========
    /**
     * デュレーションに対応するフォーマットの日時文字列を生成する。
     * @param duration デュレーション
     * @param date 日時
     * @return 日時文字列
     */
    private static String formatDateStr(Duration duration, Date date) {
        SimpleDateFormat dailyFormat = new SimpleDateFormat("yyyy/M/d");
        SimpleDateFormat hourlyFormat = new SimpleDateFormat("yyyy/M/d H:mm");

        if (Duration.ONE_DAY.compareTo(duration) == 0) {
            return dailyFormat.format(date);
        } else if (Duration.ONE_HOUR.compareTo(duration) == 0) {
            return hourlyFormat.format(date);
        }

        // 該当するデュレーションなし。
        return null;
    }

    /**
     * エレメントIDの文字列からスペースを削除する。
     * @param elementId エレメントIDの文字列
     * @param defaultElementId 生成したエレメントIDの文字列が空文字の場合に返却するデフォルトエレメントID
     * @return スペースを削除後のエレメントIDの文字列
     */
    private static String trimElementId(String elementId, String defaultElementId) {
        String result = null;

        if (elementId != null) {
            result = elementId.replaceAll(" ", "");
        }

        if (!StringUtils.isEmpty(result)) {
            return result;
        }

        // 生成した文字列が空の場合はデフォルト単位名を返す。
        return defaultElementId;
    }

    /**
     * 単位名の文字列から括弧"()"とスペースを削除する。
     * @param unitName 単位名の文字列
     * @param defaultUnitName 生成した単位名の文字列が空文字の場合に返却するデフォルト単位名
     * @return 括弧"()"とスペースを削除後の単位名の文字列
     */
    private static String trimUnitName(String unitName, String defaultUnitName) {
        String result = null;

        if (unitName != null) {
            result = unitName.replaceAll("[() ]", "");
        }

        if (!StringUtils.isEmpty(result)) {
            return result;
        }

        // 生成した文字列が空の場合はデフォルト単位名を返す。
        return defaultUnitName;
    }

    /**
     * MetSequenceの気温(最高、最低、平均)データをMetXMLのElementデータに変換する。
     * @param airTemp MetSequenceの気温(最高、最低、平均)データ
     * @return MetXMLのElementデータ
     */
    private static Element convertMetXml(AirTempMaxMinMeanImpl airTemp) {

        // 気温
        Element element = new Element();
        element.setId(MetSequenceUtils.trimElementId(airTemp.getName(), "airtemperature"));
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent("気温");

        // 最低気温
        Subelement minTemp = new Subelement();
        minTemp.setId("Min");
        minTemp.setUnit(MetSequenceUtils.trimUnitName(airTemp.getUnitsHeading(SUB_INDEX_DAILY_MIN_TEMP), "℃"));
        minTemp.setName(new Name());
        minTemp.getName().setLang("ja");
        minTemp.getName().setContent(airTemp.getSubHeading(SUB_INDEX_DAILY_MIN_TEMP));
        element.getSubelement().add(minTemp);

        // 最高気温
        Subelement maxTemp = new Subelement();
        maxTemp.setId("Max");
        maxTemp.setUnit(MetSequenceUtils.trimUnitName(airTemp.getUnitsHeading(SUB_INDEX_DAILY_MAX_TEMP), "℃"));
        maxTemp.setName(new Name());
        maxTemp.getName().setLang("ja");
        maxTemp.getName().setContent(airTemp.getSubHeading(SUB_INDEX_DAILY_MAX_TEMP));
        element.getSubelement().add(maxTemp);

        // 平均気温
        Subelement aveTemp = new Subelement();
        aveTemp.setId("Ave");
        aveTemp.setUnit(MetSequenceUtils.trimUnitName(airTemp.getUnitsHeading(SUB_INDEX_DAILY_AVE_TEMP), "℃"));
        aveTemp.setName(new Name());
        aveTemp.getName().setLang("ja");
        aveTemp.getName().setContent(airTemp.getSubHeading(SUB_INDEX_DAILY_AVE_TEMP));
        element.getSubelement().add(aveTemp);

        Interval dateRange = airTemp.getDateRange(); // データ範囲
        Duration duration = airTemp.getResolution(); // 日別(ONE_DAY) or 時別(ONE_HOUR)

        Interval tergetInterval = new Interval(dateRange.getStart() , duration);
        while (dateRange.encompasses(tergetInterval)) {
            // 最低気温
            JigsawQuantity minTempJq = airTemp.getMinimum(tergetInterval);
            if (minTempJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value minTempValue = new Value();
                minTempValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                minTempValue.setContent(String.valueOf(minTempJq.getAmount()));
                minTemp.getValue().add(minTempValue);
            }

            // 最高気温
            JigsawQuantity maxTempJq = airTemp.getMaximum(tergetInterval);
            if (maxTempJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value maxTempValue = new Value();
                maxTempValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                maxTempValue.setContent(String.valueOf(maxTempJq.getAmount()));
                maxTemp.getValue().add(maxTempValue);
            }

            // 平均気温
            JigsawQuantity aveTempJq = airTemp.getAverage(tergetInterval);
            if (aveTempJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value aveTempValue = new Value();
                aveTempValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                aveTempValue.setContent(String.valueOf(aveTempJq.getAmount()));
                aveTemp.getValue().add(aveTempValue);
            }

            // データ取得対象を次の値（日、時間）にする
            tergetInterval = new Interval(tergetInterval.getEnd() , duration);
        }

        return element;
    }

    /**
     * MetSequenceの気温(シングル)データをMetXMLのElementデータに変換する。
     * @param airTemp MetSequenceの気温(シングル)データ
     * @return MetXMLのElementデータ
     */
    private static Element convertMetXml(AirTempSingleImpl airTemp) {

        // 気温
        Element element = new Element();
        element.setId(MetSequenceUtils.trimElementId(airTemp.getName(), "airtemperature"));
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent("気温");

        // 平均気温
        Subelement aveTemp = new Subelement();
        aveTemp.setId("Ave");
        aveTemp.setUnit(MetSequenceUtils.trimUnitName(airTemp.getUnitsHeading(SUB_INDEX_HOURLY_AVE_TEMP), "℃"));
        aveTemp.setName(new Name());
        aveTemp.getName().setLang("ja");
        aveTemp.getName().setContent(airTemp.getSubHeading(SUB_INDEX_HOURLY_AVE_TEMP));
        element.getSubelement().add(aveTemp);

        Interval dateRange = airTemp.getDateRange(); // データ範囲
        Duration duration = airTemp.getResolution(); // 日別(ONE_DAY) or 時別(ONE_HOUR)

        Interval tergetInterval = new Interval(dateRange.getStart() , duration);
        while (dateRange.encompasses(tergetInterval)) {
            // 平均気温
            JigsawQuantity aveTempJq = airTemp.getAverage(tergetInterval);
            if (aveTempJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value aveTempValue = new Value();
                aveTempValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                aveTempValue.setContent(String.valueOf(aveTempJq.getAmount()));
                aveTemp.getValue().add(aveTempValue);
            }

            // データ取得対象を次の値（日、時間）にする
            tergetInterval = new Interval(tergetInterval.getEnd() , duration);
        }

        return element;
    }

    /**
     * MetSequenceの降水量データをMetXMLのElementデータに変換する。
     * @param rain MetSequenceの降水量データ
     * @return MetXMLのElementデータ
     */
    private static Element convertMetXml(RainImpl rain) {

        // 降水量
        Element element = new Element();
        element.setId(MetSequenceUtils.trimElementId(rain.getName(), "rain"));
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent("雨量");

        // 合計
        Subelement rainTotal = new Subelement();
        rainTotal.setId("Total");
        rainTotal.setUnit(MetSequenceUtils.trimUnitName(rain.getUnitsHeading(SUB_INDEX_RAIN_TOTAL), "mm"));
        rainTotal.setName(new Name());
        rainTotal.getName().setLang("ja");
        rainTotal.getName().setContent(rain.getSubHeading(SUB_INDEX_RAIN_TOTAL));
        element.getSubelement().add(rainTotal);

        Interval dateRange = rain.getDateRange(); // データ範囲
        Duration duration = rain.getResolution(); // 日別(ONE_DAY) or 時別(ONE_HOUR)

        Interval tergetInterval = new Interval(dateRange.getStart() , duration);
        while (dateRange.encompasses(tergetInterval)) {
            // 合計
            JigsawQuantity rainTotalJq = rain.getTotal(tergetInterval);
            if (rainTotalJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value rainTotalValue = new Value();
                rainTotalValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                rainTotalValue.setContent(String.valueOf(rainTotalJq.getAmount()));
                rainTotal.getValue().add(rainTotalValue);
            }

            // データ取得対象を次の値（日、時間）にする
            tergetInterval = new Interval(tergetInterval.getEnd() , duration);
        }

        return element;
    }

    /**
     * MetSequenceの日照時間データをMetXMLのElementデータに変換する。
     * @param sunshine MetSequenceの日照時間データ
     * @return MetXMLのElementデータ
     */
    private static Element convertMetXml(SunshineImpl sunshine) {

        // 日照
        Element element = new Element();
        element.setId(MetSequenceUtils.trimElementId(sunshine.getName(), "brightsunlight"));
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent("日照");

        // 合計
        Subelement brightsunlightTotal = new Subelement();
        brightsunlightTotal.setId("Total");
        brightsunlightTotal.setUnit(MetSequenceUtils.trimUnitName(sunshine.getUnitsHeading(SUB_INDEX_SUNSHINE_TOTAL), "時間"));
        brightsunlightTotal.setName(new Name());
        brightsunlightTotal.getName().setLang("ja");
        brightsunlightTotal.getName().setContent(sunshine.getSubHeading(SUB_INDEX_SUNSHINE_TOTAL));
        element.getSubelement().add(brightsunlightTotal);

        Interval dateRange = sunshine.getDateRange(); // データ範囲
        Duration duration = sunshine.getResolution(); // 日別(ONE_DAY) or 時別(ONE_HOUR)

        Interval tergetInterval = new Interval(dateRange.getStart() , duration);
        while (dateRange.encompasses(tergetInterval)) {
            // 合計
            JigsawQuantity brightsunlightTotalJq = sunshine.getTotal(tergetInterval);
            if (brightsunlightTotalJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value brightsunlightTotalValue = new Value();
                brightsunlightTotalValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                brightsunlightTotalValue.setContent(String.valueOf(brightsunlightTotalJq.getAmount()));
                brightsunlightTotal.getValue().add(brightsunlightTotalValue);
            }

            // データ取得対象を次の値（日、時間）にする
            tergetInterval = new Interval(tergetInterval.getEnd() , duration);
        }

        return element;
    }

    /**
     * MetSequenceの風速データをMetXMLのElementデータに変換する。
     * @param wind MetSequenceの風速データ
     * @return MetXMLのElementデータ
     */
    private static Element convertMetXml(WindImpl wind) {

         // 風
        Element element = new Element();
        element.setId(MetSequenceUtils.trimElementId(wind.getName(), "wind"));
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent("風");

        // 風速
        Subelement windSpeed = new Subelement();
        windSpeed.setId("Speed");
        windSpeed.setUnit(MetSequenceUtils.trimUnitName(wind.getUnitsHeading(SUB_INDEX_WIND_SPEED), "m/s"));
        windSpeed.setName(new Name());
        windSpeed.getName().setLang("ja");
        windSpeed.getName().setContent(wind.getSubHeading(SUB_INDEX_WIND_SPEED));
        element.getSubelement().add(windSpeed);

        Subelement windDirection = null;
        if (wind.hasDirection()) {
            // 風向
            windDirection = new Subelement();
            windDirection.setId("Direction");
            windDirection.setUnit(MetSequenceUtils.trimUnitName(wind.getUnitsHeading(SUB_INDEX_WIND_DIRECTION), "degrees"));
//            windDirection.setUnit("degrees"); // MetXMLのサイトで取得した場合に空文字となるため独自に"degrees"を設定する。
            windDirection.setName(new Name());
            windDirection.getName().setLang("ja");
            windDirection.getName().setContent(wind.getSubHeading(SUB_INDEX_WIND_DIRECTION));
            element.getSubelement().add(windDirection);
        }

        Interval dateRange = wind.getDateRange(); // データ範囲
        Duration duration = wind.getResolution(); // 日別(ONE_DAY) or 時別(ONE_HOUR)

        Interval tergetInterval = new Interval(dateRange.getStart() , duration);
        while (dateRange.encompasses(tergetInterval)) {
            // 風速
            JigsawQuantity windSpeedJq = wind.getAverageSpeed(tergetInterval);
            if (windSpeedJq.getCoverage() > JigsawQuantity.DEVOID) {
                Value windSpeedValue = new Value();
                windSpeedValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                windSpeedValue.setContent(String.valueOf(windSpeedJq.getAmount()));
                windSpeed.getValue().add(windSpeedValue);
            }

            if (wind.hasDirection()) {
                // 風向
                JigsawQuantity windDirectionJq = wind.getInstantDirection(tergetInterval.getEnd());
                if (windDirectionJq.getCoverage() > JigsawQuantity.DEVOID) {
                    Value windDirectionValue = new Value();
                    windDirectionValue.setDate(MetSequenceUtils.formatDateStr(duration, tergetInterval.getStart()));
                    windDirectionValue.setContent(String.valueOf(windDirectionJq.getAmount()));
                    windDirection.getValue().add(windDirectionValue);
                }
            }

            // データ取得対象を次の値（日、時間）にする
            tergetInterval = new Interval(tergetInterval.getEnd() , duration);
        }

        return element;
    }

    /**
     * MetSequenceのオブジェクトをMetXML要素のオブジェクトに変換する。
     * @param metSequence MetSequenceのオブジェクト
     * @return MetXML要素のオブジェクト
     */
    public static Element convertMetXml(MetSequence metSequence) {
        Element element = new Element();

        if (metSequence instanceof AirTempMaxMinMeanImpl) {
            // 気温(最高、最低、平均)
            element = MetSequenceUtils.convertMetXml((AirTempMaxMinMeanImpl) metSequence);
        } else if (metSequence instanceof AirTempSingleImpl) {
            // 気温(シングル)
            element = MetSequenceUtils.convertMetXml((AirTempSingleImpl) metSequence);
        } else if (metSequence instanceof RainImpl) {
            // 降水量
            element = MetSequenceUtils.convertMetXml((RainImpl) metSequence);
        } else if (metSequence instanceof SunshineImpl) {
            // 日照時間
            element = MetSequenceUtils.convertMetXml((SunshineImpl) metSequence);
        } else if (metSequence instanceof WindImpl) {
            // 風速
            element = MetSequenceUtils.convertMetXml((WindImpl) metSequence);
        }

        return element;
    }

}
