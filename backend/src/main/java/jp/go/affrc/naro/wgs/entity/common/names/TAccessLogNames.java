package jp.go.affrc.naro.wgs.entity.common.names;

import java.sql.Timestamp;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAccessLog;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TAccessLog}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TAccessLogNames {

    /**
     * logIdのプロパティ名を返します。
     * 
     * @return logIdのプロパティ名
     */
    public static PropertyName<Integer> logId() {
        return new PropertyName<Integer>("logId");
    }

    /**
     * dateTimeのプロパティ名を返します。
     * 
     * @return dateTimeのプロパティ名
     */
    public static PropertyName<Timestamp> dateTime() {
        return new PropertyName<Timestamp>("dateTime");
    }

    /**
     * ipAddrのプロパティ名を返します。
     * 
     * @return ipAddrのプロパティ名
     */
    public static PropertyName<String> ipAddr() {
        return new PropertyName<String>("ipAddr");
    }

    /**
     * priorityのプロパティ名を返します。
     * 
     * @return priorityのプロパティ名
     */
    public static PropertyName<Integer> priority() {
        return new PropertyName<Integer>("priority");
    }

    /**
     * codeのプロパティ名を返します。
     * 
     * @return codeのプロパティ名
     */
    public static PropertyName<Integer> code() {
        return new PropertyName<Integer>("code");
    }

    /**
     * userIdのプロパティ名を返します。
     * 
     * @return userIdのプロパティ名
     */
    public static PropertyName<String> userId() {
        return new PropertyName<String>("userId");
    }

    /**
     * messageのプロパティ名を返します。
     * 
     * @return messageのプロパティ名
     */
    public static PropertyName<String> message() {
        return new PropertyName<String>("message");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _TAccessLogNames extends PropertyName<TAccessLog> {

        /**
         * インスタンスを構築します。
         */
        public _TAccessLogNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TAccessLogNames(final String name) {
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
        public _TAccessLogNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
        }

        /**
         * logIdのプロパティ名を返します。
         *
         * @return logIdのプロパティ名
         */
        public PropertyName<Integer> logId() {
            return new PropertyName<Integer>(this, "logId");
        }

        /**
         * dateTimeのプロパティ名を返します。
         *
         * @return dateTimeのプロパティ名
         */
        public PropertyName<Timestamp> dateTime() {
            return new PropertyName<Timestamp>(this, "dateTime");
        }

        /**
         * ipAddrのプロパティ名を返します。
         *
         * @return ipAddrのプロパティ名
         */
        public PropertyName<String> ipAddr() {
            return new PropertyName<String>(this, "ipAddr");
        }

        /**
         * priorityのプロパティ名を返します。
         *
         * @return priorityのプロパティ名
         */
        public PropertyName<Integer> priority() {
            return new PropertyName<Integer>(this, "priority");
        }

        /**
         * codeのプロパティ名を返します。
         *
         * @return codeのプロパティ名
         */
        public PropertyName<Integer> code() {
            return new PropertyName<Integer>(this, "code");
        }

        /**
         * userIdのプロパティ名を返します。
         *
         * @return userIdのプロパティ名
         */
        public PropertyName<String> userId() {
            return new PropertyName<String>(this, "userId");
        }

        /**
         * messageのプロパティ名を返します。
         *
         * @return messageのプロパティ名
         */
        public PropertyName<String> message() {
            return new PropertyName<String>(this, "message");
        }
    }
}