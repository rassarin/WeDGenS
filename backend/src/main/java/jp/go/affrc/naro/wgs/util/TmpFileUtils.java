package jp.go.affrc.naro.wgs.util;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FilenameFilter;
import java.io.IOException;
import java.nio.channels.FileChannel;

import org.apache.commons.io.FileUtils;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

/**
 * 一時ファイルユーティリティクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DataSourceType.java 368 2014-02-14 12:47:03Z watabe $
 */
public final class TmpFileUtils {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(TmpFileUtils.class);

    /**
     * デフォルトコンストラクタ。
     * 本クラスは生成しないためprivateのコンストラクタを定義する。
     */
    private TmpFileUtils() {
    }

    /**
     * 一時結果データファイルのディレクトリ名を取得する。
     * @param requestId リクエストID
     * @return 一時結果データファイルのディレクトリ名
     */
    public static String getWorkDir(String requestId) {
        String tempPath = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.temp_path");
        return tempPath + "/" + requestId;
    }

    /**
     * 一時結果データファイルのディレクトリを生成する。
     * @param requestId リクエストID
     */
    public static void createWorkDir(String requestId) {
        String dirPath = TmpFileUtils.getWorkDir(requestId);
        File dirFile = new File(dirPath);
        if (!dirFile.exists()) {
            // ディレクトリ未作成のため、ディレクトリを作成する。
            dirFile.mkdir();
        }
    }

    /**
     * 一時結果データファイルのディレクトリに指定した相対パスのディレクトリを生成する。
     * @param requestId リクエストID
     * @param addPath 相対パスのディレクトリ
     */
    public static void createWorkDir(String requestId, String addPath) {
        String dirPath = TmpFileUtils.getWorkDir(requestId);
        File dirFile = new File(dirPath + "/" + addPath);
        if (!dirFile.exists()) {
            // ディレクトリ未作成のため、ディレクトリを作成する。
            dirFile.mkdirs();
        }
    }


    /**
     * 一時結果データファイルのディレクトリを削除する。
     * @param requestId リクエストID
     * @return 処理結果
     */
    public static boolean deleteWorkDir(String requestId) {
        String dirPath = TmpFileUtils.getWorkDir(requestId);
        try {
            File dirFile = new File(dirPath);
            if (!dirFile.exists()) {
                // ディレクトリ未作成。
                log.warn(ErrorCode.LOCAL_FILE_NOT_FOUND.getErrorMess(dirPath));
                return false;
            }

            // リクエストIDのディレクトリ配下を削除する。
            FileUtils.deleteDirectory(dirFile);
        } catch (IOException e) {
            // ディレクトリ削除エラー
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(dirPath), e);
            return false;
        }
        return true;
    }

    /**
     * リクエストIDの一時結果データファイルのディレクトリから、拡張子でフィルタしたファイル一覧を取得する。
     * @param requestId リクエストID
     * @param extension フィルタ条件の拡張子
     * @return ファイル一覧
     */
    public static File[] serchFileList(String requestId, String extension) {
        final String filterExtension = extension;
        File workDir = new File(TmpFileUtils.getWorkDir(requestId));
        return workDir.listFiles(new FilenameFilter() {
            public boolean accept(File file, String name) {
                boolean ret = name.endsWith(filterExtension);
                return ret;
            }
        });
    }

    /**
     * ファイルを一時結果データファイルのディレクトリにコピーする。
     * @param inFileName コピー元ファイル名(フルパス)
     * @param requestId リクエストID
     * @param outFileName コピー先ファイル名(一時結果データファイルのディレクトリからの相対パス)
     * @return コピー結果
     */
    public boolean copyFile(String inFileName, String requestId, String outFileName) {
        boolean result = true;

        FileChannel ifc = null;
        FileChannel ofc = null;
        FileInputStream fis = null;
        FileOutputStream fos = null;
        try {
            fis = new FileInputStream(inFileName);
            fos = new FileOutputStream(TmpFileUtils.getWorkDir(requestId) + "/" + outFileName);

            ifc = fis.getChannel();
            ofc = fos.getChannel();
            ifc.transferTo(0, ifc.size(), ofc);
        } catch (IOException e) {
            // ローカルファイルアクセスエラー
            // ログには以下の形式でファイル名を出力する
            //   コピー元ファイル + ">" + コピー先ファイル
            log.error(ErrorCode.LOCAL_FILE_ACCESS_ERROR.getErrorMess(inFileName + " > " + TmpFileUtils.getWorkDir(requestId) + "/" + outFileName), e);
            result = false;
        } finally {
            if (ifc != null) {
                try {
                    ifc.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(inFileName), e);
                }
            }
            if (ofc != null) {
                try {
                    ofc.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(TmpFileUtils.getWorkDir(requestId) + "/" + outFileName), e);
                }
            }
            if (fis != null) {
                try {
                    fis.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(inFileName), e);
                }
            }
            if (fos != null) {
                try {
                    fos.close();
                } catch (IOException e) {
                    // ローカルファイルクローズエラー
                    log.warn(ErrorCode.LOCAL_FILE_CLOSE_ERROR.getErrorMess(TmpFileUtils.getWorkDir(requestId) + "/" + outFileName), e);
                }
            }
        }

        return result;
    }

}
