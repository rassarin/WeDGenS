package jp.go.affrc.naro.wgs.util;

import java.io.File;
import java.io.IOException;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FSDataInputStream;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.FileUtil;
import org.apache.hadoop.fs.Path;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

/**
 * HDFSファイルユーティリティクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class HdfsFileUtils {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(HdfsFileUtils.class);

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private HdfsFileUtils() {
    }

    /**
     * ステーションデータファイルのパス名を取得する。
     * @param sourceId データソースID
     * @return ステーションデータファイルのパス名
     */
    public static String getStationListFilePath(String sourceId) {
        String path = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.stationlist.path");
        String fileName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.stationlist.fileName");
        return path + "/" + sourceId + "/" + fileName;
    }

    /**
     * 気象データファイルのパス名を取得する。
     * @param sourceId データソースID
     * @param regionId リージョンID
     * @param stationId ステーションID
     * @param year 年
     * @param elementId エレメントID
     * @param durationId デュレーションID
     * @return 気象データファイルのパス名
     */
    public static String getWeatherDataFilePath(
            String sourceId, String regionId, String stationId,
            String year, String elementId, String durationId) {
        String path = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.stationlist.path");

        if (regionId != null) {
            // リージョンが設定されるデータソースの場合
            return path + "/" + sourceId + "/" + regionId + "/" + stationId + "/" + year + "/" + elementId + "." + durationId;
        } else {
            // リージョンが設定されたいないデータソースの場合
            return path + "/" + sourceId + "/" + stationId + "/" + year + "/" + elementId + "." + durationId;
        }
    }

    /**
     * 気象データファイルのパス名を取得する。
     * @param user ユーザデータID
     * @return 気象データファイルのパス名
     */
    public static String getUserWeatherDataFilePath(String user) {
        String path = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.userData.path");
        String fileName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.userData.fileName");
        return path + "/" + user + "/" + fileName;
    }

    /**
     * 結果データファイルのパス名を取得する。
     * @param requestId リクエストID
     * @return 結果データファイルのパス名
     */
    public static String getResultDataFilePath(String requestId) {
        String path = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.resultData.path");
        String fileName = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.hdfs.resultData.fileName");
        return path + "/" + requestId + "/" + fileName;
    }

    /**
     * HDFSファイルをローカルにコピーする。
     * @param hdfsFileName HDFSファイル名
     * @param localFileName ローカルファイル名
     * @return 処理結果
     */
    public static boolean copyHdfsToLocal(String hdfsFileName, String localFileName) {
        boolean result = true;

        FSDataInputStream fsdIn = null;
        try {
            // HDFSファイルのオープン
            Configuration conf = new Configuration();
            Path hdfsPath = new Path(hdfsFileName);
            FileSystem fs = hdfsPath.getFileSystem(conf);

            // HDFSファイルから読込みローカルファイルへ書込む。
            result = FileUtil.copy(fs, hdfsPath, new File(localFileName), false, conf);
        } catch (IOException e) {
            // HDFSファイルアクセスエラー
            log.error(ErrorCode.HDFS_ACCESS_ERROR.getErrorMess(hdfsFileName), e);
            result = false;
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
}
