package jp.go.affrc.naro.wgs.task;

import javax.annotation.Resource;

import jp.go.affrc.naro.wgs.dto.WgsLibInfoDto;
import jp.go.affrc.naro.wgs.entity.common.TRequest;
import jp.go.affrc.naro.wgs.service.CreateDataService;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import jp.go.affrc.naro.wgs.util.RequestStatus;

import org.seasar.chronos.core.TaskTrigger;
import org.seasar.chronos.core.ThreadPoolType;
import org.seasar.chronos.core.annotation.task.Task;
import org.seasar.chronos.core.annotation.task.method.JoinTask;
import org.seasar.chronos.core.annotation.type.JoinType;
import org.seasar.framework.log.Logger;
import org.seasar.framework.util.ResourceUtil;

/**
 * データ生成タスク。
 * 受取ったデータ生成要求を非同期で実行する。
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DeleteDataAction.java 1267 2014-02-13 07:58:20Z watabe $
 */
@Task(autoSchedule = true)
public class CreateDataTask {

    /** ログ出力クラス。 */
    private static Logger log = Logger.getLogger(CreateDataTask.class);

    /** デフォルトスレッドプールサイズ */
    private static final int DEFAULT_THREAD_POOL_SIZE = 10;

    /** データ生成サービス。 */
    @Resource
    protected CreateDataService createDataService;

    /** トリガークラス */
    private CCreateDataTrigger trigger = new CCreateDataTrigger();

    /**
     * トリガーを取得する。
     * @return トリガー
     */
    public TaskTrigger getTrigger() {
        return trigger;
    }

    /**
     * スレッドプールタイプを取得する。
     * @return スレッドプールタイプ
     */
    public ThreadPoolType getThreadPoolType() {
        return ThreadPoolType.CACHED;
    }

    /**
     * スレッドプール数を取得する。
     * @return スレッドプール数
     */
    public Integer getThreadPoolSize() {
        String threadPoolSize = ResourceUtil.getProperties("wgs-backend.properties").getProperty("wgs-backend.task.threadPool.size");
        if (threadPoolSize != null) {
            return new Integer(threadPoolSize);
        }

        // プロパティ設定値が取得できない場合はデフォルト値(10)を返却する。
        return DEFAULT_THREAD_POOL_SIZE;
    }

    // タスクの実装に必要なメソッド。
    /**
     * 初期化処理。
     */
    public void initialize() {
        log.debug("データ生成タスクの初期化処理実施");
        // 即時スタートを設定する。
        trigger.setStartTask(true);

        // タスク終了なしに設定する。
        trigger.setEndTask(false);

        // 初期状態は1回目で終了しない様にリスケありを設定する。
        trigger.setReScheduleTask(true);
    }

    /**
     * タスク処理。
     */
    @JoinTask(JoinType.NoWait)
    public void doExecute() {
        // タスク開始ログ
        log.info(ErrorCode.START_TASK.getErrorMess());

        TRequest tRequest = null;
        try {
            // 受付け済みのデータ生成要求の中から、リクエストステータスの生成状況が実行待ちのリクエスト情報を取得する。
            tRequest = createDataService.selectRequest();
            if (tRequest == null) {
                // 処理可能なリクエストなし
                log.info(ErrorCode.ACCEPT_REQUEST_NOT_FOUND.getErrorMess());
                return;
            }

            // リクエスト情報から処理を行うライブラリ情報を取得する。
            WgsLibInfoDto wsgLibInfo = createDataService.selectLibInfo(tRequest);
            if (wsgLibInfo == null) {
                // 処理可能なライブラリ情報なし
                // ログを出力し、リクエストステータスをエラーに更新して終了。
                log.error(ErrorCode.ACCEPT_REQUEST_NOT_FOUND.getErrorMess(tRequest.requestId));
                createDataService.executeLog(tRequest.requestId, ErrorCode.WGS_LIB_NOT_FOUND, tRequest.requestId);
                createDataService.updateFinishedRequestStatus(tRequest, RequestStatus.ERROR);
                return;
            }

            // ライブラリを使ったデータ生成処理を実行する。
            ErrorCode errorCode = createDataService.executLib(wsgLibInfo, tRequest);
            if (errorCode != ErrorCode.CREATE_NORMAL_END) {
                // データ生成処理に失敗
                // ログを出力し、リクエストステータスをエラーに更新して終了。
                log.error(ErrorCode.GENERATOR_EXEC_ERROR.getErrorMess(tRequest.requestId));
                createDataService.executeLog(tRequest.requestId, ErrorCode.GENERATOR_EXEC_ERROR, tRequest.requestId);
                createDataService.updateFinishedRequestStatus(tRequest, RequestStatus.ERROR);
                return;
            }

            // リクエストステータスを実行完了に更新して終了。
            log.info(ErrorCode.CREATE_NORMAL_END.getErrorMess(tRequest.requestId));
            createDataService.executeLog(tRequest.requestId, ErrorCode.CREATE_NORMAL_END, tRequest.requestId);
            createDataService.updateFinishedRequestStatus(tRequest, RequestStatus.FINISHED);

        } catch (RuntimeException e) {
            // 予期しない実行エラー
            if (tRequest != null) {
                // リクエスト情報が取得済みであれば、ログを出力し、リクエストステータスをエラーに更新して終了。
                log.error(ErrorCode.SYSTEM_ERROR.getErrorMess(tRequest.requestId), e);
                createDataService.executeLog(tRequest.requestId, ErrorCode.SYSTEM_ERROR, tRequest.requestId);
                createDataService.updateFinishedRequestStatus(tRequest, RequestStatus.ERROR);
            } else {
                // リクエスト情報が取得出来ていない場合は、トレースログのみ出力する。
                log.error(ErrorCode.SYSTEM_ERROR.getErrorMess("リクエストIDなし"), e);
            }
        } finally {
            // リクエストの件数をチェック
            long count = createDataService.checkRequestCount();
            if (count > 0) {
                // 処理可能なリクエストが残っている場合はリスケジュールありを設定する。
                log.debug("処理可能なリクエスト" + count + "件");
                trigger.setReScheduleTask(true);
            } else {
                // 処理可能なリクエストが残っていない場合はリスケジュールなしを設定する。
                log.debug("処理可能なリクエストなし");
                trigger.setReScheduleTask(false);
            }

            // タスク終了ログ
            log.info(ErrorCode.END_TASK.getErrorMess());
        }
    }

    /**
     * 終了（破棄）処理。
     */
    public void destroy() {
        log.debug("データ生成タスクの終了処理実施");
    }

}
