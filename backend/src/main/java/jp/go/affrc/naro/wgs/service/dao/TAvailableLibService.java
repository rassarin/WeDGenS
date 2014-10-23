package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAvailableLib;

import static jp.go.affrc.naro.wgs.entity.common.names.TAvailableLibNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TAvailableLib}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TAvailableLibService extends AbstractService<TAvailableLib> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param libId
     *            識別子
     * @return エンティティ
     */
    public TAvailableLib findById(String libId) {
        return select().id(libId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TAvailableLib> findAllOrderById() {
        return select().orderBy(asc(libId())).getResultList();
    }
}