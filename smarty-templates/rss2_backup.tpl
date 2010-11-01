{strip}{* Entfernt alle unnötigen Leerzeichen und Leerzeilen bis {/strip} *}
{*
	Name:		RSS 2.0 
	Dateiname:	rss2.tpl
	Autor:		Tobias Jordans
	Letzte Änderung: 01.04.2006 (Tobias Jordans)
	Version:	0.0.5
	notwendige Konfiguration: erfolgt in ScoutPress
	Hilfe zum hCalendar-Microformat: http://microformats.org/wiki/hcalendar
	Hilfe zu Smarty: http://smarty.php.net/manual/de/
	Hilfe zu RSS: http://blogs.law.harvard.edu/tech/rss
	Diese Datei basiert auf http://media-cyber.law.harvard.edu/blogs/gems/tech/rss2sample.xml
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
	Generator:     RSS-ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender
	Kalender-Name: {$kalender.Ident} / {$kalender.verband}
	Kalender-ID:   {$kalender.id}
-->
<rss version="2.0">
	<channel>
		<title>{$kalender.Ident} Termine</title>
		<link>{$homepage_url}</link>
		<description>Termine für {$kalender.Ident} / {$kalender.verband}</description>
		<language>de-de</language>
		<pubDate>{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S %Z"}</pubDate>
		<lastBuildDate>{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S %Z"}</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<category>Pfadfinder</category>
		<!--image>see more: http://blogs.law.harvard.edu/tech/rss#ltimagegtSubelementOfLtchannelgt</image-->
		<generator>Scoutnet-Kalender-Plugin für ScoutPress</generator>
		<!--managingEditor>editor@example.com</managingEditor-->
		<!--webMaster>webmaster@example.com</webMaster-->
{foreach from=$groups item=monat}
{foreach from=$monat.eintraege item=eintrag}
		<item>
			<!-- Kalender-ID {$eintrag.kalender.id} -->
			<title>{$eintrag.titel} ({$eintrag.startdatum|date_format:"%a %d.%m.%y"} {$eintrag.ort})</title>
			<link>{$homepage_url}?eintryids={$eintrag.id}</link>
			<description><![CDATA[
				<p>
					Datum: <strong>
					<abbr class="dtstart" title="{$eintrag.startdatum|date_format:"%y%m%d"}T{$eintrag.startzeit|date_format:"%H%M"}+0100">{$eintrag.startdatum|date_format:"%a %d.%m.%y"}</abbr>{if $eintrag.enddatum} – 
					<abbr class="dtend" title="{$eintrag.enddatum|date_format:"%y%m%d"}T{$eintrag.endzeit|date_format:"%H%M"}+0100">{$eintrag.enddatum|date_format:"%a %d.%m.%y"}</abbr>{/if}
					</strong>
{if $eintrag.startzeit}
					<br />
					Zeit: <strong<{$eintrag.startzeit|date_format:"%H:%M"} h{if $eintrag.endzeit} – {$eintrag.endzeit|date_format:"%H:%M"} h{/if}</strong>
{/if}
		 		</p>
				<p>
					{if $eintrag.ort}Ort: {$eintrag.ort}{else}kein Ort eingetragen{/if}<br />
					Stufe: {$eintrag.stufe.bildlich_scoutnet}<br />
					Kategorie: {$eintrag.kategorie}
				</p>
{if $eintrag.info}
				<blockquote>
					{$eintrag.info|nl2br}
				</blockquote>
{/if}
				<p>
					<small>{strip}
					{if $eintrag.autor.vorname || $eintrag.autor.nachname}
						{$eintrag.autor.vorname} {$eintrag.autor.nachname}
					{else}
						{$eintrag.autor.nickname}
					{/if}{/strip}, {$eintrag.kalender.verband} {$eintrag.kalender.ebene} {$eintrag.kalender.name}
					</small>
				</p>
			]]></description>
			<author>{strip}{if $eintrag.autor.vorname || $eintrag.autor.nachname}
						{$eintrag.autor.vorname} {$eintrag.autor.nachname}
					{else}
						{$eintrag.autor.nickname}
					{/if}{/strip}</author>
			<pubDate>{if isset($eintrag.changed)}{$eintrag.changed|date_format:"%a, %d %b %Y %H:%M:%S %Z"}{else}{$eintrag.created|date_format:"%a, %d %b %Y %H:%M:%S %Z"}{/if}</pubDate>
			<guid isPermaLink="true">{$homepage_url}?eintryids={$eintrag.id}</guid>
		</item> 
{/foreach}
{/foreach}
	</channel>
</rss>
