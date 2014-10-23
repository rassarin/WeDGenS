package jp.go.affrc.naro.wgs.entity.common.names;

import javax.annotation.Generated;
import jp.go.affrc.naro.wgs.entity.common.MGeneratorStatus;
import jp.go.affrc.naro.wgs.entity.common.names.TAvailableGeneratorNames._TAvailableGeneratorNames;
import org.seasar.extension.jdbc.name.PropertyName;

/**
 * {@link MGeneratorStatus}のプロパティ名の集合です。
 * 
 */
@Generated(value = {"S2JDBC-Gen 2.4.47", "org.seasar.extension.jdbc.gen.internal.model.NamesModelFactoryImpl"}, date = "2014/03/19 13:24:03")
public class MGeneratorStatusNames {

    /**
     * generatorStatusIdのプロパティ名を返します。
     * 
     * @return generatorStatusIdのプロパティ名
     */
    public static PropertyName<String> generatorStatusId() {
        return new PropertyName<String>("generatorStatusId");
    }

    /**
     * generatorStatusのプロパティ名を返します。
     * 
     * @return generatorStatusのプロパティ名
     */
    public static PropertyName<String> generatorStatus() {
        return new PropertyName<String>("generatorStatus");
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
    public static class _MGeneratorStatusNames extends PropertyName<MGeneratorStatus> {

        /**
         * インスタンスを構築します。
         */
        public _MGeneratorStatusNames() {
        }

        /**
         * インスタンスを構築します。
         * 
         * @param name
         *            名前
         */
        public _MGeneratorStatusNames(final String name) {
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
        public _MGeneratorStatusNames(final PropertyName<?> parent, final String name) {
            super(parent, name);
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
         * generatorStatusのプロパティ名を返します。
         *
         * @return generatorStatusのプロパティ名
         */
        public PropertyName<String> generatorStatus() {
            return new PropertyName<String>(this, "generatorStatus");
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