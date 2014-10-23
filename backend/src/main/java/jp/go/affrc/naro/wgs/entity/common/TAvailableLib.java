package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Lob;
import javax.persistence.OneToMany;

/**
 * TAvailableLibエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class TAvailableLib implements Serializable {

    private static final long serialVersionUID = 1L;

    /** libIdプロパティ */
    @Id
    @Column(length = 64, nullable = false, unique = true)
    public String libId;

    /** libNameプロパティ */
    @Column(length = 256, nullable = false, unique = false)
    public String libName;

    /** requireParamsプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = false, unique = false)
    public String requireParams;

    /** descriptionプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = true, unique = false)
    public String description;

    /** TAvailableGeneratorList関連プロパティ */
    @OneToMany(mappedBy = "TAvailableLib")
    public List<TAvailableGenerator> TAvailableGeneratorList;
}