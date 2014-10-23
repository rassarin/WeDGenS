package jp.go.affrc.naro.wgs.service;

import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import javax.annotation.Resource;
import javax.servlet.ServletContext;

import jp.go.affrc.narc.metxml.Data;
import jp.go.affrc.narc.metxml.Dataset;
import jp.go.affrc.narc.metxml.Duration;
import jp.go.affrc.narc.metxml.Element;
import jp.go.affrc.narc.metxml.Interval;
import jp.go.affrc.narc.metxml.Name;
import jp.go.affrc.narc.metxml.Region;
import jp.go.affrc.narc.metxml.Source;
import jp.go.affrc.narc.metxml.Station;
import jp.go.affrc.narc.metxml.Stations;
import jp.go.affrc.narc.metxml.Subelement;
import jp.go.affrc.naro.wgs.dto.MetXmlStationDto;
import jp.go.affrc.naro.wgs.dto.WgsLibInfoDto;
import jp.go.affrc.naro.wgs.entity.common.TAvailableGenerator;
import jp.go.affrc.naro.wgs.entity.common.TAvailableLib;
import jp.go.affrc.naro.wgs.entity.common.TExecuteLog;
import jp.go.affrc.naro.wgs.entity.common.TRequest;
import jp.go.affrc.naro.wgs.form.CreateDataForm;
import jp.go.affrc.naro.wgs.service.dao.TAvailableGeneratorService;
import jp.go.affrc.naro.wgs.service.dao.TAvailableLibService;
import jp.go.affrc.naro.wgs.service.dao.TRequestService;
import jp.go.affrc.naro.wgs.task.CreateDataTask;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import jp.go.affrc.naro.wgs.util.HdfsFileUtils;
import jp.go.affrc.naro.wgs.util.RequestStatus;
import jp.go.affrc.naro.wgs.util.weatherData.MetSequenceUtils;
import jp.go.affrc.naro.wgs.util.weatherData.MetXmlUtils;
import jp.go.affrc.naro.wgs.util.wgslib.WeatherGenerator;
import net.agmodel.weatherData.MetSequence;
import net.arnx.jsonic.JSON;

import org.apache.commons.lang.StringUtils;
import org.seasar.chronos.core.Scheduler;
import org.seasar.extension.jdbc.JdbcManager;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

/**
 * データ生成サービスクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DeleteDataAction.java 1267 2014-02-13 07:58:20Z watabe $
 */
public class CreateDataService {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(CreateDataService.class);

    /** JSON応答時のcontentType。 */
    public static final String JSON_CONTENT_TYPE = "application/json";

    /** 一時結果データファイル名。 */
    public static final String RESULT_DATA_TMP_FILE_NAME = "ResultData.dat";

    /** スケジュールクラス。 */
    @Resource
    public Scheduler scheduler;

    /** Servletコンテキスト。 */
    @Resource
    public ServletContext application;

    /** JDBCマネージャ。 */
    @Resource
    protected JdbcManager jdbcManager;

    /** リクエストサービス。 */
    @Resource
    protected TRequestService tRequestService;

    /** 利用可能ジェネレータサービス。 */
    @Resource
    protected TAvailableGeneratorService tAvailableGeneratorService;

    /** 利用可能ライブラリサービス。 */
    @Resource
    protected TAvailableLibService tAvailableLibService;


