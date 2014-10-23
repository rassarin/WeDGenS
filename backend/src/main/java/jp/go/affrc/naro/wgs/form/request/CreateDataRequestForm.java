package jp.go.affrc.naro.wgs.form.request;


/**
 * CreateDataRequestFormクラス。
 * TODO:データ生成動作確認用のデータ生成要求アクションフォームクラス。動作確認用のため後で削除する。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestForm.java 954 2014-02-13 12:59:08Z watabe $
 */
public class CreateDataRequestForm {

    /** リクエストID */
    public String requestId = "";

    /**
     * 初期化処理。
     */
    public void initialize() {
        requestId = "";
    }
}
