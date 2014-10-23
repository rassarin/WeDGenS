package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MRequestStatus;
import jp.go.affrc.naro.wgs.entity.common.names.TRequestNames._TRequestNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link MRequestStatus}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class MRequestStatusNames {

    /**
     * requestStatusIdのプロパティ名を返します。
     * 
     * @return requestStatusIdのプロパティ名
     */
    public static PropertyName<String> requestStatusId() {
        return new PropertyName<String>("requestStatusId");
    }

    /**
     * requestStatusのプロパティ名を返します。
     * 
     * @return requestStatusのプロパティ名
     */
    public static PropertyName<String> requestStatus() {
        return new PropertyName<String>("requestStatus");
    }

    /**
     * TRequestListのプロパティ名を返します。
     * 
     * @return TRequestListのプロパティ名
     */
    public static _TRequestNames TRequestList() {
        return new _TRequestNames("TRequestList");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _MRequestStatusNames extends PropertyName<MRequestStatus> {

        /**
         * インスタンスを構築します。
         */
        public _MRequestStatusNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _MRequestStatusNames(final String name) {
            super(name);
        }

        /**
         * インスタンスを構築します。
         * 
         * @param parent
         *            親
         * @param name
         *            名前
         */
        public _MRequestStatusNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
        }

        /**
         * requestStatusIdのプロパティ名を返します。
         *
         * @return requestStatusIdのプロパティ名
         */
        public PropertyName<String> requestStatusId() {
            return new PropertyName<String>(this, "requestStatusId");
        }

        /**
         * requestStatusのプロパティ名を返します。
         *
         * @return requestStatusのプロパティ名
         */
        public PropertyName<String> requestStatus() {
            return new PropertyName<String>(this, "requestStatus");
        }

        /**
         * TRequestListのプロパティ名を返します。
         * 
         * @return TRequestListのプロパティ名
         */
        public _TRequestNames TRequestList() {
            return new _TRequestNames(this, "TRequestList");
        }
    }
}