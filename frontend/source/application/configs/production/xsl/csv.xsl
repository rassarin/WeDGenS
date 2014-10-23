<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:output method="text" encoding="utf-8" indent="no" />
	<xsl:variable name="delimiter" select="','" />

	<xsl:template match="/dataset">
		<xsl:apply-templates select="data" mode="table" />
	</xsl:template>

	<xsl:template match="data" mode="table">
		<xsl:text>"</xsl:text>
		<xsl:value-of select="source/name" />
		<xsl:text> / </xsl:text>
		<xsl:apply-templates select="region" mode="caption" />
		<xsl:text>"</xsl:text>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="station" mode="table" />
	</xsl:template>

	<xsl:template match="region" mode="caption">
		<xsl:choose>
			<xsl:when test="../region">
				<xsl:value-of select="name" />
				<xsl:text> / </xsl:text>
				<xsl:value-of select="../station/name" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="../station/name" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="station" mode="table">
		<xsl:for-each select="../element">
			<xsl:text>"</xsl:text>
			<xsl:value-of select="name" />
			<xsl:text>"</xsl:text>
			<xsl:for-each select="subelement">
				<xsl:if test="position() != last()">
					<xsl:value-of select="$delimiter" />
				</xsl:if>
			</xsl:for-each>
			<xsl:if test="position() != last()">
				<xsl:value-of select="$delimiter" />
			</xsl:if>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		<xsl:for-each select="../element/subelement">
			<xsl:text>"</xsl:text>
			<xsl:value-of select="name" />
			<xsl:text>"</xsl:text>
			<xsl:if test="position() != last()">
				<xsl:value-of select="$delimiter" />
			</xsl:if>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		<xsl:for-each select="../element/subelement">
			<xsl:text>"</xsl:text>
			<xsl:value-of select="@unit" />
			<xsl:text>"</xsl:text>
			<xsl:if test="position() != last()">
				<xsl:value-of select="$delimiter" />
			</xsl:if>
		</xsl:for-each>
		<xsl:text>&#xa;</xsl:text>
		<xsl:apply-templates select="." mode="data" />
	</xsl:template>

	<xsl:template match="station" mode="data">
		<xsl:for-each
			select="../element[position()=1]/subelement[position()=1]/value">
			<xsl:variable name="date" select="@date" />
			<xsl:if test="not(../../../../element/subelement/value[@date=$date]='')">
				<xsl:text>"</xsl:text>
				<xsl:value-of select="$date" />
				<xsl:text>"</xsl:text>
				<xsl:text>,</xsl:text>
				<xsl:for-each select="../../../element/subelement">
					<xsl:text>"</xsl:text>
					<xsl:value-of select="value[@date=$date]" />
					<xsl:text>"</xsl:text>
					<xsl:if test="position() != last()">
						<xsl:value-of select="$delimiter" />
					</xsl:if>
				</xsl:for-each>
				<xsl:text>&#xa;</xsl:text>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
