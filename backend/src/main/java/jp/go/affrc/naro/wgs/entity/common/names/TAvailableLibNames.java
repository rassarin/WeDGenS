package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.TAvailableLib;
import jp.go.affrc.naro.wgs.entity.common.names.TAvailableGeneratorNames._TAvailableGeneratorNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link TAvailableLib}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class TAvailableLibNames {

    /**
     * libIdのプロパティ名を返します。
     * 
     * @return libIdのプロパティ名
     */
    public static PropertyName<String> libId() {
        return new PropertyName<String>("libId");
    }

    /**
     * libNameのプロパティ名を返します。
     * 
     * @return libNameのプロパティ名
     */
    public static PropertyName<String> libName() {
        return new PropertyName<String>("libName");
    }

    /**
     * requireParamsのプロパティ名を返します。
     * 
     * @return requireParamsのプロパティ名
     */
    public static PropertyName<String> requireParams() {
        return new PropertyName<String>("requireParams");
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
     * TAvailableGeneratorListのプロパティ名を返します。
     * 
     * @return TAvailableGeneratorListのプロパティ名
     */
    public static _TAvailableGeneratorNames TAvailableGeneratorList() {
        return new _TAvailableGeneratorNames("TAvailableGeneratorList");
    }

    /**
     * @author S2JDBC-Gen
     */
    public static class _TAvailableLibNames extends PropertyName<TAvailableLib> {

        /**
         * インスタンスを構築します。
         */
        public _TAvailableLibNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _TAvailableLibNames(final String name) {
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
        public _TAvailableLibNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
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
         * libNameのプロパティ名を返します。
         *
         * @return libNameのプロパティ名
         */
        public PropertyName<String> libName() {
            return new PropertyName<String>(this, "libName");
        }

        /**
         * requireParamsのプロパティ名を返します。
         *
         * @return requireParamsのプロパティ名
         */
        public PropertyName<String> requireParams() {
            return new PropertyName<String>(this, "requireParams");
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
         * TAvailableGeneratorListのプロパティ名を返します。
         * 
         * @return TAvailableGeneratorListのプロパティ名
         */
        public _TAvailableGeneratorNames TAvailableGeneratorList() {
            return new _TAvailableGeneratorNames(this, "TAvailableGeneratorList");
        }
    }
}