package jp.go.affrc.naro.wgs.util.weatherData;


/**
 * 座標（緯度、経度、標高）のユーティリティクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class LocationUtils {

    /** 分の数値に変換する場合の倍率 */
    private static final double MINUTES_SCALE_FACTOR = 600.0;

    /** 標高を変換する場合の倍率 */
    private static final double ALT_SCALE_FACTOR = 10.0;

    /** 赤道半径[km] */
    private static final double EQUATORIAL_RADIUS = 6378.137;

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private LocationUtils() {
    }

    /**
     * 度単位の実数から整数部分の度の文字列を取得する。
     * @param degrees 度単位の値
     * @return 整数部分の度の文字列
     */
    public static String getGdsDegrees(double degrees) {
        return Integer.toString((int) degrees);
    }

    /**
     * 度単位の実数から分の値を整数値の文字列で取得する。
     * @param degrees 度単位の値
     * @return 分の値の整数値の文字列
     */
    public static String getGdsMinutes(double degrees) {
        double decimal = degrees % 1.0;
        return Integer.toString((int) (decimal * MINUTES_SCALE_FACTOR));
    }

    /**
     * 度単位の実数から度分の文字列を取得する。
     * @param degrees 度単位の値
     * @return 度分の文字列
     */
    public static String getGdsDegreesMinutes(double degrees) {
        return LocationUtils.getGdsDegrees(degrees) + LocationUtils.getGdsMinutes(degrees);
    }

    /**
     * 標高の実数から整数部と小数部1桁の小数点を含まない文字列を取得する。
     * @param alt 標高の実数
     * @return 小数部1桁の小数点を含まない文字列
     */
    public static String getGdsAlt(double alt) {
        return Integer.toString((int) (alt * ALT_SCALE_FACTOR));
    }

    /**
     * 緯度経度から2点間の距離(km)を取得する。
     * @param lat1 緯度1
     * @param lon1 経度1
     * @param lat2 緯度2
     * @param lon2 経度2
     * @return 2点間の距離(km)
     */
    public static double getDistance(double lat1, double lon1, double lat2, double lon2) {
        double latRg1 = Math.toRadians(lat1);
        double lonRg1 = Math.toRadians(lon1);

        double latRg2 = Math.toRadians(lat2);
        double lonRg2 = Math.toRadians(lon2);

        // 2点間の距離[km]
        return EQUATORIAL_RADIUS * Math.acos(Math.sin(latRg1) * Math.sin(latRg2) + Math.cos(latRg1) * Math.cos(latRg2) * Math.cos(lonRg2 - lonRg1));
    }

}
