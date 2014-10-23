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
				<style type="text/css">
					table {
						border-collapse: collapse;
						border: thin solid lightgray;
					}
					caption {
						font-size: 0.8em;
						font-weight: bold;
						white-space: nowrap;
					}
					th, td {
						border: thin solid lightgray;
						font-size: 0.8em;
						padding: 1px 3px;
					}
					td {
						text-align: right;
					}
					td.date {
						text-align: left;
					}
				</style>
			</head>
			<body>
				<xsl:apply-templates select="data" mode="table" />
			</body>
		</html>
	</xsl:template>

	<xsl:template match="data" mode="table">
		<table>
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
						<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
						<xsl:value-of select="name" />
					</th>
				</xsl:for-each>
			</tr>
			<tr>
				<xsl:for-each select="../element/subelement">
					<th>
						<xsl:value-of select="name" />
					</th>
				</xsl:for-each>
			</tr>
			<tr>
				<xsl:for-each select="../element/subelement">
					<th>
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
					<td class="date">
						<xsl:value-of select="$date" />
					</td>
					<xsl:for-each select="../../../element/subelement">
						<td>
							<xsl:value-of select="value[@date=$date]" />
						</td>
					</xsl:for-each>
				</tr>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
