package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.OneToMany;

/**
 * MRequestStatusエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class MRequestStatus implements Serializable {

    private static final long serialVersionUID = 1L;

    /** requestStatusIdプロパティ */
    @Id
    @Column(length = 16, nullable = false, unique = true)
    public String requestStatusId;

    /** requestStatusプロパティ */
    @Column(length = 16, nullable = false, unique = false)
    public String requestStatus;

    /** TRequestList関連プロパティ */
    @OneToMany(mappedBy = "MRequestStatus")
    public List<TRequest> TRequestList;
}