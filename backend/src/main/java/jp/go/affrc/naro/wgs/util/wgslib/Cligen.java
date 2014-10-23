package jp.go.affrc.naro.wgs.util.wgslib;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.lang.ProcessBuilder.Redirect;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Properties;

import jp.go.affrc.narc.metxml.Data;
import jp.go.affrc.narc.metxml.Dataset;
import jp.go.affrc.narc.metxml.Duration;
import jp.go.affrc.narc.metxml.Element;
import jp.go.affrc.narc.metxml.Interval;
import jp.go.affrc.narc.metxml.Name;
import jp.go.affrc.narc.metxml.Region;
import jp.go.affrc.narc.metxml.Station;
import jp.go.affrc.narc.metxml.Subelement;
import jp.go.affrc.narc.metxml.Value;
import jp.go.affrc.naro.wgs.dto.CligenOutElementDto;
import jp.go.affrc.naro.wgs.dto.GdsElementDto;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import jp.go.affrc.naro.wgs.util.TmpFileUtils;
import jp.go.affrc.naro.wgs.util.weatherData.LocationUtils;
import jp.go.affrc.naro.wgs.util.weatherData.MetXmlUtils;

import org.apache.commons.lang.StringUtils;
import org.seasar.framework.exception.IORuntimeException;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

import freemarker.template.Configuration;
import freemarker.template.Template;
import freemarker.template.TemplateException;

