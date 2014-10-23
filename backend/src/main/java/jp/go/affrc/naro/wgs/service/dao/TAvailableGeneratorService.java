package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAvailableGenerator;

import static jp.go.affrc.naro.wgs.entity.common.names.TAvailableGeneratorNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link TAvailableGenerator}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class TAvailableGeneratorService extends AbstractService<TAvailableGenerator> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param generatorId
     *            識別子
     * @return エンティティ
     */
    public TAvailableGenerator findById(Integer generatorId) {
        return select().id(generatorId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<TAvailableGenerator> findAllOrderById() {
        return select().orderBy(asc(generatorId())).getResultList();
    }
}