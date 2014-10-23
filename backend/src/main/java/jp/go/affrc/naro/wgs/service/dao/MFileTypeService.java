package jp.go.affrc.naro.wgs.service.dao;

import java.util.List;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MFileType;

import static jp.go.affrc.naro.wgs.entity.common.names.MFileTypeNames.*;
import static org.seasar.extension.jdbc.operation.Operations.*;

/**
 * {@link MFileType}のサービスクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceModelFactoryImpl"}, date = "2014/03/19 13:24:06")
public class MFileTypeService extends AbstractService<MFileType> {

    /**
     * 識別子でエンティティを検索します。
     * 
     * @param fileTypeId
     *            識別子
     * @return エンティティ
     */
    public MFileType findById(String fileTypeId) {
        return select().id(fileTypeId).getSingleResult();
    }

    /**
     * 識別子の昇順ですべてのエンティティを検索します。
     * 
     * @return エンティティのリスト
     */
    public List<MFileType> findAllOrderById() {
        return select().orderBy(asc(fileTypeId())).getResultList();
    }
}