package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.sql.Timestamp;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.Lob;

/**
 * TAccessLogエンティティクラス
 * 
 */
@Entity
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class TAccessLog implements Serializable {

    private static final long serialVersionUID = 1L;

    /** logIdプロパティ */
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(precision = 10, nullable = false, unique = true)
    public Integer logId;

    /** dateTimeプロパティ */
    @Column(nullable = false, unique = false)
    public Timestamp dateTime;

    /** ipAddrプロパティ */
    @Column(length = 48, nullable = false, unique = false)
    public String ipAddr;

    /** priorityプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer priority;

    /** codeプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer code;

    /** userIdプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String userId;

    /** messageプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = false, unique = false)
    public String message;
}