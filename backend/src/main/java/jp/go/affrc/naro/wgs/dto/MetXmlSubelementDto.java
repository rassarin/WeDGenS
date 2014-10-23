package jp.go.affrc.naro.wgs.dto;

import jp.go.affrc.narc.metxml.Element;
import jp.go.affrc.narc.metxml.Station;
import jp.go.affrc.narc.metxml.Subelement;

import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;

/**
 * MetXML形式のサブエレメント情報DTOクラス。
 * サブエレメントの親階層のエレメントを持つデータ構造。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "metXmlSubelementDto")
public class MetXmlSubelementDto {

    /** 親のステーション。 */
    public Station station = null;

    /** 親のエレメント。 */
    public Element element = null;

    /** サブエレメント。 */
    public Subelement subelement = null;

    /**
     * ステーションIDを取得する。
     * @return ステーションID
     */
    public String getStationId() {
        if (this.station == null) {
            return null;
        }
        return this.station.getId();
    }

    /**
     * ステーションの緯度を取得する。
     * @return ステーションの緯度
     */
    public double getLat() {
        if (this.station == null || this.station.getPlace() == null) {
            return 0;
        }
        return this.station.getPlace().getLat();
    }

    /**
     * ステーションの経度を取得する。
     * @return ステーションの経度
     */
    public double getLon() {
        if (this.station == null || this.station.getPlace() == null) {
            return 0;
        }
        return this.station.getPlace().getLon();
    }

    /**
     * エレメントIDを取得する。
     * @return エレメントID
     */
    public String getElementId() {
        if (this.element == null) {
            return null;
        }
        return this.element.getId();
    }

    /**
     * サブエレメントIDを取得する。
     * @return サブエレメントID
     */
    public String getSubelementId() {
        if (this.subelement == null) {
            return null;
        }
        return this.subelement.getId();
    }

}
