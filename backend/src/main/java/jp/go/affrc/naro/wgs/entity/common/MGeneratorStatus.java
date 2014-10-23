package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.OneToMany;

/**
 * MGeneratorStatusエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class MGeneratorStatus implements Serializable {

    private static final long serialVersionUID = 1L;

    /** generatorStatusIdプロパティ */
    @Id
    @Column(length = 16, nullable = false, unique = true)
    public String generatorStatusId;

    /** generatorStatusプロパティ */
    @Column(length = 16, nullable = false, unique = false)
    public String generatorStatus;

    /** TAvailableGeneratorList関連プロパティ */
    @OneToMany(mappedBy = "MGeneratorStatus")
    public List<TAvailableGenerator> TAvailableGeneratorList;
}