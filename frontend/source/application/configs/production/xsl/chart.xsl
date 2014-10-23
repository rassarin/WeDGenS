<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:output method="html" encoding="utf-8"
		doctype-system="about:legacy-compat" media-type="text/html"
		indent="yes" />

	<xsl:template match="/dataset">
		<html>
			<head>
				<meta charset="UTF-8"/>
				<base href="http://192.168.1.100"/>

				<link rel="stylesheet" href="/wgs/css/jqplot/jquery.jqplot.min.css" type="text/css" media="all"/>
				<script type="text/javascript" src="/wgs/js/vendor/jquery-1.10.2.min.js"></script>
				<!--[if lt IE 9]>
					<script language="javascript" type="text/javascript" src="/wgs/js/vendor/jqplot/excanvas.min.js"></script>
				<![endif]-->
				<script type="text/javascript" src="/wgs/js/vendor/jqplot/jquery.jqplot.min.js"></script>
				<script type="text/javascript" src="/wgs/js/vendor/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>

				<script type="text/javascript">
$(function () {
    var tableId = 0;
    $('table.climate_data').each(function() {
        var dataTable   = this;
        var subElements = {};
        $('.subelement', this).each(function() {
            var classNames   = $(this).attr('class');
            var buffer1      = classNames.split(/\s+/, 2);
            var buffer2      = buffer1[1].split(/_/, 2);
            var elementId    = buffer2[0];
            var subElementId = buffer2[1];
            if (elementId in subElements) {
                subElements[elementId].push(subElementId);
            } else {
                subElements[elementId] = new Array();
                subElements[elementId].push(subElementId);
            }
        });

        $('.element', this).each(function() {
            var valueElement = $(this).attr('id');
            var graphData    = new Array();
            var seriesLabel  = new Array();
            var axesOptions  = {
                xaxis : {
                    label : '日付',
                    renderer : $.jqplot.DateAxisRenderer,
                    tickOptions : {
                        formatString : '%Y/%m/%d'
                    },
                    autoscale: true
                }
            };

            var yaxisCount = '';
            var unitCheck  = {};

            $.each(subElements[valueElement], function(idxSubElement, valueSubElement){
                var subElementGraphData = new Array();
                var id   = valueElement + '_' + valueSubElement;
                var unit = $('th.unit.'  + id, dataTable).text();
                var dateList  = $('td.date', dataTable);
                var valueList = $('td.value.' + id, dataTable);

                $.each(dateList, function(idxDate, valueDate) {
                    var data = new Array();
                    data.push($(valueDate).text());
                    data.push($(valueList[idxDate]).text() * 1.0);
                    subElementGraphData.push(data);
                });
                graphData.push(subElementGraphData);

                var yaxisName = 'y' + yaxisCount + 'axis';
                if (unit in unitCheck) {
                    yaxisName = 'y' + unitCheck[unit] + 'axis';
                } else {
                    unitCheck[unit] = yaxisCount;
                }

                seriesLabel.push({
                    label : $('th.subelement.' + id, dataTable).text(),
                    yaxis : yaxisName
                });
                axesOptions[yaxisName] = {
                    label : unit,
                    autoscale: true
                }
                if (yaxisCount == '') {
                    yaxisCount = 2;
                } else {
                    yaxisCount ++;
                }
            });

            var label    = $('#' + valueElement).text();
            var chartId  = "chart_" + valueElement + "_" + tableId;
            var chartBox = $("<div/>").attr("id", chartId)
                                     .attr("class", "chart");
            $('#graph').append(chartBox);
            var options = {
                series: seriesLabel,
                title : $('caption', dataTable).text() + ' ' + label,
                legend : {
                    show : true,
                    location: 'ne'
                },
                axes : axesOptions
            };

            $.jqplot(chartId, graphData, options);
        });
        tableId ++;
    });
});
				</script>

				<style type="text/css">
					#container {
						padding: 20px;
					}
					div.chart {
						width: 1024px;
						margin-bottom: 20px;
					}
					div.graph_data {
						display: none;
					}
				</style>
			</head>
			<body>
				<div id="container">
					<div id="graph"></div>
				</div>
				<div class="graph_data">
					<xsl:apply-templates select="data" mode="table" />
				</div>
			</body>
		</html>
	</xsl:template>

	<xsl:template match="data" mode="table">
		<table>
			<xsl:attribute name="class"><xsl:value-of select="'climate_data'" /></xsl:attribute>
			<caption>
				<xsl:value-of select="source/name" />
				/
				<xsl:choose>
					<xsl:when test="region">
						<xsl:value-of select="region/name" />
						/
						<xsl:value-of select="station/name" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="station/name" />
					</xsl:otherwise>
				</xsl:choose>
			</caption>
			<xsl:apply-templates select="station" mode="table" />
		</table>
	</xsl:template>

	<xsl:template match="station" mode="table">
		<thead>
			<tr>
				<th rowspan="3">&#160;</th>
				<xsl:for-each select="../element">
					<th>
						<xsl:attribute name="colspan"><xsl:value-of select="count(subelement)" /></xsl:attribute>
						<xsl:attribute name="class">
							<xsl:text>element</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
						<xsl:value-of select="name" />
					</th>
				</xsl:for-each>
			</tr>
			<tr>
				<xsl:for-each select="../element/subelement">
					<th>
						<xsl:variable name="elementId" select="../@id" />
						<xsl:variable name="subElementId" select="@id" />
						<xsl:attribute name="class">
							<xsl:text>subelement </xsl:text>
							<xsl:value-of select="$elementId" /><xsl:text>_</xsl:text><xsl:value-of select="$subElementId" />
						</xsl:attribute>
						<xsl:value-of select="name" />
					</th>
				</xsl:for-each>
			</tr>
			<tr>
				<xsl:for-each select="../element/subelement">
					<th>
						<xsl:variable name="elementId" select="../@id" />
						<xsl:variable name="subElementId" select="@id" />
						<xsl:attribute name="class">
							<xsl:text>unit </xsl:text>
							<xsl:value-of select="$elementId" /><xsl:text>_</xsl:text><xsl:value-of select="$subElementId" />
						</xsl:attribute>
						<xsl:value-of select="@unit" />
					</th>
				</xsl:for-each>
			</tr>
		</thead>

		<tbody>
			<xsl:apply-templates select="." mode="data" />
		</tbody>
	</xsl:template>

	<xsl:template match="station" mode="data">
		<xsl:for-each
			select="../element[position()=1]/subelement[position()=1]/value">
			<xsl:variable name="date" select="@date" />
			<xsl:if test="not(../../../../element/subelement/value[@date=$date]='')">
				<tr>
					<td>
						<xsl:attribute name="class">
							<xsl:text>date </xsl:text>
						</xsl:attribute>
						<xsl:value-of select="$date" />
					</td>
					<xsl:for-each select="../../../element/subelement">
						<td>
							<xsl:variable name="elementId" select="../@id" />
							<xsl:variable name="subElementId" select="@id" />
							<xsl:attribute name="class">
								<xsl:text>value </xsl:text>
								<xsl:value-of select="$elementId" /><xsl:text>_</xsl:text><xsl:value-of select="$subElementId" />
							</xsl:attribute>
							<xsl:value-of select="value[@date=$date]" />
						</td>
					</xsl:for-each>
				</tr>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
