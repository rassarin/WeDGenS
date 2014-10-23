package jp.go.affrc.naro.wgs.dto;

import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import jp.go.affrc.naro.wgs.util.wgslib.WeatherGenerator;

import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;
import org.seasar.framework.util.ResourceUtil;

/**
 * データ生成ライブラリ情報DTOクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "wgsLibInfoDto")
public class WgsLibInfoDto {
    /** ライブラリ数のプロパティキー */
    private static final String KEY_LIB_SIZE = "wgs-lib.list.size";

    /** ライブラリ名のプロパティキー */
    private static final String KEY_LIB_NAME = "wgs-lib.list.libName";

    /** ライブラリクラス名のプロパティキー */
    private static final String KEY_LIB_CLASS = "wgs-lib.list.libClass";

    /** ライブラリID */
    public String libId = "";

    /** ライブラリ名 */
    public String libName = "";

    /** ライブラリクラス名 */
    public String libClass = "";

    /**
     * 気象データ生成ライブラリの実装クラスを取得する。
     * @return 気象データ生成ライブラリ
     * @throws InstantiationException インスタンス生成エラー
     * @throws IllegalAccessException アクセス権限不正
     * @throws ClassNotFoundException クラスロードエラー
     */
    public WeatherGenerator getWeatherGenerator() throws InstantiationException, IllegalAccessException, ClassNotFoundException {
        return (WeatherGenerator) Class.forName(libClass).newInstance();
    }

    /**
     * データ生成ライブラリ情報リスト取得。
     * @return データ生成ライブラリ情報リスト
     */
    public static List<WgsLibInfoDto> getWgsLibInfoList() {

        List<WgsLibInfoDto> result = new ArrayList<WgsLibInfoDto>();

        Properties property = ResourceUtil.getProperties("wgs-lib.properties");
        String libSize = property.getProperty(KEY_LIB_SIZE, "0");
        int size = Integer.parseInt(libSize);

        for (int count = 1; count <= size; count++) {
            WgsLibInfoDto wsgLibInfo = new WgsLibInfoDto();
            wsgLibInfo.libId = String.valueOf(count);
            wsgLibInfo.libName = property.getProperty(KEY_LIB_NAME + count);
            wsgLibInfo.libClass = property.getProperty(KEY_LIB_CLASS + count);
            result.add(wsgLibInfo);
        }

        return result;
    }

    /**
     * データ生成ライブラリ情報を取得する。
     * @param libId ライブラリID
     * @return データ生成ライブラリ情報
     */
    public static WgsLibInfoDto findWgsLibInfo(String libId) {
        List<WgsLibInfoDto> wgsLibInfoList = WgsLibInfoDto.getWgsLibInfoList();

        for (WgsLibInfoDto wgsLibInfo : wgsLibInfoList) {
            if (wgsLibInfo.libId != null && wgsLibInfo.libId.equals(libId)) {
                // 該当するライブラリ情報を取得
                return wgsLibInfo;
            }
        }

        // 該当するライブラリ情報なし
        return null;
    }

    /**
     * データ生成ライブラリ情報を取得する。
     * @param libName ライブラリ名
     * @return データ生成ライブラリ情報
     */
    public static WgsLibInfoDto findWgsLibInfoByLibName(String libName) {
        List<WgsLibInfoDto> wgsLibInfoList = WgsLibInfoDto.getWgsLibInfoList();

        for (WgsLibInfoDto wgsLibInfo : wgsLibInfoList) {
            if (wgsLibInfo.libName != null && wgsLibInfo.libName.equals(libName)) {
                // 該当するライブラリ情報を取得
                return wgsLibInfo;
            }
        }

        // 該当するライブラリ情報なし
        return null;
    }
}