    // データ生成サービス固有処理。
    /**
     * リクエストが本サーバで処理可能かチェックする。
     * @param requestId リクエストID
     * @return チェック結果
     */
    private ErrorCode checkRequest(String requestId) {
        TRequest tRequest = tRequestService.findById(requestId);
        if (tRequest == null) {
            // リクエストIDに該当するリクエスト情報がテーブルに登録されていない。
            log.warn(ErrorCode.REQUEST_NOT_FOUND.getErrorMess(requestId));
            this.executeLog(requestId, ErrorCode.REQUEST_NOT_FOUND, requestId);
            return ErrorCode.REQUEST_NOT_FOUND;
        }

        TAvailableGenerator tAvailableGenerator = tAvailableGeneratorService.findById(tRequest.generatorId);
        if (tAvailableGenerator == null) {
            // 利用可能ジェネレータなし。
            log.warn(ErrorCode.GENERATOR_NOT_FOUND.getErrorMess(tRequest.generatorId));
            this.executeLog(requestId, ErrorCode.GENERATOR_NOT_FOUND, tRequest.generatorId);
            return ErrorCode.GENERATOR_NOT_FOUND;
        }

        String ipAddr = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.ip_addr");
        String contextName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.context_name");
        if (ipAddr == null || !ipAddr.equals(tAvailableGenerator.ipAddr)) {
            // ジェネレータIPアドレスが違う
            log.warn(ErrorCode.GENERATOR_NOT_FOUND.getErrorMess(tRequest.generatorId));
            this.executeLog(requestId, ErrorCode.GENERATOR_NOT_FOUND, tRequest.generatorId);
            return ErrorCode.GENERATOR_NOT_FOUND;
        }

        if (contextName == null || !contextName.equals(tAvailableGenerator.contextName)) {
            // WebAPI コンテキスト名が違う
            log.warn(ErrorCode.GENERATOR_NOT_FOUND.getErrorMess(tRequest.generatorId));
            this.executeLog(requestId, ErrorCode.GENERATOR_NOT_FOUND, tRequest.generatorId);
            return ErrorCode.GENERATOR_NOT_FOUND;
        }

        TAvailableLib tAvailableLib = tAvailableLibService.findById(tAvailableGenerator.libId);
        if (tAvailableLib == null) {
            // 利用可能ライブラリなし。
            log.warn(ErrorCode.LIB_NOT_FOUND.getErrorMess(tAvailableGenerator.libId));
            this.executeLog(requestId, ErrorCode.LIB_NOT_FOUND, tAvailableGenerator.libId);
            return ErrorCode.LIB_NOT_FOUND;
        }

        WgsLibInfoDto wgsLibInfo = WgsLibInfoDto.findWgsLibInfoByLibName(tAvailableLib.libName);
        if (wgsLibInfo == null) {
            // 利用可能ライブラリなし。
            log.warn(ErrorCode.LIB_NOT_FOUND.getErrorMess(tAvailableGenerator.libId));
            this.executeLog(requestId, ErrorCode.LIB_NOT_FOUND, tAvailableGenerator.libId);
            return ErrorCode.LIB_NOT_FOUND;
        }

        // 該当リクエスト情報の処理可能
        return ErrorCode.REQUEST_NORMAL_END;
    }

    /**
     * デュレーションIDからデュレーション名（日本語名）を取得する。
     * @param durationId デュレーションID
     * @return デュレーション名（日本語名）
     */
    private Name getDurationName(String durationId) {
        Name name = null;
        if ("daily".equals(durationId)) {
            // 日別値
            name = new Name();
            name.setLang("ja");
            name.setContent("日別値");
        } else if ("hourly".equals(durationId)) {
            // 時別値
            name = new Name();
            name.setLang("ja");
            name.setContent("時別値");
        }
        return name;
    }

    /**
     * 入力気象データの観測年のリストを取得する。
     * @param paramData リクエスト情報の入力データの情報
     * @return 観測年のリスト
     */
    private List<String> getYearList(Map<String, Object> paramData) {
        List<String> yearList = new ArrayList<String>();

        String beginYearStr = (String) paramData.get("begin_year");
        String endYearStr = (String) paramData.get("end_year");

        if (StringUtils.isEmpty(beginYearStr) || StringUtils.isEmpty(endYearStr)) {
            // 観測年開始、観測年終了の何れかが未設定
            // 観測年を特定できないためエラー
            log.warn(ErrorCode.DATA_YEAR_ERROR.getErrorMess(beginYearStr, endYearStr));
            return null;
        }

        int beginYear = Integer.parseInt(beginYearStr);
        int endYear = Integer.parseInt(endYearStr);
        if (endYear < beginYear) {
            // 観測年開始と観測年終了の前後が逆転している。
            // 観測年を特定できないためエラー
            log.warn(ErrorCode.DATA_YEAR_ERROR.getErrorMess(beginYearStr, endYearStr));
            return null;
        }

        // 観測年開始から観測年終了までの文字列のリストを生成する。
        for (int year = beginYear; year <= endYear; year++) {
            yearList.add(String.valueOf(year));
        }

        return yearList;
    }

