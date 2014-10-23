package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TUserData;

import static jp.go.affrc.naro.wgs.entity.common.names.TUserDataNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TUserData}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TUserDataService extends AbstractService<TUserData> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param userDataId
     *            識別子
     * @return エンティティ
     */
    public TUserData findById(String userDataId) {
        return select().id(userDataId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TUserData> findAllOrderById() {
        return select().orderBy(asc(userDataId())).getResultList();
    }
}