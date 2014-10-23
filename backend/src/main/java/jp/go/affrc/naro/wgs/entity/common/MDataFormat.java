package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.OneToMany;

/**
 * MDataFormatエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class MDataFormat implements Serializable {

    private static final long serialVersionUID = 1L;

    /** dataFormatIdプロパティ */
    @Id
    @Column(length = 64, nullable = false, unique = true)
    public String dataFormatId;

    /** dataFormatプロパティ */
    @Column(length = 128, nullable = false, unique = false)
    public String dataFormat;

    /** MDataTypeList関連プロパティ */
    @OneToMany(mappedBy = "MDataFormat")
    public List<MDataType> MDataTypeList;
}