    /**
     * 地点種別が地点(point)の場合の気象データ読込み処理。
     * MetBrokerデータ(MetSequence形式)を示すデータリソースID、リージョンID、ステーションIDが
     * 入力データの情報として設定される。
     * データリソースID、リージョンID、ステーションIDを元にHDFSに登録されているステーションデータファイル
     * から観測点識別情報を取得する。
     * データリソースID、リージョンID、ステーションIDを元にHDFSに登録されている気象データファイル
     * から気象データを取得する。
     * ※データソースによってリージョンIDがない場合があるため、リージョンIDがない場合はリージョンIDなしの
     * Datasetオブジェクトを生成して返却する。
     * @param paramData リクエスト情報の入力データの情報
     * @param elementIdList 気象データを取得するエレメントIDのリスト
     * @return 気象データ(MetXML形式のDatasetオブジェクト)
     */
    private Dataset loadPointWeatherData(Map<String, Object> paramData, LinkedHashMap<String, List<String>> elementIdList) {
        Dataset dataset = new Dataset();

        List<String> yearList = this.getYearList(paramData);
        if (yearList == null || yearList.isEmpty()) {
            // 観測年の取得エラー
            // 観測年開始、観測年終了をログに出力するため、getYearListメソッド内でログを出力する。
            return null;
        }
        String durationId = (String) paramData.get("duration");
        String sourceId = (String) paramData.get("source_id");

        // データリソースIDに該当するステーションデータリストをHDFSから取得する。
        Stations stations = (Stations) MetXmlUtils.unmarshalMetXmlHdfsFile(HdfsFileUtils.getStationListFilePath(sourceId));
        if (stations == null) {
            // ステーションデータリストの取得エラー
            log.error(ErrorCode.STATION_LIST_FILE_NOT_FOUND.getErrorMess(sourceId));
            return null;
        }

        // ステーションデータファイルは1データリソースから取得したデータであるため、先頭のデータリソースを取得する。
        Source source = (Source) stations.getSource().get(0);

        List<Map<String, Object>> pointList = (List<Map<String, Object>>) paramData.get("point");
        for (Map<String, Object> point : pointList) {
            String regionId = (String) point.get("region_id");
            String stationId = (String) point.get("station_id");

            Region region = null;
            Station station = null;
            if (!StringUtils.isEmpty(regionId)) {
                // リージョンリストからリージョンIDに該当するリージョンを取得する。
                region = MetXmlUtils.serchRegion(source.getRegion(), regionId);
                if (region == null) {
                    // リージョンの取得エラー
                    log.error(ErrorCode.REGION_NOT_FOUND.getErrorMess(regionId));
                    return null;
                }

                // リージョンからステーションIDに｣該当するステーションを取得する。
                station = MetXmlUtils.serchStation(region.getStation(), stationId);
            } else {
                // データリソースからステーションIDに｣該当するステーションを取得する。
                station = MetXmlUtils.serchStation(source.getStation(), stationId);
            }

            if (station == null) {
                // ステーションの取得エラー
                log.error(ErrorCode.STATION_NOT_FOUND.getErrorMess(stationId));
                return null;
            }

            // 返却用のデータを生成する。
            Data resultData = new Data();
            dataset.getData().add(resultData);

            // 返却用のデータリソースを生成する。
            Source resultSource = new Source();
            resultSource.setId(source.getId());
            resultSource.setName(source.getName());
            resultData.setSource(resultSource);

            if (region != null) {
                // 返却用のリージョンを生成する。
                Region resultRegion = new Region();
                resultRegion.setId(region.getId());
                resultRegion.setName(region.getName());
                resultData.setRegion(resultRegion);
            }

            // 返却用のステーションを生成する。
            Station resultStation = new Station();
            resultStation.setId(station.getId());
            resultStation.setName(station.getName());
            resultStation.setPlace(station.getPlace());
            resultData.setStation(resultStation);

            // 返却用のデュレーションを生成する。
            Duration resultDuration = new Duration();
            resultDuration.setId(durationId);
            resultDuration.setName(this.getDurationName(durationId));
            resultData.setDuration(resultDuration);

            for (String year : yearList) {
                for (String elementId : elementIdList.keySet()) {
                    // データソースID、リージョンID、ステーションID、年、エレメントID、デュレーションID
                    // に該当する気象データをHDFSから取得する。
                    MetSequence metSequence = MetSequenceUtils.readMetSequenceHdfsFile(HdfsFileUtils.getWeatherDataFilePath(
                            sourceId, regionId, stationId, year, elementId, durationId));
                    if (metSequence == null) {
                        // 気象データの取得エラー
                        // 一部の気象データが取得できなくても処理可能なデータ生成ライブラリがあるため、
                        // 気象データの取得処理は終了せずに継続する。
                        log.info(ErrorCode.WEATHER_DATA_FILE_NOT_FOUND.getErrorMess(sourceId, regionId, stationId, year, elementId, durationId));
                        continue;
                    }
                    Element element = MetSequenceUtils.convertMetXml(metSequence);

                    // 返却用のエレメント
                    Element resultElement = MetXmlUtils.serchElement(resultData.getElement(), elementId);
                    if (resultElement == null) {
                        resultElement = new Element();
                        resultElement.setId(element.getId());
                        resultElement.setName(element.getName());
                        resultData.getElement().add(resultElement);
                    }

                    for (String subelementId : elementIdList.get(elementId)) {
                        // 気象データからサブエレメントを取得する。
                        Subelement subelement = MetXmlUtils.serchSubelement(element.getSubelement(), subelementId);
                        if (subelement == null) {
                            // 気象データに該当するサブエレメントなし
                            // 一部の気象データが取得できなくても処理可能なデータ生成ライブラリがあるため、
                            // 気象データの取得処理は終了せずに継続する。
                            log.info(ErrorCode.SUBELEMENT_NOT_FOUND.getErrorMess(elementId, subelementId));
                            continue;
                        }

                        // 返却用のサブエレメント
                        Subelement resultSubelement = MetXmlUtils.serchSubelement(resultElement.getSubelement(), subelementId);
                        if (resultSubelement == null) {
                            resultSubelement = new Subelement();
                            resultSubelement.setId(subelement.getId());
                            resultSubelement.setUnit(subelement.getUnit());
                            resultSubelement.setName(subelement.getName());
                            resultElement.getSubelement().add(resultSubelement);
                        }

                        // 気象データを返却用のサブエレメントに追加する。
                        resultSubelement.getValue().addAll(subelement.getValue());

                        // 返却用のインターバルを生成する。
                        Interval resultInterval = resultData.getInterval();
                        if (resultInterval == null) {
                            resultInterval = new Interval();
                            resultData.setInterval(resultInterval);
                        }
                        resultInterval.setStart(MetXmlUtils.serchStartDay(subelement.getValue(), resultInterval.getStart()));
                        resultInterval.setEnd(MetXmlUtils.serchEndDay(subelement.getValue(), resultInterval.getEnd()));
                    }
                }
            }
        }

        return dataset;
    }

