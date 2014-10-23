package jp.go.affrc.naro.wgs.util.wgslib;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
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
import jp.go.affrc.naro.wgs.dto.CdfdmElementDto;
import jp.go.affrc.naro.wgs.dto.MetXmlSubelementDto;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import jp.go.affrc.naro.wgs.util.HdfsFileUtils;
import jp.go.affrc.naro.wgs.util.TmpFileUtils;
import jp.go.affrc.naro.wgs.util.weatherData.LocationUtils;
import jp.go.affrc.naro.wgs.util.weatherData.MetXmlUtils;

import org.apache.commons.lang.StringUtils;
import org.apache.hadoop.fs.FSDataInputStream;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.seasar.framework.exception.IORuntimeException;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

import freemarker.template.Configuration;
import freemarker.template.Template;
import freemarker.template.TemplateException;

/**
 * Cdfdmのライブラリを使った気象データ生成クラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public class Cdfdm implements WeatherGenerator {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(Cdfdm.class);

    /** 出力ファイルの日別データの開始行数 */
    private static final int DAILY_DATA_START_LINE = 2;

    /** 出力ファイルの日別データ行の桁数 */
    private static final int DAILY_DATA_STRING_SIZE = 24;

    /** ステーション座標データのデータ数 */
    private static final int INDEX_STATION_DATA_SIZE = 3;

    /** ステーション座標データのインデックス：ステーションID */
    private static final int INDEX_STATION_ID = 0;

    /** ステーション座標データのインデックス：ステーションの緯度 */
    private static final int INDEX_STATION_LAT = 1;

    /** ステーション座標データのインデックス：ステーションの経度 */
    private static final int INDEX_STATION_LON = 2;

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
     * ステーションID、エレメントID、サブエレメントIDからcdfdmの気象データの入力データファイル名を取得する。
     * @param stationId ステーションID
     * @param elementId エレメントID
     * @param subelementId サブエレメントID
     * @return cdfdmの気象データの入力データファイル名
     */
    private String getObsFileName(String stationId, String elementId, String subelementId) {
        String obsPath = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.obs.path");
        String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  elementId + "." + subelementId);
        return TmpFileUtils.getWorkDir(requestId) + "/" + obsPath + "/" + climateValueName + "_" + stationId + ".dat";
    }

    /**
     * コマンド実行に必要な入力ファイルを生成する。
     * @return サブエレメント情報のリスト
     */
    private List<MetXmlSubelementDto> createInputFile() {
        List<MetXmlSubelementDto> metXmlSubelementList = new ArrayList<MetXmlSubelementDto>();

        for (Data data : weatherData.getData()) {
            for (MetXmlSubelementDto metXmlSubelement : MetXmlUtils.serchSubelement(data)) {

                // MetXML形式をcdfdm入力ファイル形式に変換する。
                Map<String, Object> cdfdmData = new HashMap<String, Object>();

                // cdfdm入力ファイル形式の気象データ格納用に、日数分の空の気象データを生成する。
                LinkedHashMap<String, CdfdmElementDto> elementMap = CdfdmElementDto.createElementList(
                        data.getInterval().getStart(), data.getInterval().getEnd());

                // 気象データの観測値を取得して、該当する日付に設定する。
                for (Value value : metXmlSubelement.subelement.getValue()) {
                    CdfdmElementDto cdfdmElement = elementMap.get(value.getDate());
                    if (cdfdmElement != null) {
                        cdfdmElement.setMetXmlValue(value.getContent());
                    } else {
                        // 該当日付のデータなし
                        log.warn("気象データの観測値に該当日付のデータなし　日付=" + value.getDate());
                    }
                }

                // 気象データのMapをListに変換してGDS形式の気象データに設定する。
                List<CdfdmElementDto> elementList = new ArrayList<CdfdmElementDto>();
                for (CdfdmElementDto cdfdmElement : elementMap.values()) {
                    elementList.add(cdfdmElement);
                }
                cdfdmData.put("elementDataList", elementList);

                // 気象データをファイルに出力する。
                FileWriter fw = null;
                String fileName = null;
                try {
                    String fileData = this.getInputFileData(ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.observedDataTemplate"), cdfdmData);
                    fileName = this.getObsFileName(data.getStation().getId(), metXmlSubelement.getElementId(), metXmlSubelement.getSubelementId());
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

                metXmlSubelementList.add(metXmlSubelement);
            }
        }

        return metXmlSubelementList;
    }

    /**
     * コマンド実行に必要な入力ファイルのリストファイルを生成する。
     * @param metXmlSubelementList サブエレメント情報のリスト
     * @return 生成結果
     */
    private boolean createInputListFile(List<MetXmlSubelementDto> metXmlSubelementList) {
        boolean result = true;
        FileWriter fw = null;
        String fileName = null;
        try {
            fileName = TmpFileUtils.getWorkDir(requestId) + "/" + ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.listData.fileName");
            fw = new FileWriter(fileName);
            for (MetXmlSubelementDto metXmlSubelement : metXmlSubelementList) {
                String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  metXmlSubelement.getElementId() + "." + metXmlSubelement.getSubelementId());
                fw.write(metXmlSubelement.getStationId() + " " + climateValueName + "\n");
            }
            fw.flush();
        } catch (IORuntimeException | IOException e) {
            // リストファイルのファイル生成エラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(fileName), e);
            result = false;
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
        return result;
    }

    /**
     * 気象データのステーションに近い気候モデルのステーションのステーションIDを取得する。
     * @param modelId モデルID
     * @param subItems セット名
     * @param lat 緯度
     * @param lon 経度
     * @return 気候モデルのステーションID
     */
    private String getGcmStetionId(String modelId, String subItems, double lat, double lon) {
        String result = null;

        FSDataInputStream fsdIn = null;
        String hdfsFileName = null;
        try {
            String path = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.model.path");
            String fileName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.model.stationlist.fileName");

            // HDFSファイルのオープン
            org.apache.hadoop.conf.Configuration conf = new org.apache.hadoop.conf.Configuration();
            hdfsFileName = path + "/" + modelId + "/" + subItems + "/" + fileName;
            Path hdfsPath = new Path(hdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            fsdIn = fs.open(hdfsPath);
            BufferedReader br = new BufferedReader(new InputStreamReader(fsdIn));

            double minDistance = Double.MAX_VALUE;
            for (String lineStr = br.readLine(); lineStr != null; lineStr = br.readLine()) {
                String[] stationDataList = lineStr.split("[ ]++");
                if (stationDataList.length >= INDEX_STATION_DATA_SIZE) {
                    double distance = LocationUtils.getDistance(lat, lon, Double.parseDouble(stationDataList[INDEX_STATION_LAT]), Double.parseDouble(stationDataList[INDEX_STATION_LON]));

                    if (minDistance > distance) {
                        minDistance = distance;
                        result = stationDataList[INDEX_STATION_ID];
                    }
                }
            }

        } catch (IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(hdfsFileName), e);
        } finally {
            if (fsdIn != null) {
                try {
                    fsdIn.close();
                } catch (IOException e) {
                    // HDFSファイルクローズエラー
                    log.warn(ErrorCode.HDFS_CLOSE_ERROR.getErrorMess(hdfsFileName), e);
                }
            }
        }

        return result;
    }

    /**
     * モデルID、セット名、サブエレメント情報からcdfdmの気候モデルのHDFSファイル名を取得する。
     * @param modelId モデルID
     * @param subItems セット名
     * @param metXmlSubelement サブエレメント情報
     * @return cdfdmの気候モデルのHDFSファイル名
     */
    private String getGcmHdfsFileName(String modelId, String subItems, MetXmlSubelementDto metXmlSubelement) {
        String gcmStetionId = this.getGcmStetionId(modelId, subItems, metXmlSubelement.getLat(), metXmlSubelement.getLon());
        String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  metXmlSubelement.getElementId() + "." + metXmlSubelement.getSubelementId());
        String modelPath = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.model.path");
        return modelPath + "/" + modelId + "/" + subItems + "/" + climateValueName + "_" + gcmStetionId + ".dat";
    }

    /**
     * サブエレメント情報から気候モデルのローカルファイル名を取得する。
     * @param metXmlSubelement サブエレメント情報
     * @return 気候モデルのローカルファイル名
     */
    private String getGcmLocalFileName(MetXmlSubelementDto metXmlSubelement) {
        String workDir = TmpFileUtils.getWorkDir(requestId);
        String gcmPath = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.gcm.path");
        String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  metXmlSubelement.getElementId() + "." + metXmlSubelement.getSubelementId());
        return workDir + "/" + gcmPath + "/" + climateValueName + "_" + metXmlSubelement.getStationId() + ".dat";
    }

    /**
     * コマンド実行に必要なモデルデータの入力ファイルを生成する。
     * @param modelId モデルID
     * @param subItems セット名
     * @param metXmlSubelementList サブエレメント情報のリスト
     * @return 生成結果
     */
    private boolean createInputModelFile(String modelId, String subItems, List<MetXmlSubelementDto> metXmlSubelementList) {
        for (MetXmlSubelementDto metXmlSubelement : metXmlSubelementList) {
            String hdfsFileName = this.getGcmHdfsFileName(modelId, subItems, metXmlSubelement);
            String localFileName = this.getGcmLocalFileName(metXmlSubelement);
            boolean copyresult = HdfsFileUtils.copyHdfsToLocal(hdfsFileName, localFileName);
            if (!copyresult) {
                // 気候モデルの入力ファイル生成エラー
                log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(hdfsFileName + " > " + localFileName));
                return false;
            }
        }

        // 全ファイルのコピー成功。
        return true;
    }

    /**
     * 出力ファイル名からステーションIDを取得する。
     * 出力ファイル名は以下のフォーマットで出力される。
     *   気候データID + "_" + ステーションID + ".dat"
     * @param outFile 出力ファイル
     * @return ステーションID
     */
    private String getStationId(File outFile) {
        return outFile.getName().substring(outFile.getName().indexOf("_") + 1, outFile.getName().lastIndexOf("."));
    }

    /**
     * 出力ファイル名からエレメントIDを取得する。
     * @param outFile 出力ファイル
     * @return エレメントID
     */
    private String getElementId(File outFile) {
        String outFileClimateValueName = outFile.getName().substring(0, outFile.getName().indexOf("_"));
        if (StringUtils.isEmpty(outFileClimateValueName)) {
            // ファイル名から気候データIDが取得できない。
            return null;
        }

        LinkedHashMap<String, List<String>> elementIdList = this.getElementIdList();
        for (String elementId : elementIdList.keySet()) {
            List<String> subelementIdList = elementIdList.get(elementId);
            for (String subelementId : subelementIdList) {
                String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  elementId + "." + subelementId);
                if (outFileClimateValueName.equals(climateValueName)) {
                    // ファイル名の気候データIDに該当するエレメントIDあり。
                    return elementId;
                }
            }
        }

        // ファイル名の気候データIDに該当するエレメントIDなし。
        return null;
    }

    /**
     * 出力ファイル名からサブエレメントIDを取得する。
     * @param outFile 出力ファイル
     * @return サブエレメントID
     */
    private String getSubelementId(File outFile) {
        String outFileClimateValueName = outFile.getName().substring(0, outFile.getName().indexOf("_"));
        if (StringUtils.isEmpty(outFileClimateValueName)) {
            // ファイル名から気候データIDが取得できない。
            return null;
        }

        LinkedHashMap<String, List<String>> elementIdList = this.getElementIdList();
        for (String elementId : elementIdList.keySet()) {
            List<String> subelementIdList = elementIdList.get(elementId);
            for (String subelementId : subelementIdList) {
                String climateValueName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.gcm.element." +  elementId + "." + subelementId);
                if (outFileClimateValueName.equals(climateValueName)) {
                    // ファイル名の気候データIDに該当するサブエレメントIDあり。
                    return subelementId;
                }
            }
        }

        // ファイル名の気候データIDに該当するサブエレメントIDなし。
        return null;
    }

    // WeatherGeneratorインターフェースを実装したメソッド
    /**
     * 気象データ生成ライブラリに必要な気象データのエレメントIDリストを取得する。
     * プロパティファイルに以下のフォーマットで定義する。
     *  - エレメントID + "." + サブエレメントID + "," + エレメントID + "." + サブエレメントID
     * ※","以降をエレメントID数繰り返す。
     * [例]
     *  airtemperature.Min,airtemperature.Max,airtemperature.Ave,rain.Total,wind.Speed
     * @return エレメントIDリスト
     */
    @Override
    public LinkedHashMap<String, List<String>> getElementIdList() {
        LinkedHashMap<String, List<String>> result = new LinkedHashMap<String, List<String>>();

        Properties property = ResourceUtil.getProperties("cdfdm.properties");
        String elementIdListStr = property.getProperty("cdfdm.elementIdList");
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
            TmpFileUtils.createWorkDir(this.requestId);

            // 気候モデルのファイル格納ディレクトリをワークディレクトリに作成する。
            String gcmPath = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.gcm.path");
            TmpFileUtils.createWorkDir(this.requestId, gcmPath);

            // 気象データのファイル格納ディレクトリをワークディレクトリに作成する。
            String obsPath = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.obs.path");
            TmpFileUtils.createWorkDir(this.requestId, obsPath);

            // 受取ったデータ確認のためMetXML形式のデータオブジェクトをファイルに出力する。
            String metXmlFileName = TmpFileUtils.getWorkDir(this.requestId) + "/MetXML_dataset.xml";
            MetXmlUtils.marshalMetXmlFile(weatherData, metXmlFileName);

            // 気象データの入力ファイルを生成する。
            List<MetXmlSubelementDto> metXmlSubelementList = this.createInputFile();
            if (metXmlSubelementList == null) {
                // 気象データの入力ファイルの生成エラー
                return false;
            }

            // 入力ファイルのリストファイルを生成する。
            if (!this.createInputListFile(metXmlSubelementList)) {
                // 入力ファイルのリストファイルの生成エラー
                return false;
            }

            // モデルデータの入力ファイルを生成する。
            String modelId = (String) parameter.get("model_id");
            String subItems = (String) parameter.get("set_id");
            log.info("モデルID=" + modelId);
            log.info("セット名=" + subItems);
            if (!this.createInputModelFile(modelId, subItems, metXmlSubelementList)) {
                // モデルデータの入力ファイルの生成エラー
                return false;
            }

            // コマンドとコマンド引数を取得
            Properties property = ResourceUtil.getProperties("cdfdm.properties");
            String commandStr = property.getProperty("cdfdm.command");
            log.info("実行コマンド=" + commandStr);

            // プロセス生成準備
            ProcessBuilder pb = new ProcessBuilder(commandStr);
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
                // プロセスの強制終了
                log.error(ErrorCode.KILL_PROCESS.getErrorMess(this.requestId), e);
                return false;
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
        // 入力ファイルのリストファイル名を取得する。
        String listFileName = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.data.input.listData.fileName");

        // パラメータから返却対象の期間、気象データを取得する。
        String startYear = parameter.get("start_year").toString();
        String endYear = parameter.get("stop_year").toString();
        log.info("生成開始年=" + startYear);
        log.info("生成終了年=" + endYear);
        List<String> resultClimateIdList = (List<String>) parameter.get("climate_id");

        // 返却用のデータセット
        Dataset dataset = new Dataset();

        File[] outFileList  = TmpFileUtils.serchFileList(requestId, ".dat");
        for (File outFile : outFileList) {
            if (listFileName.equals(outFile.getName())) {
                // 入力ファイルのリストファイルは拡張子が".dat"でも対象外のためスキップする。
                continue;
            }

            if (resultClimateIdList != null && resultClimateIdList.size() > 0 && !resultClimateIdList.contains(this.getElementId(outFile))) {
                // 生成する気候データのパラメータがある場合に、エレメントIDが該当しないファイルはスキップする。
                continue;
            }

            if (outFile.isFile()) {
                FileReader fr = null;
                BufferedReader br = null;
                try {
                    fr = new FileReader(outFile);
                    br = new BufferedReader(fr);

                    String lineStr = br.readLine();
                    List<CdfdmElementDto> outElementList = new ArrayList<CdfdmElementDto>();
                    for (int count = 1; lineStr != null; count++, lineStr = br.readLine()) {
                        if (count < DAILY_DATA_START_LINE) {
                            // 日別データの開始行（2行目）までは読み飛ばす
                            continue;
                        }

                        if (lineStr.length() < DAILY_DATA_STRING_SIZE) {
                            // 日別データ行の桁数（24桁）より短い行は読み飛ばす
                            continue;
                        }

                        // データ部分を取得
                        outElementList.add(new CdfdmElementDto(lineStr));
                    }

                    // ファイル名からステーションIDを取得する。
                    String stationId = this.getStationId(outFile);

                    // ステーションIDに該当するステーションを取得する。
                    Station station = MetXmlUtils.serchStation(this.weatherData, stationId);

                    // ステーションIDに該当するステーションの親のリージョンを取得する。
                    Region region = MetXmlUtils.serchRegion(this.weatherData, stationId);

                    // ステーションIDに該当する入力気象データを取得する。
                    Data inputData = MetXmlUtils.serchData(this.weatherData, stationId);

                    // 返却用のデータ
                    Data data = MetXmlUtils.serchData(dataset, stationId);
                    if (data == null) {
                        // データセットにステーションIDに該当する気象データがない場合は生成する。
                        data = new Data();

                        // データソース情報の設定(生成ライブラリ名のCdfdmを設定する)
                        data.setSource(MetXmlUtils.createSource("Cdfdm", "Cdfdm"));

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

                        // 時間幅の設定
                        data.setDuration(new Duration());
                        data.getDuration().setId("daily");
                        data.getDuration().setName(new Name());
                        data.getDuration().getName().setLang("ja");
                        data.getDuration().getName().setContent("日別値");

                        dataset.getData().add(data);
                    }

                    // ファイル名からエレメントIDを取得する。
                    String elementId = this.getElementId(outFile);

                    // 気象データからエレメントIDに該当するエレメント情報を取得する。
                    Element element = MetXmlUtils.serchElement(data, elementId);
                    if (element == null) {
                        // エレメントIDに該当するエレメント情報がない場合は生成する。
                        element = MetXmlUtils.createElement(elementId, MetXmlUtils.serchElementName(inputData, elementId));
                        data.getElement().add(element);
                    }

                    // ファイル名からサブエレメントIDを取得する。
                    String subelementId = this.getSubelementId(outFile);

                    // 気象データからサブエレメントIDに該当するサブエレメント情報を取得する。
                    Subelement subelement = MetXmlUtils.serchSubelement(data, subelementId);
                    if (subelement == null) {
                        // サブエレメントIDに該当するサブエレメント情報がない場合は生成する。
                        subelement = MetXmlUtils.createSubelement(
                                subelementId,
                                MetXmlUtils.serchSubelementUnit(inputData, subelementId),
                                MetXmlUtils.serchSubelementName(inputData, subelementId));
                        element.getSubelement().add(subelement);
                    }

                    for (CdfdmElementDto outElement : outElementList) {
                        if (!outElement.isInPeriod(startYear, endYear)) {
                            // 返却期間に含まれない年のデータはスキップする。
                            continue;
                        }
                        subelement.getValue().add(outElement.getMetXmlValue());
                    }

                    // 期間の設定
                    data.getInterval().setStart(subelement.getValue().get(0).getDate()); // 観測日（最初の年月日）
                    data.getInterval().setEnd(subelement.getValue().get(subelement.getValue().size() - 1).getDate()); // 観測日(最後の年月日)
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
        String tempFileDelete = ResourceUtil.getProperties("cdfdm.properties").getProperty("cdfdm.tempFile.delete");
        if ("true".equals(tempFileDelete)) {
            // 一時結果ファイルの削除フラグがtrueの場合は、一時ファイルのディレクトリを削除する。
            return TmpFileUtils.deleteWorkDir(requestId);
        }

        // 一時結果ファイルの削除フラグがtrue以外の場合は、削除せずに削除成功(true)を返却する。
        return true;
    }
}
