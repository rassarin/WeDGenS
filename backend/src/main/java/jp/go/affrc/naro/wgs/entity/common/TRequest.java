package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.sql.Timestamp;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.Lob;
import javax.persistence.ManyToOne;

/**
 * TRequestエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class TRequest implements Serializable {

    private static final long serialVersionUID = 1L;

    /** requestIdプロパティ */
    @Id
    @Column(length = 36, nullable = false, unique = true)
    public String requestId;

    /** dataTypeIdプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer dataTypeId;

    /** paramsプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = false, unique = false)
    public String params;

    /** userIdプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String userId;

    /** pubFlagプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer pubFlag;

    /** generatorIdプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer generatorId;

    /** requestStatusIdプロパティ */
    @Column(length = 16, nullable = false, unique = false)
    public String requestStatusId;

    /** registeredAtプロパティ */
    @Column(nullable = false, unique = false)
    public Timestamp registeredAt;

    /** executedAtプロパティ */
    @Column(nullable = true, unique = false)
    public Timestamp executedAt;

    /** finishedAtプロパティ */
    @Column(nullable = true, unique = false)
    public Timestamp finishedAt;

    /** MDataType関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "data_type_id", referencedColumnName = "data_type_id")
    public MDataType MDataType;

    /** MRequestStatus関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "request_status_id", referencedColumnName = "request_status_id")
    public MRequestStatus MRequestStatus;

    /** TAvailableGenerator関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "generator_id", referencedColumnName = "generator_id")
    public TAvailableGenerator TAvailableGenerator;
}