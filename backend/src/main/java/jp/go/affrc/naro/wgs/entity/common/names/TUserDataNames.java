package jp.go.affrc.naro.wgs.entity.common.names;

import java.sql.Timestamp;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TUserData;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TUserData}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TUserDataNames {

    /**
     * userDataIdのプロパティ名を返します。
     * 
     * @return userDataIdのプロパティ名
     */
    public static PropertyName<String> userDataId() {
        return new PropertyName<String>("userDataId");
    }

    /**
     * dataNameのプロパティ名を返します。
     * 
     * @return dataNameのプロパティ名
     */
    public static PropertyName<String> dataName() {
        return new PropertyName<String>("dataName");
    }

    /**
     * userNameのプロパティ名を返します。
     * 
     * @return userNameのプロパティ名
     */
    public static PropertyName<String> userName() {
        return new PropertyName<String>("userName");
    }

    /**
     * pubFlagのプロパティ名を返します。
     * 
     * @return pubFlagのプロパティ名
     */
    public static PropertyName<Integer> pubFlag() {
        return new PropertyName<Integer>("pubFlag");
    }

    /**
     * registeredAtのプロパティ名を返します。
     * 
     * @return registeredAtのプロパティ名
     */
    public static PropertyName<Timestamp> registeredAt() {
        return new PropertyName<Timestamp>("registeredAt");
    }

    /**
     * commentのプロパティ名を返します。
     * 
     * @return commentのプロパティ名
     */
    public static PropertyName<String> comment() {
        return new PropertyName<String>("comment");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _TUserDataNames extends PropertyName<TUserData> {

        /**
         * インスタンスを構築します。
         */
        public _TUserDataNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TUserDataNames(final String name) {
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
        public _TUserDataNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
        }

        /**
         * userDataIdのプロパティ名を返します。
         *
         * @return userDataIdのプロパティ名
         */
        public PropertyName<String> userDataId() {
            return new PropertyName<String>(this, "userDataId");
        }

        /**
         * dataNameのプロパティ名を返します。
         *
         * @return dataNameのプロパティ名
         */
        public PropertyName<String> dataName() {
            return new PropertyName<String>(this, "dataName");
        }

        /**
         * userNameのプロパティ名を返します。
         *
         * @return userNameのプロパティ名
         */
        public PropertyName<String> userName() {
            return new PropertyName<String>(this, "userName");
        }

        /**
         * pubFlagのプロパティ名を返します。
         *
         * @return pubFlagのプロパティ名
         */
        public PropertyName<Integer> pubFlag() {
            return new PropertyName<Integer>(this, "pubFlag");
        }

        /**
         * registeredAtのプロパティ名を返します。
         *
         * @return registeredAtのプロパティ名
         */
        public PropertyName<Timestamp> registeredAt() {
            return new PropertyName<Timestamp>(this, "registeredAt");
        }

        /**
         * commentのプロパティ名を返します。
         *
         * @return commentのプロパティ名
         */
        public PropertyName<String> comment() {
            return new PropertyName<String>(this, "comment");
        }
    }
}