/**
 * Cligenのライブラリを使った気象データ生成クラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public class Cligen implements WeatherGenerator {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(Cligen.class);

    /** 出力ファイルの日別データの開始行数 */
    private static final int DAILY_DATA_START_LINE = 16;

    /** 出力ファイルの日別データ行の桁数 */
    private static final int DAILY_DATA_STRING_SIZE = 69;

    /** リクエストID */
    private String requestId = null;

    /** 気象データ */
    private Dataset weatherData = null;

    /** パラメータ */
    private Map<String, Object> parameter = null;

    /**
     * 入力データファイルの内容を生成するテンプレート処理を実行する。
     *
     * @param templateFilePath テンプレートファイルパス
     * @param dataMap テンプレートに埋め込むデータ
     * @return フィールドデータ
     * @throws IOException テンプレートファイルアクセスエラー
     * @throws TemplateException テンプレート処理エラー
     */
    private String getInputFileData(String templateFilePath, Object dataMap) throws IOException, TemplateException {

        // テンプレートファイルパスからファイルパスとファイル名を取得する。
        String filePath = templateFilePath.substring(0, templateFilePath.lastIndexOf("/"));
        String fileName = templateFilePath.substring(templateFilePath.lastIndexOf("/") + 1);

        // FreeMarkerを使ってフィールドデータを生成する。
        Configuration cfg = new Configuration();
        cfg.setDirectoryForTemplateLoading(new File(filePath));
        cfg.setDefaultEncoding("MS932");
        Template temp = cfg.getTemplate(fileName);
        ByteArrayOutputStream bos = new ByteArrayOutputStream();
        Writer out = new OutputStreamWriter(bos);
        temp.process(dataMap, out);

        return bos.toString();
    }

    /**
     * コマンド実行に必要な入力ファイルを生成する。
     * @return ステーションIDのリスト
     */
    private List<String> createInputFile() {
        List<String> stationIdList = new ArrayList<String>();

        for (Data data : weatherData.getData()) {
            // MetXML形式をGDS形式に変換する。
            Map<String, Object> gdsData = new HashMap<String, Object>();
            // 1行目に出力するデータ
            gdsData.put("stationId", data.getStation().getId());
            gdsData.put("regionName", data.getRegion().getName().getContent());
            gdsData.put("stationName", data.getStation().getName().getContent());
            gdsData.put("lat", LocationUtils.getGdsDegreesMinutes(data.getStation().getPlace().getLat()));
            gdsData.put("lon", LocationUtils.getGdsDegreesMinutes(data.getStation().getPlace().getLon()));
            gdsData.put("alt", LocationUtils.getGdsAlt(data.getStation().getPlace().getAlt()));

            // GDS形式の気象データ格納用に、日数分の空の気象データを生成する。
            LinkedHashMap<String, GdsElementDto> elementMap = GdsElementDto.createElementList(
                    data.getInterval().getStart(), data.getInterval().getEnd());

            // 2行目以降に出力するデータ
            // 最高温度（℃）
            Element airtemperature = MetXmlUtils.serchElement(data.getElement(), "airtemperature");
            Subelement maxTemp = MetXmlUtils.serchSubelement(airtemperature.getSubelement(), "Max");
            for (Value value : maxTemp.getValue()) {
                GdsElementDto gdsElement = elementMap.get(value.getDate());
                if (gdsElement != null) {
                    gdsElement.setMetXmlMaxTemp(value.getContent());
                } else {
                    // 該当日付のデータなし
                    log.info("最高気温に該当日付のデータなし　日付=" + value.getDate());
                }
            }

            // 最低温度（℃）
            Subelement minTemp = MetXmlUtils.serchSubelement(airtemperature.getSubelement(), "Min");
            for (Value value : minTemp.getValue()) {
                GdsElementDto gdsElement = elementMap.get(value.getDate());
                if (gdsElement != null) {
                    gdsElement.setMetXmlMinTemp(value.getContent());
                } else {
                    // 該当日付のデータなし
                    log.info("最低気温に該当日付のデータなし　日付=" + value.getDate());
                }
            }

            // 降水量（mm）
            Element rain = MetXmlUtils.serchElement(data.getElement(), "rain");
            Subelement prec = MetXmlUtils.serchSubelement(rain.getSubelement(), "Total");
            for (Value value : prec.getValue()) {
                GdsElementDto gdsElement = elementMap.get(value.getDate());
                if (gdsElement != null) {
                    gdsElement.setMetXmlPrec(value.getContent());
                } else {
                    // 該当日付のデータなし
                    log.info("降水量に該当日付のデータなし　日付=" + value.getDate());
                }
            }

            // 気象データのMapをListに変換してGDS形式の気象データに設定する。
            List<GdsElementDto> elementList = new ArrayList<GdsElementDto>();
            for (GdsElementDto gdsElement : elementMap.values()) {
                elementList.add(gdsElement);
            }
            gdsData.put("elementDataList", elementList);

            // 気象データをファイルに出力する。
            FileWriter fw = null;
            String fileName = null;
            try {
                String fileData = this.getInputFileData(ResourceUtil.getProperties("cligen.properties").getProperty("cligen.gdsTemplate"), gdsData);
                fileName = TmpFileUtils.getWorkDir(requestId) + "/" + data.getStation().getId() + ".GDS";
                fw = new FileWriter(fileName);
                fw.write(fileData);
                fw.flush();
            } catch (IORuntimeException | IOException | TemplateException e) {
                // 気象データのファイル生成エラー
                log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(fileName), e);
                return null;
            } finally {
                if (fw != null) {
                    try {
                        fw.close();
                    } catch (IOException e) {
                        // ローカルファイルクローズエラー
                        log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(fileName), e);
                    }
                }
            }

            stationIdList.add(data.getStation().getId());
        }

        return stationIdList;
    }

    /**
     * 出力ファイル名からステーションIDを取得する。
     * @param outFile 出力ファイル
     * @return ステーションID
     */
    private String getStationId(File outFile) {
        return outFile.getName().substring(0, outFile.getName().lastIndexOf("."));
    }

    /**
     * 指定したエレメントIDが返却する気候データか判定する。
     * 生成する気候データが空の場合は、全てのエレメントIDを出力する。
     * @param resultClimateIdList 生成する気候データのリスト
     * @param elementId エレメントID
     * @return 判定結果
     */
    private boolean isResultElement(List<String> resultClimateIdList, String elementId) {
        if (resultClimateIdList != null && resultClimateIdList.size() > 0 && !resultClimateIdList.contains(elementId)) {
            // 返却しない気象データ
            return false;
        }

        // 返却する気象データ
        return true;
    }

    // WeatherGeneratorインターフェースを実装したメソッド
    /**
     * 気象データ生成ライブラリに必要な気象データのエレメントIDリストを取得する。
     * プロパティファイルに以下のフォーマットで定義する。
     *  - エレメントID + "." + サブエレメントID + "," + エレメントID + "." + サブエレメントID
     * ※","以降をエレメントID数繰り返す。
     * [例]
     *  airtemperature.Min,airtemperature.Max,rain.Total
     * @return エレメントIDリスト
     */
    @Override
    public LinkedHashMap<String, List<String>> getElementIdList() {
        LinkedHashMap<String, List<String>> result = new LinkedHashMap<String, List<String>>();

        Properties property = ResourceUtil.getProperties("cligen.properties");
        String elementIdListStr = property.getProperty("cligen.elementIdList");
        if (!StringUtils.isEmpty(elementIdListStr)) {
            String[] elementIdList = elementIdListStr.split(",");
            for (String elementId : elementIdList) {
                String[] idAndSubId = elementId.split("\\.");
                List<String> subIdList = result.get(idAndSubId[0]);
                if (subIdList == null) {
                    subIdList = new ArrayList<String>();
                    result.put(idAndSubId[0], subIdList);
                }
                if (idAndSubId.length > 1) {
                    subIdList.add(idAndSubId[1]);
                }
            }
        }

        return result;
    }

    /**
     * リクエストIDを設定する。
     * @param requestId リクエストID
     */
    @Override
    public void setRequestId(String requestId) {
        this.requestId = requestId;
    }

    /**
     * 気象データを設定する。
     * MetXML形式Stationsをルートにしたデータで、観測点識別情報と気象データを含む。
     * @param weatherData 気象データ
     */
    @Override
    public void setWeatherData(Dataset weatherData) {
        this.weatherData = weatherData;
    }

    /**
     * パラメータを設定する。
     * @param parameter パラメータ
     */
    @Override
    public void setParameter(Map<String, Object> parameter) {
        this.parameter = parameter;
    }

    /**
     * データ生成処理を実行する。
     * @return 実行結果
     */
    @Override
    public boolean generateData() {
        // データ生成ライブラリ処理開始ログ
        log.info(ErrorCode.START_LIB.getErrorMess(this.requestId));

        try {
            // ワークディレクトリを作成する。
            TmpFileUtils.createWorkDir(requestId);

            // 受取ったデータ確認のためMetXML形式のデータオブジェクトをファイルに出力する。
            String metXmlFileName = TmpFileUtils.getWorkDir(this.requestId) + "/MetXML_dataset.xml";
            MetXmlUtils.marshalMetXmlFile(weatherData, metXmlFileName);

            // 入力ファイルを生成する。
            List<String> stationIdList = this.createInputFile();
            if (stationIdList == null) {
                // 気象データの入力ファイルの生成エラー
                return false;
            }

            // コマンドとコマンド引数を取得
            Properties property = ResourceUtil.getProperties("cligen.properties");
            String commandStr = property.getProperty("cligen.command");
            String startYear = parameter.get("start_year").toString();
            String numberOfYears = (String) parameter.get("num_of_year").toString();
            log.info("実行コマンド=" + commandStr);
            log.info("開始年=" + startYear);
            log.info("年数=" + numberOfYears);

            for (String stationId : stationIdList) {
                // プロセス生成準備
                ProcessBuilder pb = new ProcessBuilder(commandStr, stationId, startYear, numberOfYears);
                pb.directory(new File(TmpFileUtils.getWorkDir(this.requestId)));
                File logFile = new File(TmpFileUtils.getWorkDir(this.requestId) + "/command.log");
                pb.redirectErrorStream(true);
                pb.redirectOutput(Redirect.appendTo(logFile));
                try {
                    // コマンド実行
                    Process process = pb.start();
                    process.waitFor();
                    int exitValue = process.exitValue();
                    log.info(ErrorCode.PROCESS_EXIT_VALUE.getErrorMess(this.requestId, exitValue));
                    if (exitValue != 0) {
                        // コマンド実行エラー
                        log.error(ErrorCode.PROCESS_EXECUTE_ERROR.getErrorMess(this.requestId));
                        return false;
                    }
                } catch (IOException e) {
                    // プロセス実行エラー
                    log.error(ErrorCode.PROCESS_EXECUTE_ERROR.getErrorMess(this.requestId), e);
                    return false;
                } catch (InterruptedException e) {
                    // プロセスの処理終了
                    log.error(ErrorCode.KILL_PROCESS.getErrorMess(this.requestId), e);
                    return false;
                }
            }

            return true;
        } finally {
            // データ生成ライブラリ処理終了ログ
            log.info(ErrorCode.END_LIB.getErrorMess(this.requestId));
        }
    }

    /**
     * 結果データを取得する。
     * @return 結果データ
     */
    @Override
    public Dataset getCreatedData() {

        // パラメータから返却対象の気象データを取得する。
        List<String> resultClimateIdList = (List<String>) parameter.get("climate_id");

        // 返却用のデータセット
        Dataset dataset = new Dataset();

        File[] outFileList  = TmpFileUtils.serchFileList(requestId, ".out");
        for (File outFile : outFileList) {
            if (outFile.isFile()) {
                FileReader fr = null;
                BufferedReader br = null;
                try {
                    fr = new FileReader(outFile);
                    br = new BufferedReader(fr);

                    String lineStr = br.readLine();
                    List<CligenOutElementDto> outElementList = new ArrayList<CligenOutElementDto>();
                    for (int count = 1; lineStr != null; count++, lineStr = br.readLine()) {
                        if (count < DAILY_DATA_START_LINE) {
                            // 日別データの開始行（16行目）までは読み飛ばす
                            continue;
                        }

                        if (lineStr.length() < DAILY_DATA_STRING_SIZE) {
                            // 日別データ行の桁数（69桁）より短い行は読み飛ばす
                            continue;
                        }

                        // データ部分を取得
                        outElementList.add(new CligenOutElementDto(lineStr));
                    }

                    // ファイル名からステーションIDを取得する。
                    String stationId = this.getStationId(outFile);

                    // ステーションIDに該当するステーションを取得する。
                    Station station = MetXmlUtils.serchStation(this.weatherData, stationId);

                    // ステーションIDに該当するステーションの親のリージョンを取得する。
                    Region region = MetXmlUtils.serchRegion(this.weatherData, stationId);

                    // 返却用のデータ
                    Data data = new Data();

                    // データソース情報の設定(生成ライブラリ名のCligenを設定する)
                    data.setSource(MetXmlUtils.createSource("Cligen", "Cligen"));

                    if (region != null) {
                        // リージョン情報の設定
                        data.setRegion(new Region());
                        data.getRegion().setId(region.getId());
                        data.getRegion().setName(region.getName());
                    }

                    // ステーション情報の設定
                    data.setStation(MetXmlUtils.createStation(station.getId(), station.getName(), station.getPlace()));

                    // 期間の設定
                    data.setInterval(new Interval());
                    data.getInterval().setStart(outElementList.get(0).getDateStr()); // 観測日（最初の年月日）
                    data.getInterval().setEnd(outElementList.get(outElementList.size() - 1).getDateStr()); // 観測日(最後の年月日)

                    // 時間幅の設定
                    data.setDuration(new Duration());
                    data.getDuration().setId("daily");
                    data.getDuration().setName(new Name());
                    data.getDuration().getName().setLang("ja");
                    data.getDuration().getName().setContent("日別値");

                    Subelement rainTotal = null;
                    Subelement rainDuration = null;
                    if (this.isResultElement(resultClimateIdList, "rain")) {
                        // 降水量（mm）、降雨継続時間（h）の設定
                        Element rain = MetXmlUtils.createElement("rain", "雨量");
                        data.getElement().add(rain);

                        rainTotal = MetXmlUtils.createSubelement("Total", "mm", "合計");
                        rain.getSubelement().add(rainTotal);

                        rainDuration = MetXmlUtils.createSubelement("RainDuration", "h", "降雨継続時間");
                        rain.getSubelement().add(rainDuration);
                    }

                    Subelement maxTemp = null;
                    Subelement minTemp = null;
                    if (this.isResultElement(resultClimateIdList, "airtemperature")) {
                        // 最高気温（℃）、最低気温（℃）の設定
                        Element temp = MetXmlUtils.createElement("airtemperature", "気温");
                        data.getElement().add(temp);

                        maxTemp = MetXmlUtils.createSubelement("Max", "℃", "最高");
                        temp.getSubelement().add(maxTemp);

                        minTemp = MetXmlUtils.createSubelement("Min", "℃", "最低");
                        temp.getSubelement().add(minTemp);
                    }

                    Subelement windSpeed = null;
                    Subelement windDirection = null;
                    if (this.isResultElement(resultClimateIdList, "wind")) {
                        // 風速（m/s）、風向（Deg）の設定
                        Element wind = MetXmlUtils.createElement("wind", "風");
                        data.getElement().add(wind);

                        windSpeed = MetXmlUtils.createSubelement("Speed", "m/s", "風速");
                        wind.getSubelement().add(windSpeed);

                        windDirection = MetXmlUtils.createSubelement("Direction", "degrees", "風向");
                        wind.getSubelement().add(windDirection);
                    }

                    Subelement solarRadiation = null;
                    if (this.isResultElement(resultClimateIdList, "radiation")) {
                        // 日射量（Langleys/day）の設定
                        Element radiation = MetXmlUtils.createElement("radiation", "日射");
                        data.getElement().add(radiation);

                        solarRadiation = MetXmlUtils.createSubelement("SolarRadiation", "Langleys/day", "日射量");
                        radiation.getSubelement().add(solarRadiation);
                    }

                    for (CligenOutElementDto outElement : outElementList) {

                        if (rainTotal != null) {
                            rainTotal.getValue().add(outElement.getRainTotalValue()); // 降水量（mm）
                        }
                        if (rainDuration != null) {
                            rainDuration.getValue().add(outElement.getRainDurationValue()); // 降雨継続時間（h）
                        }
                        if (maxTemp != null) {
                            maxTemp.getValue().add(outElement.getMaxTempValue()); // 最高気温（℃）
                        }
                        if (minTemp != null) {
                            minTemp.getValue().add(outElement.getMinTempValue()); // 最低気温（℃）
                        }
                        if (windSpeed != null) {
                            windSpeed.getValue().add(outElement.getWindSpeedValue()); // 風速（m/s）
                        }
                        if (windDirection != null) {
                            windDirection.getValue().add(outElement.getWindDirectionValue()); // 風向（Deg）
                        }
                        if (solarRadiation != null) {
                            solarRadiation.getValue().add(outElement.getSolarRadiationValue()); // 日射量（Langleys/day）
                        }
                    }

                    dataset.getData().add(data);
                } catch (IOException e) {
                    // ローカルファイルアクセスエラー
                    log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(outFile.getName()), e);
                } finally {
                    if (fr != null) {
                        try {
                            fr.close();
                        } catch (IOException e) {
                            // ローカルファイルクローズエラー
                            log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(outFile.getName()), e);
                        }
                    }
                }
            }
        }

        return dataset;
    }

    /**
     * 一時結果データを削除する。
     * @return 削除結果
     */
    @Override
    public boolean deleteDataFile() {
        String tempFileDelete = ResourceUtil.getProperties("cligen.properties").getProperty("cligen.tempFile.delete");
        if ("true".equals(tempFileDelete)) {
            // 一時結果ファイルの削除フラグがtrueの場合は、一時ファイルのディレクトリを削除する。
            return TmpFileUtils.deleteWorkDir(requestId);
        }

        // 一時結果ファイルの削除フラグがtrue以外の場合は、削除せずに削除成功(true)を返却する。
        return true;
    }
}
