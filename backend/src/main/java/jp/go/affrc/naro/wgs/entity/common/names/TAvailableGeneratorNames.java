package jp.go.affrc.naro.wgs.entity.common.names;

import java.sql.Timestamp;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAvailableGenerator;
import jp.go.affrc.naro.wgs.entity.common.names.MGeneratorStatusNames._MGeneratorStatusNames;
import jp.go.affrc.naro.wgs.entity.common.names.TAvailableLibNames._TAvailableLibNames;
import jp.go.affrc.naro.wgs.entity.common.names.TRequestNames._TRequestNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TAvailableGenerator}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TAvailableGeneratorNames {

    /**
     * generatorIdのプロパティ名を返します。
     * 
     * @return generatorIdのプロパティ名
     */
    public static PropertyName<Integer> generatorId() {
        return new PropertyName<Integer>("generatorId");
    }

    /**
     * libIdのプロパティ名を返します。
     * 
     * @return libIdのプロパティ名
     */
    public static PropertyName<String> libId() {
        return new PropertyName<String>("libId");
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
     * contextNameのプロパティ名を返します。
     * 
     * @return contextNameのプロパティ名
     */
    public static PropertyName<String> contextName() {
        return new PropertyName<String>("contextName");
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
     * generatorStatusIdのプロパティ名を返します。
     * 
     * @return generatorStatusIdのプロパティ名
     */
    public static PropertyName<String> generatorStatusId() {
        return new PropertyName<String>("generatorStatusId");
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
     * MGeneratorStatusのプロパティ名を返します。
     * 
     * @return MGeneratorStatusのプロパティ名
     */
    public static _MGeneratorStatusNames MGeneratorStatus() {
        return new _MGeneratorStatusNames("MGeneratorStatus");
    }

    /**
     * TAvailableLibのプロパティ名を返します。
     * 
     * @return TAvailableLibのプロパティ名
     */
    public static _TAvailableLibNames TAvailableLib() {
        return new _TAvailableLibNames("TAvailableLib");
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
    public static class _TAvailableGeneratorNames extends PropertyName<TAvailableGenerator> {

        /**
         * インスタンスを構築します。
         */
        public _TAvailableGeneratorNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TAvailableGeneratorNames(final String name) {
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
        public _TAvailableGeneratorNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
        }

        /**
         * generatorIdのプロパティ名を返します。
         *
         * @return generatorIdのプロパティ名
         */
        public PropertyName<Integer> generatorId() {
            return new PropertyName<Integer>(this, "generatorId");
        }

        /**
         * libIdのプロパティ名を返します。
         *
         * @return libIdのプロパティ名
         */
        public PropertyName<String> libId() {
            return new PropertyName<String>(this, "libId");
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
         * contextNameのプロパティ名を返します。
         *
         * @return contextNameのプロパティ名
         */
        public PropertyName<String> contextName() {
            return new PropertyName<String>(this, "contextName");
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
         * generatorStatusIdのプロパティ名を返します。
         *
         * @return generatorStatusIdのプロパティ名
         */
        public PropertyName<String> generatorStatusId() {
            return new PropertyName<String>(this, "generatorStatusId");
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
         * MGeneratorStatusのプロパティ名を返します。
         * 
         * @return MGeneratorStatusのプロパティ名
         */
        public _MGeneratorStatusNames MGeneratorStatus() {
            return new _MGeneratorStatusNames(this, "MGeneratorStatus");
        }

        /**
         * TAvailableLibのプロパティ名を返します。
         * 
         * @return TAvailableLibのプロパティ名
         */
        public _TAvailableLibNames TAvailableLib() {
            return new _TAvailableLibNames(this, "TAvailableLib");
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