    /**
     * 地点種別が矩形範囲(area)の場合の気象データ読込み処理。
     * MetBrokerデータ(MetSequence形式)を示す北西緯度、北西経度、南東緯度、南東経度が
     * 入力データの情報として設定される。
     * データリソースID、北西緯度、北西経度、南東緯度、南東経度を元にHDFSに登録されているステーションデータファイル
     * から観測点識別情報を取得する。
     * 取得した観測点識別情報からデータリソースID、リージョンID、ステーションIDを取得する。
     * 観測点識別情報のデータリソースID、リージョンID、ステーションIDを元にHDFSに登録されている気象データファイル
     * から気象データを取得する。
     * @param paramData リクエスト情報の入力データの情報
     * @param elementIdList 気象データを取得するエレメントIDのリスト
     * @return 気象データ(MetXML形式のDatasetオブジェクト)
     */
    private Dataset loadAreaWeatherData(Map<String, Object> paramData, LinkedHashMap<String, List<String>> elementIdList) {

        String sourceId = (String) paramData.get("source_id");

        Map<String, Object> area = (Map<String, Object>) paramData.get("area");

        double northwestLat = Double.parseDouble((String) area.get("nw_lat"));
        double northwestLon = Double.parseDouble((String) area.get("nw_lon"));
        double southeastLat = Double.parseDouble((String) area.get("se_lat"));
        double southeastLon = Double.parseDouble((String) area.get("se_lon"));

        // データリソースIDに該当するステーションデータリストをHDFSから取得する。
        Stations stations = (Stations) MetXmlUtils.unmarshalMetXmlHdfsFile(HdfsFileUtils.getStationListFilePath(sourceId));
        if (stations == null) {
            // ステーションデータリストの取得エラー
            log.error(ErrorCode.STATION_LIST_FILE_NOT_FOUND.getErrorMess(sourceId));
            return null;
        }

        //ステーションデータのMetXML要素のオブジェクトから、矩形エリア内のステーション情報リストを取得する。
        List<MetXmlStationDto> metXmlStationList = MetXmlUtils.serchStation(stations, northwestLat, northwestLon, southeastLat, southeastLon);

        // ステーション情報リストのリージョンID、ステーションIDを元に、地点種別が地点(point)の場合のパラメータを追加する。
        List<Map<String, Object>> pointList = new ArrayList<Map<String, Object>>();
        for (MetXmlStationDto metXmlStation : metXmlStationList) {
            Map<String, Object> point = new HashMap<String, Object>();
            if (metXmlStation.getRegionId() != null) {
                point.put("region_id", metXmlStation.getRegionId());
            }
            point.put("station_id", metXmlStation.getStationId());
            pointList.add(point);
        }
        paramData.put("point", pointList);

        return this.loadPointWeatherData(paramData, elementIdList);
    }

