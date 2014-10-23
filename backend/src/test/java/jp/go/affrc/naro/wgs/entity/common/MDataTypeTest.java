package jp.go.affrc.naro.wgs.entity.common;

import javax.annotation.Generated;
import org.seasar.extension.jdbc.JdbcManager;
import org.seasar.extension.unit.S2TestCase;

import static jp.go.affrc.naro.wgs.entity.common.names.MDataTypeNames.*;

/**
 * {@link MDataType}のテストクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityTestModelFactoryImpl"}, date = "2014/02/26 11:09:29")
public class MDataTypeTest extends S2TestCase {

    private JdbcManager jdbcManager;

    /**
     * 事前処理をします。
     * 
     * @throws Exception
     */
    @Override
    protected void setUp() throws Exception {
        super.setUp();
        include("s2jdbc.dicon");
    }

    /**
     * 識別子による取得をテストします。
     * 
     * @throws Exception
     */
    public void testFindById() throws Exception {
        jdbcManager.from(MDataType.class).id(1).getSingleResult();
    }

    /**
     * MDataFormatとの外部結合をテストします。
     * 
     * @throws Exception
     */
    public void testLeftOuterJoin_MDataFormat() throws Exception {
        jdbcManager.from(MDataType.class).leftOuterJoin(MDataFormat()).id(1).getSingleResult();
    }

    /**
     * MFileTypeとの外部結合をテストします。
     * 
     * @throws Exception
     */
    public void testLeftOuterJoin_MFileType() throws Exception {
        jdbcManager.from(MDataType.class).leftOuterJoin(MFileType()).id(1).getSingleResult();
    }

    /**
     * TRequestListとの外部結合をテストします。
     * 
     * @throws Exception
     */
    public void testLeftOuterJoin_TRequestList() throws Exception {
        jdbcManager.from(MDataType.class).leftOuterJoin(TRequestList()).id(1).getSingleResult();
    }
}