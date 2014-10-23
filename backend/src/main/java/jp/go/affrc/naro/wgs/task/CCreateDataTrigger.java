package jp.go.affrc.naro.wgs.task;

import org.seasar.chronos.core.trigger.AbstractTrigger;

/**
 * データ生成トリガー。
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: DeleteDataAction.java 1267 2014-02-13 07:58:20Z watabe $
 */
public class CCreateDataTrigger extends AbstractTrigger {

    /** タスクの終了条件。 */
    private boolean endTask = false;

    /** タスクの開始条件。 */
    private boolean startTask = false;

    /**
     * タスクの終了条件を取得する。
     * @return タスクの終了条件
     */
    @Override
    public boolean isEndTask() {
        return this.endTask;
    }

    /**
     * タスクの開始条件を取得する。
     * @return タスクの開始条件
     */
    @Override
    public boolean isStartTask() {
        return this.startTask;
    }

    /**
     * タスクの終了条件を設定する。
     * @param paramBoolean タスクの終了条件
     */
    @Override
    public void setEndTask(boolean paramBoolean) {
        this.endTask = paramBoolean;
    }

    /**
     * タスクの開始条件を設定する。
     * @param paramBoolean タスクの開始条件
     */
    @Override
    public void setStartTask(boolean paramBoolean) {
        this.startTask = paramBoolean;
    }
}
