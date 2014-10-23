package jp.go.affrc.naro.wgs.util.wgslib;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import jp.go.affrc.narc.metxml.Dataset;

/**
 * 気象データ生成ライブラリインターフェース。
 * 気象データ生成用のライブラリは、非同期用のタスクから実行するために以下の機能を実装する。
 * - エレメントIDリストを取得
 *   ・気象データ生成ライブラリに必要な気象データのエレメントIDリストを取得する。
 * - リクエストIDを設定
 *   ・気象データ生成のリクエストIDを設定する。
 * - 気象データを設定
 *   ・観測点の情報と気象データをMetXML要素のオブジェクトで受取る。
 *   ・MetXML要素のオブジェクトはStationsをルートにした形式で受取る。
 *   ※観測点の情報と気象データが別ファイルで保管されたいた場合でも、呼び出し側で1つに結合したオブジェクトを生成する。
 * - パラメータを設定
 *   ・気象データ以外に必要な気象データ生成ライブラリ用の処理パラメータを設定する。
 *   ※JSON形式でリクエスト情報に登録されているテキストを、呼び出し側でデコードしてMapオブジェクトを生成する。
 * - データ生成処理を実行
 *   ・MetXML要素のオブジェクトを気象データ生成ライブラリ用のパラメータに変換する。
 *   ・観測点識別情報、気象データ、処理パラメータを気象データ生成ライブラリに渡してデータ生成処理を実行する。
 *   ・気象データ生成ライブラリが複数同時実行できない場合は、この処理の中で排他制御する。
 * - 結果データを取得
 *   ・データ生成処理で生成した結果データをMetXML要素のオブジェクトに変換する。
 *   ・データ生成処理で一時結果データファイルを生成する場合は、一時結果データファイルからMetXML要素のオブジェクトに変換する。
 * - 一時結果データファイルを削除
 *   ・データ生成処理で一時結果データファイルを生成する場合は、一時結果データファイルを削除する。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public interface WeatherGenerator {

    /**
     * 気象データ生成ライブラリに必要な気象データのエレメントIDリストを取得する。
     * LinkedHashMapに以下の構成で値を設定する。
     * - key = エレメントID
     * - value = サブエレメントIDリスト
     * @return エレメントIDリスト
     */
    LinkedHashMap<String, List<String>> getElementIdList();

    /**
     * リクエストIDを設定する。
     * @param requestId リクエストID
     */
    void setRequestId(String requestId);

    /**
     * 気象データを設定する。
     * MetXML形式Stationsをルートにしたデータで、観測点識別情報と気象データを含む。
     * @param weatherData 気象データ
     */
    void setWeatherData(Dataset weatherData);

    /**
     * パラメータを設定する。
     * @param parameter パラメータ
     */
    void setParameter(Map<String, Object> parameter);

    /**
     * データ生成処理を実行する。
     * @return 実行結果
     */
    boolean generateData();

    /**
     * 結果データを取得する。
     * @return 結果データ
     */
    Dataset getCreatedData();

    /**
     * 一時結果データを削除する。
     * @return 削除結果
     */
    boolean deleteDataFile();
}
