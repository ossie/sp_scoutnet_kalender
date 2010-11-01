{strip}{* Entfernt alle unnötigen Leerzeichen und Leerzeilen bis {/strip} *}
{*
	Name:		RSS 1.0 
	Dateiname:	rss10.tpl
	Autor:		Tobias Jordans
	Letzte Änderung: 01.04.2006 (Tobias Jordans)
	Version:	0.0.4
	notwendige Konfiguration: erfolgt in ScoutPress
	Hilfe zum hCalendar-Microformat: http://microformats.org/wiki/hcalendar
	Hilfe zu Smarty: http://smarty.php.net/manual/de/
	Hilfe zu RSS: http://blogs.law.harvard.edu/tech/rss
*}
	{* Übernimmt den URL-Parameter der Quell-Homepage oder setzt ihn auf die Mutter-Instanz. *}
	{if isset($url_parameters.homepage_url)}
		{assign var="homepage_url" value=$url_parameters.homepage_url}
	{else}
		{assign var="homepage_url" value="http://mutter.scoutpress.de/kalender/"}
	{/if}

	{if $groups.jahrmonat}
		{assign var="groups" value="`$groups.jahrmonat`"}
	{/if}
	{* assign var="lastmodified" value="1970-01-01 00:00:00" *}
	{* TODO: Im Array [eintraege] gibt es [created] => 2005-09-30 13:56:15 und [changed] => 2005-09-30 13:56:15 *}

{/strip}<?xml version="1.0" encoding="UTF-8" ?>
<!-- 
	Generator:		RSS-ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender
	Kalender-Name:	{$kalender.Ident} / {$kalender.verband}
	Kalender-ID: 	{$kalender.id}
-->
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns" xmlns="http://purl.org/rss/1.0/"> 

	<channel rdf:about="{$homepage_url}">
		<title>{$kalender.Ident} Termine</title>
		<link>http://www.dpsg-langerwehe.de/MEINE-SEITE</link>
		<description>Termine für {$kalender.Ident} / {$kalender.verband}</description>
		<lastBuildDate>{$smarty.now|date_format:"%Y%m%dT%H%M%SZ"}</lastBuildDate>
		<language>de-de</language>
		<items>
			<rdf:Seq>
{foreach from=$groups item=monat}{foreach from=$monat.eintraege item=eintrag}
				<rdf:li rdf:resource="{$homepage_url}?eintryids={$eintrag.id}"/>
{/foreach}{/foreach}
			</rdf:Seq>
		</items>
	</channel>

{foreach from=$groups item=monat}
{foreach from=$monat.eintraege item=eintrag}
	<item rdf:about="{$homepage_url}?eintryids={$eintrag.id}">
		<title>
			{$eintrag.titel} ({$eintrag.startdatum|date_format:"%a %d.%m.%y"} {$eintrag.ort})
		</title>
		<link>{$homepage_url}?eintryids={$eintrag.id}</link>
		<description>
			<p>
				Datum: <strong>
				<abbr class="dtstart" title="{$eintrag.startdatum|date_format:"%y%m%d"}T{$eintrag.startzeit|date_format:"%H%M"}+0100">{$eintrag.startdatum|date_format:"%a %d.%m.%y"}</abbr>{if $eintrag.enddatum} – 
				<abbr class="dtend" title="{$eintrag.enddatum|date_format:"%y%m%d"}T{$eintrag.endzeit|date_format:"%H%M"}+0100">{$eintrag.enddatum|date_format:"%a %d.%m.%y"}</abbr>{/if}
				</strong>
{if $eintrag.startzeit}
				<br />
				Zeit: <strong>{$eintrag.startzeit|date_format:"%H:%M"} h{if $eintrag.endzeit} – {$eintrag.endzeit|date_format:"%H:%M"} h{/if}</strong>
{/if}
	 		</p>
			<p>
				{if $eintrag.ort}Ort: {$eintrag.ort}{else}kein Ort eingetragen{/if}<br />
				Stufe: <![CDATA[{$eintrag.stufe.bildlich_scoutnet}]]><br />
				Kategorie: {$eintrag.kategorie}
			</p>
			<blockquote>
				<![CDATA[{$eintrag.info|nl2br}]]>
			</blockquote>
			<p>
				<small>{strip}
				{if $eintrag.autor.vorname || $eintrag.autor.nachname}
					{$eintrag.autor.vorname} {$eintrag.autor.nachname}
				{else}
					{$eintrag.autor.nickname}
				{/if}{/strip}, {$eintrag.kalender.ebene} {$eintrag.kalender.name}
				</small>
			</p>
		</description>
		{foreach from=$eintrag.keywords item=kategorie}
		<dc:subject>{$kategorie}</dc:subject>
		{/foreach}
		<dc:creator>{strip}{if $eintrag.autor.vorname || $eintrag.autor.nachname}
					{$eintrag.autor.vorname} {$eintrag.autor.nachname}
				{else}
					{$eintrag.autor.nickname}
				{/if}{/strip}</dc:creator>
	</item> 
{/foreach}
{/foreach}
</rdf:RDF>