    /**
     * 地点種別がユーザ投入MetXML(user)の場合の気象データ読込み処理。
     * MetBrokerデータ(MetXML形式)を示すユーザデータIDが入力データの情報として設定される。
     * ユーザデータIDを元にHDFSに登録されている気象データファイルから気象データを取得する。
     * @param paramData リクエスト情報の入力データの情報
     * @return 気象データ(MetXML形式のDatasetオブジェクト)
     */
    private Dataset loadUserWeatherData(Map<String, Object> paramData) {

        // ユーザデータIDを取得する。
        String user = (String) paramData.get("user");

        // ユーザデータIDに該当する気象データをHDFSから取得する。
        return (Dataset) MetXmlUtils.unmarshalMetXmlHdfsFile(HdfsFileUtils.getUserWeatherDataFilePath(user));
    }

    /**
     * リクエスト情報に登録されているパラメータの入力データの情報を元に気象データをHDFSから読み込む。
     * 地点種別によってファイルの形式やファイル分割単位などの違いがあるが、
     * 何れの場合もMetXML形式のDatasetをルートにしたオブジェクトに加工して返却する。
     * @param paramData リクエスト情報の入力データの情報
     * @param elementIdList 気象データを取得するエレメントIDのリスト
     * @return 気象データ(MetXML形式のDatasetオブジェクト)
     */
    private Dataset loadWeatherData(Map<String, Object> paramData, LinkedHashMap<String, List<String>> elementIdList) {
        Dataset dataset = null;

        String rangeType = (String) paramData.get("range_type");
        if ("point".equals(rangeType)) {
            // 地点種別:地点
            dataset = this.loadPointWeatherData(paramData, elementIdList);
        } else if ("area".equals(rangeType)) {
            // 地点種別:矩形範囲
            dataset = this.loadAreaWeatherData(paramData, elementIdList);
        } else if ("mesh".equals(rangeType)) {
            // 地点種別:メッシュ
            // TODO:地点種別がメッシュの処理を実装する。
            log.error(ErrorCode.RANGE_TYPE_ERROR.getErrorMess(rangeType));
        } else if ("user".equals(rangeType)) {
            // 地点種別:ユーザ投入MetXML
            dataset = this.loadUserWeatherData(paramData);
        } else {
            // 該当する地点種別なし
            log.error(ErrorCode.RANGE_TYPE_ERROR.getErrorMess(rangeType));
        }
        return dataset;
    }

