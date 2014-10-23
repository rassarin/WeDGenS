package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MDataType;
import jp.go.affrc.naro.wgs.entity.common.names.MDataFormatNames._MDataFormatNames;
import jp.go.affrc.naro.wgs.entity.common.names.MFileTypeNames._MFileTypeNames;
import jp.go.affrc.naro.wgs.entity.common.names.TRequestNames._TRequestNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link MDataType}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class MDataTypeNames {

    /**
     * dataTypeIdのプロパティ名を返します。
     * 
     * @return dataTypeIdのプロパティ名
     */
    public static PropertyName<Integer> dataTypeId() {
        return new PropertyName<Integer>("dataTypeId");
    }

    /**
     * dataTypeのプロパティ名を返します。
     * 
     * @return dataTypeのプロパティ名
     */
    public static PropertyName<String> dataType() {
        return new PropertyName<String>("dataType");
    }

    /**
     * dataFormatIdのプロパティ名を返します。
     * 
     * @return dataFormatIdのプロパティ名
     */
    public static PropertyName<String> dataFormatId() {
        return new PropertyName<String>("dataFormatId");
    }

    /**
     * fileTypeIdのプロパティ名を返します。
     * 
     * @return fileTypeIdのプロパティ名
     */
    public static PropertyName<String> fileTypeId() {
        return new PropertyName<String>("fileTypeId");
    }

    /**
     * descriptionのプロパティ名を返します。
     * 
     * @return descriptionのプロパティ名
     */
    public static PropertyName<String> description() {
        return new PropertyName<String>("description");
    }

    /**
     * MDataFormatのプロパティ名を返します。
     * 
     * @return MDataFormatのプロパティ名
     */
    public static _MDataFormatNames MDataFormat() {
        return new _MDataFormatNames("MDataFormat");
    }

    /**
     * MFileTypeのプロパティ名を返します。
     * 
     * @return MFileTypeのプロパティ名
     */
    public static _MFileTypeNames MFileType() {
        return new _MFileTypeNames("MFileType");
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
    public static class _MDataTypeNames extends PropertyName<MDataType> {

        /**
         * インスタンスを構築します。
         */
        public _MDataTypeNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _MDataTypeNames(final String name) {
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
        public _MDataTypeNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
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
         * dataTypeのプロパティ名を返します。
         *
         * @return dataTypeのプロパティ名
         */
        public PropertyName<String> dataType() {
            return new PropertyName<String>(this, "dataType");
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
         * fileTypeIdのプロパティ名を返します。
         *
         * @return fileTypeIdのプロパティ名
         */
        public PropertyName<String> fileTypeId() {
            return new PropertyName<String>(this, "fileTypeId");
        }

        /**
         * descriptionのプロパティ名を返します。
         *
         * @return descriptionのプロパティ名
         */
        public PropertyName<String> description() {
            return new PropertyName<String>(this, "description");
        }

        /**
         * MDataFormatのプロパティ名を返します。
         * 
         * @return MDataFormatのプロパティ名
         */
        public _MDataFormatNames MDataFormat() {
            return new _MDataFormatNames(this, "MDataFormat");
        }

        /**
         * MFileTypeのプロパティ名を返します。
         * 
         * @return MFileTypeのプロパティ名
         */
        public _MFileTypeNames MFileType() {
            return new _MFileTypeNames(this, "MFileType");
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