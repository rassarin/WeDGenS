package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MDataType;

import static jp.go.affrc.naro.wgs.entity.common.names.MDataTypeNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link MDataType}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class MDataTypeService extends AbstractService<MDataType> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param dataTypeId
     *            識別子
     * @return エンティティ
     */
    public MDataType findById(Integer dataTypeId) {
        return select().id(dataTypeId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<MDataType> findAllOrderById() {
        return select().orderBy(asc(dataTypeId())).getResultList();
    }
}