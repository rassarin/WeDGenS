package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TExecuteLog;

import static jp.go.affrc.naro.wgs.entity.common.names.TExecuteLogNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TExecuteLog}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TExecuteLogService extends AbstractService<TExecuteLog> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param logId
     *            識別子
     * @return エンティティ
     */
    public TExecuteLog findById(Integer logId) {
        return select().id(logId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TExecuteLog> findAllOrderById() {
        return select().orderBy(asc(logId())).getResultList();
    }
}