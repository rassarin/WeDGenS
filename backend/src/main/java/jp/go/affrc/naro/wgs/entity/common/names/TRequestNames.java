package jp.go.affrc.naro.wgs.entity.common.names;

import java.sql.Timestamp;
import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TRequest;
import jp.go.affrc.naro.wgs.entity.common.names.MDataTypeNames._MDataTypeNames;
import jp.go.affrc.naro.wgs.entity.common.names.MRequestStatusNames._MRequestStatusNames;
import jp.go.affrc.naro.wgs.entity.common.names.TAvailableGeneratorNames._TAvailableGeneratorNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TRequest}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TRequestNames {

    /**
     * requestIdのプロパティ名を返します。
     * 
     * @return requestIdのプロパティ名
     */
    public static PropertyName<String> requestId() {
        return new PropertyName<String>("requestId");
    }

    /**
     * dataTypeIdのプロパティ名を返します。
     * 
     * @return dataTypeIdのプロパティ名
     */
    public static PropertyName<Integer> dataTypeId() {
        return new PropertyName<Integer>("dataTypeId");
    }

    /**
     * paramsのプロパティ名を返します。
     * 
     * @return paramsのプロパティ名
     */
    public static PropertyName<String> params() {
        return new PropertyName<String>("params");
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
     * pubFlagのプロパティ名を返します。
     * 
     * @return pubFlagのプロパティ名
     */
    public static PropertyName<Integer> pubFlag() {
        return new PropertyName<Integer>("pubFlag");
    }

    /**
     * generatorIdのプロパティ名を返します。
     * 
     * @return generatorIdのプロパティ名
     */
    public static PropertyName<Integer> generatorId() {
        return new PropertyName<Integer>("generatorId");
    }

    /**
     * requestStatusIdのプロパティ名を返します。
     * 
     * @return requestStatusIdのプロパティ名
     */
    public static PropertyName<String> requestStatusId() {
        return new PropertyName<String>("requestStatusId");
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
     * executedAtのプロパティ名を返します。
     * 
     * @return executedAtのプロパティ名
     */
    public static PropertyName<Timestamp> executedAt() {
        return new PropertyName<Timestamp>("executedAt");
    }

    /**
     * finishedAtのプロパティ名を返します。
     * 
     * @return finishedAtのプロパティ名
     */
    public static PropertyName<Timestamp> finishedAt() {
        return new PropertyName<Timestamp>("finishedAt");
    }

    /**
     * MDataTypeのプロパティ名を返します。
     * 
     * @return MDataTypeのプロパティ名
     */
    public static _MDataTypeNames MDataType() {
        return new _MDataTypeNames("MDataType");
    }

    /**
     * MRequestStatusのプロパティ名を返します。
     * 
     * @return MRequestStatusのプロパティ名
     */
    public static _MRequestStatusNames MRequestStatus() {
        return new _MRequestStatusNames("MRequestStatus");
    }

    /**
     * TAvailableGeneratorのプロパティ名を返します。
     * 
     * @return TAvailableGeneratorのプロパティ名
     */
    public static _TAvailableGeneratorNames TAvailableGenerator() {
        return new _TAvailableGeneratorNames("TAvailableGenerator");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _TRequestNames extends PropertyName<TRequest> {

        /**
         * インスタンスを構築します。
         */
        public _TRequestNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TRequestNames(final String name) {
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
        public _TRequestNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
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
         * dataTypeIdのプロパティ名を返します。
         *
         * @return dataTypeIdのプロパティ名
         */
        public PropertyName<Integer> dataTypeId() {
            return new PropertyName<Integer>(this, "dataTypeId");
        }

        /**
         * paramsのプロパティ名を返します。
         *
         * @return paramsのプロパティ名
         */
        public PropertyName<String> params() {
            return new PropertyName<String>(this, "params");
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
         * pubFlagのプロパティ名を返します。
         *
         * @return pubFlagのプロパティ名
         */
        public PropertyName<Integer> pubFlag() {
            return new PropertyName<Integer>(this, "pubFlag");
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
         * requestStatusIdのプロパティ名を返します。
         *
         * @return requestStatusIdのプロパティ名
         */
        public PropertyName<String> requestStatusId() {
            return new PropertyName<String>(this, "requestStatusId");
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
         * executedAtのプロパティ名を返します。
         *
         * @return executedAtのプロパティ名
         */
        public PropertyName<Timestamp> executedAt() {
            return new PropertyName<Timestamp>(this, "executedAt");
        }

        /**
         * finishedAtのプロパティ名を返します。
         *
         * @return finishedAtのプロパティ名
         */
        public PropertyName<Timestamp> finishedAt() {
            return new PropertyName<Timestamp>(this, "finishedAt");
        }

        /**
         * MDataTypeのプロパティ名を返します。
         * 
         * @return MDataTypeのプロパティ名
         */
        public _MDataTypeNames MDataType() {
            return new _MDataTypeNames(this, "MDataType");
        }

        /**
         * MRequestStatusのプロパティ名を返します。
         * 
         * @return MRequestStatusのプロパティ名
         */
        public _MRequestStatusNames MRequestStatus() {
            return new _MRequestStatusNames(this, "MRequestStatus");
        }

        /**
         * TAvailableGeneratorのプロパティ名を返します。
         * 
         * @return TAvailableGeneratorのプロパティ名
         */
        public _TAvailableGeneratorNames TAvailableGenerator() {
            return new _TAvailableGeneratorNames(this, "TAvailableGenerator");
        }
    }
}