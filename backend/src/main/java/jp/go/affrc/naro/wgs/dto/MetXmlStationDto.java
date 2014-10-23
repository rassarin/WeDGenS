package jp.go.affrc.naro.wgs.dto;

import jp.go.affrc.narc.metxml.Region;
import jp.go.affrc.narc.metxml.Source;
import jp.go.affrc.narc.metxml.Station;

import org.seasar.framework.container.annotation.tiger.Component;
import org.seasar.framework.container.annotation.tiger.InstanceType;

/**
 * MetXML形式のステーション情報DTOクラス。
 * ステーションの親階層のリージョン、データソースを持つデータ構造。
 *
 * @author Mitsubishi Space Software Co.,Ltd.
 * @version $Id: CreateDataRequestDto.java 442 2014-02-17 13:55:31Z watabe $
 */
@Component(instance = InstanceType.PROTOTYPE, name = "metXmlStationDto")
public class MetXmlStationDto {

    /** 親のデータソース。 */
    public Source source = null;

    /** 親のリージョン。 */
    public Region region = null;

    /** ステーション。 */
    public Station station = null;

    /**
     * データソースID、リージョンID、ステーションIDを繋げたステーションパス名を取得する。
     * @return ステーションパス名
     */
    public String getStationPath() {
        if (source != null && region != null && station != null) {
            return source.getId() + "/" + region.getId() + "/" + station.getId();
        } else if (source != null && region == null && station != null) {
            return source.getId() + "/" + station.getId();
        }

        return null;
    }

    /**
     * データソースIDを取得する。
     * @return データソースID
     */
    public String getSourceId() {
        if (this.source == null) {
            return null;
        }
        return this.source.getId();
    }

    /**
     * リージョンIDを取得する。
     * @return リージョンID
     */
    public String getRegionId() {
        if (this.region == null) {
            return null;
        }
        return this.region.getId();
    }

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

}
