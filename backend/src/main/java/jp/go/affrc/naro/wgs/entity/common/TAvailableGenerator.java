package jp.go.affrc.naro.wgs.entity.common;

import java.io.Serializable;
import java.sql.Timestamp;
import java.util.List;
import javax.annotation.Generated;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.Lob;
import javax.persistence.ManyToOne;
import javax.persistence.OneToMany;
import javax.persistence.Table;
import javax.persistence.UniqueConstraint;

/**
 * TAvailableGeneratorエンティティクラス
 * 
 */
@Entity
@Table(uniqueConstraints = { @UniqueConstraint(columnNames = { "ip_addr", "context_name" }) })
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityModelFactoryImpl"}, date = "2014/03/19 13:24:00")
public class TAvailableGenerator implements Serializable {

    private static final long serialVersionUID = 1L;

    /** generatorIdプロパティ */
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(precision = 10, nullable = false, unique = true)
    public Integer generatorId;

    /** libIdプロパティ */
    @Column(length = 64, nullable = false, unique = false)
    public String libId;

    /** ipAddrプロパティ */
    @Column(length = 48, nullable = false, unique = false)
    public String ipAddr;

    /** contextNameプロパティ */
    @Lob
    @Column(length = 2147483647, nullable = false, unique = false)
    public String contextName;

    /** priorityプロパティ */
    @Column(precision = 10, nullable = false, unique = false)
    public Integer priority;

    /** generatorStatusIdプロパティ */
    @Column(length = 16, nullable = false, unique = false)
    public String generatorStatusId;

    /** registeredAtプロパティ */
    @Column(nullable = false, unique = false)
    public Timestamp registeredAt;

    /** MGeneratorStatus関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "generator_status_id", referencedColumnName = "generator_status_id")
    public MGeneratorStatus MGeneratorStatus;

    /** TAvailableLib関連プロパティ */
    @ManyToOne
    @JoinColumn(name = "lib_id", referencedColumnName = "lib_id")
    public TAvailableLib TAvailableLib;

    /** TRequestList関連プロパティ */
    @OneToMany(mappedBy = "TAvailableGenerator")
    public List<TRequest> TRequestList;
}