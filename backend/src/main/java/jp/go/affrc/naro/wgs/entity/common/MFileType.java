package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.OneToMany;

/**
 * MFileTypeエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class MFileType implements Serializable {

    private static final long serialVersionUID = 1L;

    /** fileTypeIdプロパティ */
    @Id
    @Column(length = 64, nullable = false, unique = true)
    public String fileTypeId;

    /** fileTypeプロパティ */
    @Column(length = 128, nullable = false, unique = false)
    public String fileType;

    /** MDataTypeList関連プロパティ */
    @OneToMany(mappedBy = "MFileType")
    public List<MDataType> MDataTypeList;
}