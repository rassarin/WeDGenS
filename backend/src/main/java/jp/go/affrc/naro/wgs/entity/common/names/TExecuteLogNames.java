package jp.go.affrc.naro.wgs.entity.common.names;

import java.sql.Timestamp;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TExecuteLog;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TExecuteLog}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TExecuteLogNames {

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
     * requestIdのプロパティ名を返します。
     * 
     * @return requestIdのプロパティ名
     */
    public static PropertyName<String> requestId() {
        return new PropertyName<String>("requestId");
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
    public static class _TExecuteLogNames extends PropertyName<TExecuteLog> {

        /**
         * インスタンスを構築します。
         */
        public _TExecuteLogNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TExecuteLogNames(final String name) {
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
        public _TExecuteLogNames(final PropertyName<?> parent, final String name) {
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
         * requestIdのプロパティ名を返します。
         *
         * @return requestIdのプロパティ名
         */
        public PropertyName<String> requestId() {
            return new PropertyName<String>(this, "requestId");
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
         * messageのプロパティ名を返します。
         *
         * @return messageのプロパティ名
         */
        public PropertyName<String> message() {
            return new PropertyName<String>(this, "message");
        }
    }
}