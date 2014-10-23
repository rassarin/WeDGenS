package jp.go.affrc.naro.wgs.action;

import java.util.HashMap;
import java.util.Map;

import javax.annotation.Resource;
import javax.servlet.http.HttpServletRequest;

import jp.go.affrc.naro.wgs.form.CreateDataForm;
import jp.go.affrc.naro.wgs.service.CreateDataService;
import jp.go.affrc.naro.wgs.util.ErrorCode;
import net.arnx.jsonic.JSON;

import org.seasar.struts.annotation.ActionForm;
import org.seasar.struts.annotation.Execute;
import org.seasar.struts.util.ResponseUtil;

/**
 * CreateDataActionクラス。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataAction.java 1267 2014-02-13 07:58:20Z watabe $
 */
public class CreateDataAction {

    /** 正常受信 */
    private static final String NORMAL = "0";

    /** アクションフォーム。 */
    @Resource
    @ActionForm
    protected CreateDataForm createDataForm;

    /** HTTPリクエスト。 */
    @Resource
    public HttpServletRequest request;

    /** データ生成サービス。 */
    @Resource
    protected CreateDataService createDataService;

    //========== アクションイベント ==========
    /**
     * 自身のアルゴリズムによるデータを生成する。
     *
     * @return null (画面遷移なし)
     */
    @Execute(input = "index.jsp")
    public String index() {

        // データ生成要求処理を実行する。
        ErrorCode result = createDataService.createRequest(createDataForm);
        if (result == ErrorCode.REQUEST_NORMAL_END) {
            // データ生成要求処理受付け成功
            Map<String, String> dto = new HashMap<String, String>();
            dto.put("request_id", createDataForm.requestId);
            dto.put("status", NORMAL);
            ResponseUtil.write(JSON.encode(dto), CreateDataService.JSON_CONTENT_TYPE);
        } else {
            // データ生成要求処理受付け失敗
            Map<String, String> dto = new HashMap<String, String>();
            dto.put("request_id", createDataForm.requestId);
            dto.put("status", result.getErrorCode());
            ResponseUtil.write(JSON.encode(dto), CreateDataService.JSON_CONTENT_TYPE);
        }

        return null;
    }
}
