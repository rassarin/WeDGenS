package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MFileType;
import jp.go.affrc.naro.wgs.entity.common.names.MDataTypeNames._MDataTypeNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link MFileType}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class MFileTypeNames {

    /**
     * fileTypeIdのプロパティ名を返します。
     * 
     * @return fileTypeIdのプロパティ名
     */
    public static PropertyName<String> fileTypeId() {
        return new PropertyName<String>("fileTypeId");
    }

    /**
     * fileTypeのプロパティ名を返します。
     * 
     * @return fileTypeのプロパティ名
     */
    public static PropertyName<String> fileType() {
        return new PropertyName<String>("fileType");
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
    public static class _MFileTypeNames extends PropertyName<MFileType> {

        /**
         * インスタンスを構築します。
         */
        public _MFileTypeNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _MFileTypeNames(final String name) {
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
        public _MFileTypeNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
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
         * fileTypeのプロパティ名を返します。
         *
         * @return fileTypeのプロパティ名
         */
        public PropertyName<String> fileType() {
            return new PropertyName<String>(this, "fileType");
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