    // サービスが提供する機能のメソッド。
    /**
     * データ要求処理を実行する。
     * @param createDataForm データ生成フォーム
     * @return 処理結果
     */
    public ErrorCode createRequest(CreateDataForm createDataForm) {
        // リクエスト内容をチェックする。
        ErrorCode errorCode = checkRequest(createDataForm.requestId);
        if (errorCode != ErrorCode.REQUEST_NORMAL_END) {
            // リクエスト情報エラー。
            return errorCode;
        }

        // タスク起動処理
        boolean result = scheduler.addTask(CreateDataTask.class);
        if (!result) {
            // タスク追加失敗。
            // タスクに追加できなくても現在実行中のタスクがリクエスト処理を行うため、ログのみ出力して応答は成功を返却する。
            log.warn(ErrorCode.ADD_TASK_ERROR.getErrorMess(createDataForm.requestId));
            this.executeLog(createDataForm.requestId, ErrorCode.ADD_TASK_ERROR, createDataForm.requestId);
        }

        // データ生成リクエスト受付け成功。
        log.info(ErrorCode.REQUEST_NORMAL_END.getErrorMess(createDataForm.requestId));
        this.executeLog(createDataForm.requestId, ErrorCode.REQUEST_NORMAL_END, createDataForm.requestId);
        return ErrorCode.REQUEST_NORMAL_END;
    }

    /**
     * リクエスト情報検索処理。
     * 受付け済みのリクエスト情報の中から、結果データの生成状況が未生成のリクエスト情報を1件取得する。
     * @return リクエスト情報
     */
    public TRequest selectRequest() {
        String ipAddr = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.ip_addr");
        String contextName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.context_name");
        synchronized (CreateDataService.class) {
            // ジェネレータIPアドレス、WebAPI コンテキスト名、リクエストステータスがリクエスト受付
            // を条件にしてリクエスト情報テーブルを検索する。
            List<TRequest> tRequestList = jdbcManager
                    .from(TRequest.class)
                    .innerJoin("TAvailableGenerator",
                            "t2_.ip_addr = ? and t2_.context_name = ?",
                            ipAddr,
                            contextName)
                    .where("request_status_id = ?",
                           "ACCEPT")
                    .getResultList();
            if (tRequestList.size() > 0) {
                // 該当するリクエスト情報あり
                // 該当するリクエスト情報の先頭の1件のリクエストステータスを実行中に更新する。
                TRequest tRequest = tRequestList.get(0);
                if (updateExecutedRequestStatus(tRequest) != ErrorCode.CREATE_NORMAL_END) {
                    // 実行中に更新失敗
                    log.info(ErrorCode.DB_ACCESS_ERROR.getErrorMess(TRequest.class.getSimpleName()));
                    this.executeLog(tRequest.requestId, ErrorCode.DB_ACCESS_ERROR, tRequest.requestId);
                    return null;
                }

                // 該当するリクエスト情報の先頭の1件を返却する。
                return tRequest;
            }

            // 該当するリクエスト情報なし
            return null;
        }
    }

    /**
     * リクエスト情報件数検索処理。
     * 受付け済みのリクエスト情報の中から、結果データの生成状況が未生成のリクエスト情報の件数を取得する。
     * @return リクエスト情報件数
     */
    public long checkRequestCount() {
        String ipAddr = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.ip_addr");
        String contextName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.context_name");
        synchronized (CreateDataService.class) {
            // ジェネレータIPアドレス、WebAPI コンテキスト名、リクエストステータスがリクエスト受付
            // を条件にしてリクエスト情報テーブルを検索する。
            long count = jdbcManager
                    .from(TRequest.class)
                    .innerJoin("TAvailableGenerator",
                            "t2_.ip_addr = ? and t2_.context_name = ?",
                            ipAddr,
                            contextName)
                    .where("request_status_id = ?",
                           "ACCEPT")
                    .getCount();
            return count;
        }
    }

