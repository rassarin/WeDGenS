package jp.go.affrc.naro.wgs.util.weatherData;

import java.io.File;
import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;

import jp.go.affrc.narc.metxml.Data;
import jp.go.affrc.narc.metxml.Dataset;
import jp.go.affrc.narc.metxml.Element;
import jp.go.affrc.narc.metxml.Name;
import jp.go.affrc.narc.metxml.Place;
import jp.go.affrc.narc.metxml.Region;
import jp.go.affrc.narc.metxml.Source;
import jp.go.affrc.narc.metxml.Station;
import jp.go.affrc.narc.metxml.Stations;
import jp.go.affrc.narc.metxml.Subelement;
import jp.go.affrc.narc.metxml.Value;
import jp.go.affrc.naro.wgs.dto.MetXmlStationDto;
import jp.go.affrc.naro.wgs.dto.MetXmlSubelementDto;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import net.agmodel.physical.GeographicalBox;
import net.agmodel.physical.Location2D;

import org.apache.commons.lang.StringUtils;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FSDataInputStream;
import org.apache.hadoop.fs.FSDataOutputStream;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

/**
 * MetXMLユーティリティクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class MetXmlUtils {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(MetXmlUtils.class);

    /** マーシャリング／アンマーシャリングで使用するJAXBコンテキストのクラスパス */
    private static final String JAXB_CONTEXT_PATH = "jp.go.affrc.narc.metxml";

    /** MetXMLのxsdファイルのURLのプロパティキー */
    private static final String KEY_METXML_XSD_URL = "wgs-backend.metxml.xsd.url";

    /** エレメントIDに対応するエレメント名（日本語）の定義 */
    private static final String[][] ELEMENT_NAME_LIST = {
        {"airtemperature", "気温"},
        {"brightsunlight", "日照"},
        {"rain", "雨量"},
        {"wind", "風"}
    };

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private MetXmlUtils() {
    }

    // ========== MetXMLオブジェクトの生成関連処理 ==========
    /**
     * エレメントIDに該当するエレメント名（日本語）を取得する。
     * @param elementId エレメントID
     * @return エレメント名（日本語）
     */
    public static String getElementName(String elementId) {
        for (String[] element : ELEMENT_NAME_LIST) {
            if (element[0].equals(elementId)) {
                return element[1];
            }
        }

        // 該当するエレメント名なし
        return null;
    }

    /**
     * データソースを生成する。
     * @param id データソースID
     * @param content データソース名
     * @return データソース
     */
    public static Source createSource(String id, String content) {
        Source source = new Source();
        source.setId(id);
        source.setName(new Name());
        source.getName().setLang("ja");
        source.getName().setContent(content);
        return source;
    }

    /**
     * ステーションを生成する。
     * @param id ステーションID
     * @param name ステーション名
     * @param place 座標位置データ
     * @return ステーション
     */
    public static Station createStation(String id, Name name, Place place) {
        Station station = new Station();
        station.setId(id);
        station.setName(name);
        station.setPlace(place);
        return station;
    }

    /**
     * エレメントを生成する。
     * @param id エレメントID
     * @param content エレメント名
     * @return エレメント
     */
    public static Element createElement(String id, String content) {
        Element element = new Element();
        element.setId(id);
        element.setName(new Name());
        element.getName().setLang("ja");
        element.getName().setContent(content);
        return element;
    }

    /**
     * サブエレメントを生成する。
     * @param id サブエレメントID
     * @param unit 単位
     * @param content サブエレメント名
     * @return サブエレメント
     */
    public static Subelement createSubelement(String id, String unit, String content) {
        Subelement subelement = new Subelement();
        subelement.setId(id);
        subelement.setUnit(unit);
        subelement.setName(new Name());
        subelement.getName().setLang("ja");
        subelement.getName().setContent(content);
        return subelement;
    }


    // ========== MetXMLオブジェクトのファイルアクセス処理 ==========
    // ---------- ローカルファイルアクセス処理 ----------
    /**
     * MetXMLフォーマットのファイルを読み込んでJavaObjectを生成する。
     * @param metXmlFileName MetXMLファイル名
     * @return MetXML要素のオブジェクト
     */
    public static Object unmarshalMetXmlFile(String metXmlFileName) {
        Object data = null;
        try {
            // JAXBのアンマーシャリング用クラス生成
            JAXBContext context = JAXBContext.newInstance(JAXB_CONTEXT_PATH);
            Unmarshaller unmarshaller = context.createUnmarshaller();

            // ローカルファイルからMetXML要素のオブジェクトを読込み
            data = unmarshaller.unmarshal(new File(metXmlFileName));
        } catch (JAXBException e) {
            // ローカルファイルアクセスエラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(metXmlFileName), e);
        }
        return data;
    }

    /**
     * MetXML要素のオブジェクトをMetXMLフォーマットのファイルに出力する。
     * @param obj MetXML要素のオブジェクト
     * @param metXmlFileName MetXMLファイル名
     * @return 出力結果
     */
    public static boolean marshalMetXmlFile(Object obj, String metXmlFileName) {
        boolean result = true;
        try {
            // MetXMLのxsdファイルのURLをプロパティファイルから取得する。
            String metxmlXsdUrl = ResourceUtil.getProperties("wgs-backend.properties").getProperty(KEY_METXML_XSD_URL);

            // JAXBのマーシャリング用クラス生成
            JAXBContext context = JAXBContext.newInstance(JAXB_CONTEXT_PATH);
            Marshaller marshaller = context.createMarshaller();
            marshaller.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
            marshaller.setProperty(Marshaller.JAXB_NO_NAMESPACE_SCHEMA_LOCATION, metxmlXsdUrl);

            // ローカルファイルにMetXMLを書き込む
            marshaller.marshal(obj, new File(metXmlFileName));
        } catch (JAXBException e) {
            // ローカルファイルアクセスエラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(metXmlFileName), e);
            result = false;
        }
        return result;
    }

    // ---------- HDFSファイルアクセス処理 ----------
    /**
     * MetXMLフォーマットのHDFSファイルを読み込んでJavaObjectを生成する。
     * @param metXmlHdfsFileName MetXMLファイル名(HDFSファイル)
     * @return MetXML要素のオブジェクト
     */
    public static Object unmarshalMetXmlHdfsFile(String metXmlHdfsFileName) {
        Object data = null;
        FSDataInputStream fsdIn = null;
        try {
            // JAXBのアンマーシャリング用クラス生成
            JAXBContext context = JAXBContext.newInstance(JAXB_CONTEXT_PATH);
            Unmarshaller unmarshaller = context.createUnmarshaller();

            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(metXmlHdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            fsdIn = fs.open(hdfsPath);

            // HDFSファイルからMetXML要素のオブジェクトを読込み
            data = unmarshaller.unmarshal(fsdIn);
        } catch (JAXBException | IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(metXmlHdfsFileName), e);
        } finally {
            if (fsdIn != null) {
                try {
                    fsdIn.close();
                } catch (IOException e) {
                    // HDFSファイルクローズエラー
                    log.warn(ErrorCode.HDFS_CLOSE_ERROR.getErrorMess(metXmlHdfsFileName), e);
                }
            }
        }
        return data;
    }

    /**
     * MetXML要素のオブジェクトをMetXMLフォーマットのHDFSファイルに出力する。
     * @param obj MetXML要素のオブジェクト
     * @param metXmlHdfsFileName MetXMLファイル名(HDFSファイル)
     * @return 出力結果
     */
    public static boolean marshalMetXmlHdfsFile(Object obj, String metXmlHdfsFileName) {
        boolean result = true;
        FSDataOutputStream fsdOut = null;
        try {
            // MetXMLのxsdファイルのURLをプロパティファイルから取得する。
            String metxmlXsdUrl = ResourceUtil.getProperties("wgs-backend.properties").getProperty(KEY_METXML_XSD_URL);

            // JAXBのマーシャリング用クラス生成
            JAXBContext context = JAXBContext.newInstance(JAXB_CONTEXT_PATH);
            Marshaller marshaller = context.createMarshaller();
            marshaller.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
            marshaller.setProperty(Marshaller.JAXB_NO_NAMESPACE_SCHEMA_LOCATION, metxmlXsdUrl);

            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(metXmlHdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            fs.setVerifyChecksum(true);
            fsdOut = fs.create(hdfsPath);

            // HDFSファイルにMetXMLを書き込む
            marshaller.marshal(obj, fsdOut);
        } catch (JAXBException | IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(metXmlHdfsFileName), e);
            result = false;
        } finally {
            if (fsdOut != null) {
                try {
                    fsdOut.close();
                } catch (IOException e) {
                    // HDFSファイルクローズエラー
                    log.warn(ErrorCode.HDFS_CLOSE_ERROR.getErrorMess(metXmlHdfsFileName), e);
                }
            }
        }
        return result;
    }

    /**
     * HDFSファイルを削除する。
     * @param hdfsFileName HDFSファイル名
     * @return 削除結果
     */
    public static boolean deleteHdfsFile(String hdfsFileName) {
        boolean result = true;
        try {
            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(hdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);
            result = fs.delete(hdfsPath, true);
        } catch (IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(hdfsFileName), e);
            result = false;
        }
        return result;
    }


    // ========== MetXMLオブジェクトの検索処理 ==========
    // ---------- 気象データ検索処理 ----------
    /**
     * データセットから、ステーションIDに該当する気象データを取得する。
     * @param dataset データセット
     * @param stationId ステーションID
     * @return 気象データ
     */
    public static Data serchData(Dataset dataset, String stationId) {
        for (Data data : dataset.getData()) {
            if (data.getStation() != null && data.getStation().getId() != null
                    && data.getStation().getId().equals(stationId)) {
                // ステーションIDが同じ気象データ
                return data;
            }
        }

        // 該当するサブエレメントなし
        return null;
    }


    // ---------- リージョン検索処理 ----------
    /**
     * リージョン情報のリストから、リージョンIDに該当するリージョン情報を取得する。
     * @param regionList リージョン情報のリスト
     * @param regionId リージョンID
     * @return リージョン情報
     */
    public static Region serchRegion(List<Region> regionList, String regionId) {
        for (Region region : regionList) {
            if (region.getId() != null && region.getId().equals(regionId)) {
                // リージョンIDが同じリージョン情報
                return region;
            }
        }

        // 該当するリージョンなし
        return null;
    }

    /**
     * データセット情報から、ステーションIDに該当するステーションの親のリージョン情報を取得する。
     * @param dataset データセット情報
     * @param stationId ステーションID
     * @return リージョン情報
     */
    public static Region serchRegion(Dataset dataset, String stationId) {

        for (Data data : dataset.getData()) {
            if (data.getStation() != null && data.getStation().getId() != null
                    && data.getStation().getId().equals(stationId)) {
                // ステーションIDが同じステーション情報
                // ステーションの親のリージョン情報を返却する。
                return data.getRegion();
            }
        }

        // 該当するステーションなし
        return null;
    }


    // ---------- ステーション検索処理 ----------
    /**
     * ステーションデータのMetXML要素のオブジェクトから、矩形エリア内のステーション情報リストを取得する。
     * ステーションIDは、以下の文字列に加工して返却する。
     *     "データソースID/リージョンID/ステーションID"
     * 緯度、経度がそれぞれ同じ値の場合は、矩形エリアの指定にならないためエラーとなる。
     * @param stations ステーションデータ
     * @param northwestLat 北西の緯度
     * @param northwestLon 北西の経度
     * @param southeastLat 南東の緯度
     * @param southeastLon 南東の経度
     * @return ステーション情報リスト
     */
    public static List<MetXmlStationDto> serchStation(
            Stations stations, double northwestLat, double northwestLon, double southeastLat, double southeastLon) {
        List<MetXmlStationDto> result = new ArrayList<MetXmlStationDto>();
        log.info("北西の緯度=" + northwestLat + "、北西の経度=" + northwestLon + "、南東の緯度=" + southeastLat + "、南東の経度=" + southeastLon);

        Location2D nw = new Location2D(northwestLat, northwestLon);
        Location2D se = new Location2D(southeastLat, southeastLon);
        GeographicalBox area = new GeographicalBox(nw, se);

        Source source = stations.getSource().get(0);
        List<Region> regionList = source.getRegion();
        log.info("リージョンサイズ=" + regionList.size());
        if (regionList.size() > 0) {
            // リージョンがある場合はリージョンからステーションを取得する。
            for (Region region : regionList) {
                List<Station> stationList = region.getStation();
                for (Station station : stationList) {
                    Place place = station.getPlace();
                    Location2D stationLocation = new Location2D(place.getLat(), place.getLon());
                    if (area.contains(stationLocation)) {
                        // 指定矩形エリアに含まれるステーション
                        MetXmlStationDto metXmlStation = new MetXmlStationDto();
                        metXmlStation.source = source;
                        metXmlStation.region = region;
                        metXmlStation.station = station;
                        log.info("ステーションID=" + station.getId() + "、緯度=" + place.getLat() + "、経度=" + place.getLon());
                        result.add(metXmlStation);
                    }
                }
            }
        } else {
            // リージョンがない場合はデータソースからステーションを取得する。
            List<Station> stationList = source.getStation();
            for (Station station : stationList) {
                Place place = station.getPlace();
                Location2D stationLocation = new Location2D(place.getLat(), place.getLon());
                if (area.contains(stationLocation)) {
                    // 指定矩形エリアに含まれるステーション
                    MetXmlStationDto metXmlStation = new MetXmlStationDto();
                    metXmlStation.source = source;
                    metXmlStation.region = null;
                    metXmlStation.station = station;
                    log.info("ステーションID=" + station.getId() + "、緯度=" + place.getLat() + "、経度=" + place.getLon());
                    result.add(metXmlStation);
                }
            }
        }

        return result;
    }

    /**
     * ステーション情報リストから、ステーションIDに該当するステーション情報を取得する。
     * @param stationList ステーション情報リスト
     * @param stationId ステーションID
     * @return ステーション情報
     */
    public static Station serchStation(List<Station> stationList, String stationId) {
        for (Station station : stationList) {
            if (station.getId() != null && station.getId().equals(stationId)) {
                // ステーションIDが同じステーション情報
                return station;
            }
        }

        // 該当するステーションなし
        return null;
    }

    /**
     * ステーションリスト情報から、ステーションIDに該当するステーション情報を取得する。
     * @param stations ステーションリスト情報
     * @param stationId ステーションID
     * @return ステーション情報
     */
    public static Station serchStation(Stations stations, String stationId) {
        Source source = stations.getSource().get(0);
        List<Region> regionList = source.getRegion();
        for (Region region : regionList) {
            List<Station> stationList = region.getStation();
            for (Station station : stationList) {
                if (station.getId() != null && station.getId().equals(stationId)) {
                    // ステーションIDが同じステーション情報
                    return station;
                }
            }
        }

        // 該当するステーションなし
        return null;
    }

    /**
     * データセット情報から、ステーションIDに該当するステーション情報を取得する。
     * @param dataset データセット情報
     * @param stationId ステーションID
     * @return ステーション情報
     */
    public static Station serchStation(Dataset dataset, String stationId) {

        for (Data data : dataset.getData()) {
            if (data.getStation() != null && data.getStation().getId() != null
                    && data.getStation().getId().equals(stationId)) {
                // ステーションIDが同じステーション情報
                return data.getStation();
            }
        }

        // 該当するステーションなし
        return null;
    }


    // ---------- エレメント検索処理 ----------
    /**
     * エレメント情報リストから、エレメントIDに該当するエレメント情報を取得する。
     * @param elementList エレメント情報リスト
     * @param elementId エレメントID
     * @return エレメント情報
     */
    public static Element serchElement(List<Element> elementList, String elementId) {
        for (Element element : elementList) {
            if (element.getId() != null && element.getId().equals(elementId)) {
                // エレメントIDが同じエレメント情報
                return element;
            }
        }

        // 該当するエレメントなし
        return null;
    }

    /**
     * 気象データから、エレメントIDに該当するエレメント情報を取得する。
     * @param data 気象データ
     * @param elementId エレメントID
     * @return エレメント情報
     */
    public static Element serchElement(Data data, String elementId) {
        return MetXmlUtils.serchElement(data.getElement(), elementId);
    }

    /**
     * 気象データから、エレメントIDに該当するエレメント名を取得する。
     * @param data 気象データ
     * @param elementId エレメントID
     * @return エレメント名
     */
    public static String serchElementName(Data data, String elementId) {
        Element element = MetXmlUtils.serchElement(data, elementId);
        if (element != null && element.getName() != null) {
            return element.getName().getContent();
        }

        // 該当するエレメントなし
        return null;
    }


    // ---------- サブエレメント検索処理 ----------
    /**
     * 気象データから、サブエレメント情報のリストを取得する。
     * @param data 気象データ
     * @return サブエレメント情報のリスト
     */
    public static List<MetXmlSubelementDto> serchSubelement(Data data) {
        List<MetXmlSubelementDto> result = new ArrayList<MetXmlSubelementDto>();
        for (Element element : data.getElement()) {
            for (Subelement subelement : element.getSubelement()) {
                MetXmlSubelementDto metXmlSubelement = new MetXmlSubelementDto();
                metXmlSubelement.station = data.getStation();
                metXmlSubelement.element = element;
                metXmlSubelement.subelement = subelement;
                result.add(metXmlSubelement);
            }
        }
        return result;
    }

    /**
     * サブエレメント情報リストから、サブエレメントIDに該当するサブエレメント情報を取得する。
     * @param subelementList サブエレメント情報リスト
     * @param subelementId サブエレメントID
     * @return サブエレメント情報
     */
    public static Subelement serchSubelement(List<Subelement> subelementList, String subelementId) {
        for (Subelement subelement : subelementList) {
            if (subelement.getId() != null && subelement.getId().equals(subelementId)) {
                // サブエレメントIDが同じサブエレメント情報
                return subelement;
            }
        }

        // 該当するサブエレメントなし
        return null;
    }

    /**
     * 気象データから、サブエレメントIDに該当するサブエレメント情報を取得する。
     * @param data 気象データ
     * @param subelementId サブエレメントID
     * @return サブエレメント情報
     */
    public static Subelement serchSubelement(Data data, String subelementId) {
        for (Element element : data.getElement()) {
            Subelement subelement = MetXmlUtils.serchSubelement(element.getSubelement(), subelementId);
            if (subelement != null) {
                // サブエレメントIDが同じサブエレメント情報
                return subelement;
            }
        }

        // 該当するサブエレメントなし
        return null;
    }

    /**
     * 気象データから、サブエレメントIDに該当するサブエレメントの単位を取得する。
     * @param data 気象データ
     * @param subelementId サブエレメントID
     * @return サブエレメントの単位
     */
    public static String serchSubelementUnit(Data data, String subelementId) {
        Subelement subelement = MetXmlUtils.serchSubelement(data, subelementId);
        if (subelement != null) {
            return subelement.getUnit();
        }

        // 該当するサブエレメントなし
        return null;
    }

    /**
     * 気象データから、サブエレメントIDに該当するサブエレメント名を取得する。
     * @param data 気象データ
     * @param subelementId サブエレメントID
     * @return サブエレメント名
     */
    public static String serchSubelementName(Data data, String subelementId) {
        Subelement subelement = MetXmlUtils.serchSubelement(data, subelementId);
        if (subelement != null && subelement.getName() != null) {
            return subelement.getName().getContent();
        }

        // 該当するサブエレメントなし
        return null;
    }


    // ---------- 気象データ観測値検索処理 ----------
    /**
     * 気象データのリストから、観測日の開始日付の文字列を取得する。
     * 引数で渡した開始日付が気象データの開始日付よりも前の場合は、引数の日付を返却する。
     * @param valueList 気象データのリスト
     * @param start 開始日付
     * @return 観測日の開始日付
     */
    public static String serchStartDay(List<Value> valueList, String start) {
        SimpleDateFormat format = new SimpleDateFormat("yyyy/M/d");
        Calendar startDay = null;
        if (!StringUtils.isEmpty(start)) {
            startDay = Calendar.getInstance();
            try {
                startDay.setTime(format.parse(start));
            } catch (ParseException e) {
                // 日付の変換エラー
                log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
                startDay = null;
            }
        }

        for (Value value : valueList) {
            if (value.getDate() != null) {
                Calendar targetDay = Calendar.getInstance();
                try {
                    targetDay.setTime(format.parse(value.getDate()));
                } catch (ParseException e) {
                    // 日付の変換エラー
                    log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
                    continue;
                }
                if (startDay == null || startDay.after(targetDay)) {
                    startDay = targetDay;
                }
            }
        }

        return format.format(startDay.getTime());
    }

    /**
     * 気象データのリストから、観測日の終了日付の文字列を取得する。
     * 引数で渡した終了日付が気象データの終了日付よりも後の場合は、引数の日付を返却する。
     * @param valueList 気象データのリスト
     * @param end 終了日付
     * @return 観測日の終了日付
     */
    public static String serchEndDay(List<Value> valueList, String end) {
        SimpleDateFormat format = new SimpleDateFormat("yyyy/M/d");
        Calendar endDay = null;
        if (!StringUtils.isEmpty(end)) {
            endDay = Calendar.getInstance();
            try {
                endDay.setTime(format.parse(end));
            } catch (ParseException e) {
                // 日付の変換エラー
                log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
                endDay = null;
            }
        }

        for (Value value : valueList) {
            if (value.getDate() != null) {
                Calendar targetDay = Calendar.getInstance();
                try {
                    targetDay.setTime(format.parse(value.getDate()));
                } catch (ParseException e) {
                    // 日付の変換エラー
                    log.warn(ErrorCode.PARSE_DATE_ERROR.getErrorMess(), e);
                    continue;
                }
                if (endDay == null || endDay.before(targetDay)) {
                    endDay = targetDay;
                }
            }
        }

        // 該当するサブエレメントなし
        return format.format(endDay.getTime());
    }

}
