package jp.go.affrc.naro.wgs.service.dao;

import javax.annotation.Generated;
import org.seasar.extension.unit.S2TestCase;

/**
 * {@link MDataFormatService}のテストクラスです。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.ServiceTestModelFactoryImpl"}, date = "2014/02/26 11:09:31")
public class MDataFormatServiceTest extends S2TestCase {

    private MDataFormatService mDataFormatService;

    /**
     * 事前処理をします。
     * 
     * @throws Exception
     */
    @Override
    protected void setUp() throws Exception {
        super.setUp();
        include("app.dicon");
    }

    /**
     * {@link #mDataFormatService}が利用可能であることをテストします。
     * 
     * @throws Exception
     */
    public void testAvailable() throws Exception {
        assertNotNull(mDataFormatService);
    }
}