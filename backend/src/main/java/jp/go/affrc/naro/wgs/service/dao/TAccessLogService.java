package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAccessLog;

import static jp.go.affrc.naro.wgs.entity.common.names.TAccessLogNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TAccessLog}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TAccessLogService extends AbstractService<TAccessLog> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param logId
     *            識別子
     * @return エンティティ
     */
    public TAccessLog findById(Integer logId) {
        return select().id(logId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TAccessLog> findAllOrderById() {
        return select().orderBy(asc(logId())).getResultList();
    }
}