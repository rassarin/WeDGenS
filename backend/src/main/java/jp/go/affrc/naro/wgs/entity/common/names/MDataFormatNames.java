package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MDataFormat;
import jp.go.affrc.naro.wgs.entity.common.names.MDataTypeNames._MDataTypeNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link MDataFormat}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class MDataFormatNames {

    /**
     * dataFormatIdのプロパティ名を返します。
     * 
     * @return dataFormatIdのプロパティ名
     */
    public static PropertyName<String> dataFormatId() {
        return new PropertyName<String>("dataFormatId");
    }

    /**
     * dataFormatのプロパティ名を返します。
     * 
     * @return dataFormatのプロパティ名
     */
    public static PropertyName<String> dataFormat() {
        return new PropertyName<String>("dataFormat");
    }

    /**
     * MDataTypeListのプロパティ名を返します。
     * 
     * @return MDataTypeListのプロパティ名
     */
    public static _MDataTypeNames MDataTypeList() {
        return new _MDataTypeNames("MDataTypeList");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _MDataFormatNames extends PropertyName<MDataFormat> {

        /**
         * インスタンスを構築します。
         */
        public _MDataFormatNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _MDataFormatNames(final String name) {
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
        public _MDataFormatNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
        }

        /**
         * dataFormatIdのプロパティ名を返します。
         *
         * @return dataFormatIdのプロパティ名
         */
        public PropertyName<String> dataFormatId() {
            return new PropertyName<String>(this, "dataFormatId");
        }

        /**
         * dataFormatのプロパティ名を返します。
         *
         * @return dataFormatのプロパティ名
         */
        public PropertyName<String> dataFormat() {
            return new PropertyName<String>(this, "dataFormat");
        }

        /**
         * MDataTypeListのプロパティ名を返します。
         * 
         * @return MDataTypeListのプロパティ名
         */
        public _MDataTypeNames MDataTypeList() {
            return new _MDataTypeNames(this, "MDataTypeList");
        }
    }
}