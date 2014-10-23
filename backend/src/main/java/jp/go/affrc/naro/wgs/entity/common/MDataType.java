package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.Lob;
import javax.persistence.ManyToOne;
import javax.persistence.OneToMany;

/**
 * MDataTypeエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class MDataType implements Serializable {

    private static final long serialVersionUID = 1L;

    /** dataTypeIdプロパティ */
    @Id
    @Column(precision = 10, nullable = false, unique = true)
    public Integer dataTypeId;

    /** dataTypeプロパティ */
    @Column(length = 128, nullable = false, unique = false)
    public String dataType;

    /** dataFormatIdプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String dataFormatId;

    /** fileTypeIdプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String fileTypeId;

    /** descriptionプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = true, unique = false)
    public String description;

    /** MDataFormat関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "data_format_id", referencedColumnName = "data_format_id")
    public MDataFormat MDataFormat;

    /** MFileType関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "file_type_id", referencedColumnName = "file_type_id")
    public MFileType MFileType;

    /** TRequestList関連プロパティ */
    @OneToMany(mappedBy = "MDataType")
    public List<TRequest> TRequestList;
}