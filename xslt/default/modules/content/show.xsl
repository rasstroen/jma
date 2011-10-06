
<xsl:template match="content_module[@action='show' and @mode='random']">
	
</xsl:template>

<xsl:template match="content_module[@action='show' and not(@mode)]">
    <xsl:apply-templates select="picture" mode="picture-item"></xsl:apply-templates>
</xsl:template>

<xsl:template match="*" mode="picture-item">
    <div class="picture-item title">
        <xsl:value-of select="@title" />
    </div>
     <div class="picture-item image">
        <img src="{@source}" />
    </div>
</xsl:template>
