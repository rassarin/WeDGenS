package jp.go.affrc.naro.wgs.form;


/**
 * CreateDataFormクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataForm.java 954 2014-02-13 12:59:08Z watabe $
 */
public class CreateDataForm {
    /** リクエストID（UUID） */
    public String requestId = "";

    /**
     * 初期化処理。
     */
    public void initialize() {
        this.requestId = "";
    }
}
