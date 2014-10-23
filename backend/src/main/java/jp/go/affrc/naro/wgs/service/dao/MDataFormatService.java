package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MDataFormat;

import static jp.go.affrc.naro.wgs.entity.common.names.MDataFormatNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link MDataFormat}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class MDataFormatService extends AbstractService<MDataFormat> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param dataFormatId
     *            識別子
     * @return エンティティ
     */
    public MDataFormat findById(String dataFormatId) {
        return select().id(dataFormatId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<MDataFormat> findAllOrderById() {
        return select().orderBy(asc(dataFormatId())).getResultList();
    }
}