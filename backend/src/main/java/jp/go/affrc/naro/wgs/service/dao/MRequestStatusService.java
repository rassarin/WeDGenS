package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MRequestStatus;

import static jp.go.affrc.naro.wgs.entity.common.names.MRequestStatusNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link MRequestStatus}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class MRequestStatusService extends AbstractService<MRequestStatus> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param requestStatusId
     *            識別子
     * @return エンティティ
     */
    public MRequestStatus findById(String requestStatusId) {
        return select().id(requestStatusId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<MRequestStatus> findAllOrderById() {
        return select().orderBy(asc(requestStatusId())).getResultList();
    }
}