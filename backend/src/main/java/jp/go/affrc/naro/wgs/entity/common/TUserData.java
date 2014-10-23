package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.sql.Timestamp;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Lob;

/**
 * TUserDataエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class TUserData implements Serializable {

    private static final long serialVersionUID = 1L;

    /** userDataIdプロパティ */
    @Id
    @Column(length = 36, nullable = false, unique = true)
    public String userDataId;

    /** dataNameプロパティ */
    @Column(length = 256, nullable = false, unique = false)
    public String dataName;

    /** userNameプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String userName;

    /** pubFlagプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer pubFlag;

    /** registeredAtプロパティ */
    @Column(nullable = false, unique = false)
    public Timestamp registeredAt;

    /** commentプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = true, unique = false)
    public String comment;
}