    /**
     * リクエスト情報から処理を行うライブラリ情報を取得する。
     * @param tRequest リクエスト情報
     * @return ライブラリ情報
     */
    public WgsLibInfoDto selectLibInfo(TRequest tRequest) {
        List<TAvailableGenerator> tAvailableGeneratorList = jdbcManager
                .from(TAvailableGenerator.class)
                .innerJoin("TAvailableLib")
                .where("generator_id = ?", tRequest.generatorId)
                .getResultList();
        if (tAvailableGeneratorList.size() == 0) {
            // 利用可能ジェネレータなし。
            log.warn(ErrorCode.GENERATOR_NOT_FOUND.getErrorMess(tRequest.generatorId));
            this.executeLog(tRequest.requestId, ErrorCode.GENERATOR_NOT_FOUND, tRequest.generatorId);
            return null;
        }
        TAvailableGenerator tAvailableGenerator = tAvailableGeneratorList.get(0);

        if (tAvailableGenerator.TAvailableLib == null) {
            // 利用可能ライブラリなし
            log.warn(ErrorCode.LIB_NOT_FOUND.getErrorMess(tAvailableGenerator.libId));
            this.executeLog(tRequest.requestId, ErrorCode.LIB_NOT_FOUND, tAvailableGenerator.libId);
            return null;
        }

        WgsLibInfoDto wgsLibInfo = WgsLibInfoDto.findWgsLibInfoByLibName(tAvailableGenerator.TAvailableLib.libName);
        if (wgsLibInfo == null) {
            // 利用可能ライブラリなし。
            log.warn(ErrorCode.LIB_NOT_FOUND.getErrorMess(tAvailableGenerator.libId));
            this.executeLog(tRequest.requestId, ErrorCode.LIB_NOT_FOUND, tAvailableGenerator.libId);
            return null;
        }

        // 該当するライブラリ情報あり
        return wgsLibInfo;
    }

    /**
     * ライブラリを使ったデータ生成処理を実行する。
     * @param wgsLibInfo ライブラリ情報
     * @param tRequest リクエスト情報
     * @return 処理結果コード
     */
    public ErrorCode executLib(WgsLibInfoDto wgsLibInfo, TRequest tRequest) {
        try {
            // 気象データ生成ライブラリのクラスを取得。
            WeatherGenerator wg = wgsLibInfo.getWeatherGenerator();

            // エレメントIDリストを取得する
            LinkedHashMap<String, List<String>> elementIdList = wg.getElementIdList();

            // リクエストIDを設定
            wg.setRequestId(tRequest.requestId);

            // リクエスト情報のJSON形式のパラメータを取得
            Map<String, Object> params = (Map<String, Object>) JSON.decode(tRequest.params);

            // 気象データをHDFSファイルから取得
            Dataset dataset = this.loadWeatherData((Map<String, Object>) params.get("data"), elementIdList);
            if (dataset == null) {
                // 気象データ取得エラー
                log.error(ErrorCode.WEATHER_DATA_ERROR.getErrorMess(tRequest.requestId));
                this.executeLog(tRequest.requestId, ErrorCode.WEATHER_DATA_ERROR, tRequest.requestId);
                return ErrorCode.HDFS_ACCESS_ERROR;
            }

            // 気象データを設定
            wg.setWeatherData(dataset);

            // パラメータを設定
            wg.setParameter((Map<String, Object>) params.get("params"));

            // データ生成処理を実行
            if (!wg.generateData()) {
                // データ生成処理失敗
                log.error(ErrorCode.GENERATOR_EXEC_ERROR.getErrorMess(tRequest.requestId));
                return ErrorCode.GENERATOR_EXEC_ERROR;
            }

            // 結果データを取得する。
            Dataset createdData = wg.getCreatedData();

            // 結果データをストレージ（HDFS）に登録する。
            String resultFileName = HdfsFileUtils.getResultDataFilePath(tRequest.requestId);
            MetXmlUtils.marshalMetXmlHdfsFile(createdData, resultFileName);

            // 一時結果データファイルを削除する。
            if (!wg.deleteDataFile()) {
                // 一時結果データファイルの削除エラー
                // 一時結果データファイルの削除が失敗しても結果データの登録がきているためエラーにしない。
                log.warn(ErrorCode.TEMP_FILE_DELETE_ERROR.getErrorMess(tRequest.requestId));
                this.executeLog(tRequest.requestId, ErrorCode.TEMP_FILE_DELETE_ERROR, tRequest.requestId);
            }

        } catch (InstantiationException | IllegalAccessException | ClassNotFoundException e) {
            // 気象データ生成ライブラリのクラスの取得エラー
            log.error(ErrorCode.GENERATOR_EXEC_ERROR.getErrorMess(tRequest.requestId), e);
            return ErrorCode.GENERATOR_EXEC_ERROR;
        }

        return ErrorCode.CREATE_NORMAL_END;
    }

