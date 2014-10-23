package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TRequest;

import static jp.go.affrc.naro.wgs.entity.common.names.TRequestNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TRequest}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TRequestService extends AbstractService<TRequest> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param requestId
     *            識別子
     * @return エンティティ
     */
    public TRequest findById(String requestId) {
        return select().id(requestId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TRequest> findAllOrderById() {
        return select().orderBy(asc(requestId())).getResultList();
    }
}