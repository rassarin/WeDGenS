package jp.go.affrc.naro.wgs.entity.common;

import javax.annotation.Generated;
import org.seasar.extension.jdbc.JdbcManager;
import org.seasar.extension.unit.S2TestCase;

import static jp.go.affrc.naro.wgs.entity.common.names.MRequestStatusNames.*;

/**
 * {@link MRequestStatus}のテストクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.EntityTestModelFactoryImpl"}, date = "2014/02/26 11:09:29")
public class MRequestStatusTest extends S2TestCase {

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
        jdbcManager.from(MRequestStatus.class).id("aaa").getSingleResult();
    }

    /**
     * TRequestListとの外部結合をテストします。
     * 
     * @throws Exception
     */
    public void testLeftOuterJoin_TRequestList() throws Exception {
        jdbcManager.from(MRequestStatus.class).leftOuterJoin(TRequestList()).id("aaa").getSingleResult();
    }
}