    /**
     * リクエスト情報のリクエストステータスを実行中に更新する。
     * @param tRequest リクエスト情報
     * @return 処理結果コード
     */
    public ErrorCode updateExecutedRequestStatus(TRequest tRequest) {

        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報のリクエストステータスID、処理開始日時を更新する。
        tRequest.requestStatusId = RequestStatus.RUNNING.name();
        tRequest.executedAt = nowTimestamp;
        int count = jdbcManager
                .update(tRequest)
                .excludesNull()
                .execute();

        if (count == 0) {
            // 更新対象のリクエスト情報なし
            log.warn(ErrorCode.REQUEST_NOT_FOUND.getErrorMess(tRequest.requestId));
            this.executeLog(tRequest.requestId, ErrorCode.REQUEST_NOT_FOUND, tRequest.requestId);
            return ErrorCode.REQUEST_NOT_FOUND;
        }

        // リクエストステータスの更新成功
        return ErrorCode.CREATE_NORMAL_END;
    }

    /**
     * リクエスト情報のリクエストステータスを指定して処理結果で更新する。
     * @param tRequest リクエスト情報
     * @param requestStatus リクエストステータス
     * @return 処理結果コード
     */
    public ErrorCode updateFinishedRequestStatus(TRequest tRequest, RequestStatus requestStatus) {

        // 現在日時を取得する。
        Timestamp nowTimestamp = new Timestamp(System.currentTimeMillis());

        // リクエスト情報のリクエストステータスID、処理完了日時を更新する。
        tRequest.requestStatusId = requestStatus.name();
        tRequest.finishedAt = nowTimestamp;
        int count = jdbcManager
                .update(tRequest)
                .excludesNull()
                .execute();

        if (count == 0) {
            // 更新対象のリクエスト情報なし
            log.warn(ErrorCode.REQUEST_NOT_FOUND.getErrorMess(tRequest.requestId));
            this.executeLog(tRequest.requestId, ErrorCode.REQUEST_NOT_FOUND, tRequest.requestId);
            return ErrorCode.REQUEST_NOT_FOUND;
        }

        // リクエストステータスの更新成功
        return ErrorCode.CREATE_NORMAL_END;
    }

    /**
     * 実行ログをDBに出力する。
     * @param requestId リクエストID
     * @param errorCode エラーコード
     * @param args メッセージ置換文字列
     */
    public void executeLog(String requestId, ErrorCode errorCode, Object... args) {

        TExecuteLog tExecuteLog = new TExecuteLog();
        tExecuteLog.requestId = requestId;
        tExecuteLog.priority = errorCode.getPriority().getCode();
        tExecuteLog.code = new Integer(errorCode.getErrorCode());
        tExecuteLog.message = errorCode.getErrorMess(args);
        int count = jdbcManager
                .insert(tExecuteLog)
                .excludesNull()
                .execute();
        if (count == 0) {
            // 実行ログの登録失敗
            log.error(ErrorCode.EXECUTE_LOG_ERROR.getErrorMess(requestId));
        }

    }
}
