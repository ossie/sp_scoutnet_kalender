{strip}{* Entfernt alle unn�tigen Leerzeichen und Leerzeilen bis {/strip} *}
{*
	Name:		RSS 2.0 
	Dateiname:	rss2.tpl
	Autor:		Tobias Jordans
	Letzte �nderung: 01.04.2006 (Tobias Jordans)
	Version:	0.0.5
	notwendige Konfiguration: erfolgt in ScoutPress
	Hilfe zum hCalendar-Microformat: http://microformats.org/wiki/hcalendar
	Hilfe zu Smarty: http://smarty.php.net/manual/de/
	Hilfe zu RSS: http://blogs.law.harvard.edu/tech/rss
	Diese Datei basiert auf http://media-cyber.law.harvard.edu/blogs/gems/tech/rss2sample.xml
*}
	{* �bernimmt den URL-Parameter der Quell-Homepage oder setzt ihn auf die Mutter-Instanz. *}
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

{/strip}<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">
	<channel>
		<title>{$kalender.Ident|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} Termine</title>
		<link>{$homepage_url}</link>
		<description>Termine fuer {$kalender.Ident|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} / {$kalender.verband|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}</description>
		<language>de-de</language>
		<pubDate>{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S %Z"}</pubDate>
		<lastBuildDate>{$smarty.now|date_format:"%a, %d %b %Y %H:%M:%S %Z"}</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<category>Pfadfinder</category>
		<generator>Scoutnet-Kalender-Plugin fuer ScoutPress</generator>
{foreach from=$groups item=monat}
{foreach from=$monat.eintraege item=eintrag}
		<item>
			<title>{$eintrag.titel|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} ({$eintrag.startdatum|date_format:"%a %d.%m.%y"} {$eintrag.ort})</title>
			<link>{$homepage_url}&amp;termin={$eintrag.id}</link>
			<description><![CDATA[
				<p>
					Datum: <strong>
					<abbr class="dtstart" title="{$eintrag.startdatum|date_format:"%y%m%d"}T{$eintrag.startzeit|date_format:"%H%M"}+0100">{$eintrag.startdatum|date_format:"%a %d.%m.%y"}</abbr>{if $eintrag.enddatum} &ndash; 
					<abbr class="dtend" title="{$eintrag.enddatum|date_format:"%y%m%d"}T{$eintrag.endzeit|date_format:"%H%M"}+0100">{$eintrag.enddatum|date_format:"%a %d.%m.%y"}</abbr>{/if}
					</strong>
{if $eintrag.startzeit}
					<br />
					Zeit: <strong<{$eintrag.startzeit|date_format:"%H:%M"} h{if $eintrag.endzeit} &ndash; {$eintrag.endzeit|date_format:"%H:%M"} h{/if}</strong>
{/if}
		 		</p>
				<p>
					{if $eintrag.ort}Ort: {$eintrag.ort|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}{else}kein Ort eingetragen{/if}<br />
					Stufe: {$eintrag.stufe.bildlich_scoutnet}<br />
					Kategorie: {$eintrag.kategorie|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}
				</p>
{if $eintrag.info}
				<blockquote>
					{$eintrag.info|nl2br}
				</blockquote>
{/if}
				<p>
					<small>{strip}
					{if $eintrag.autor.vorname || $eintrag.autor.nachname}
						{$eintrag.autor.vorname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} {$eintrag.autor.nachname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}
					{else}
						{$eintrag.autor.nickname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}
					{/if}{/strip}, {$eintrag.kalender.verband|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} {$eintrag.kalender.ebene|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} {$eintrag.kalender.name|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}, {$eintrag.kalender.id}
					</small>
				</p>
			]]></description>
			<author>{strip}{if $eintrag.autor.vorname || $eintrag.autor.nachname}
						{$eintrag.autor.vorname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"} {$eintrag.autor.nachname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}
					{else}
						{$eintrag.autor.nickname|escape:"htmlall"|replace:"&uuml;":"ue"|replace:"&ouml;":"oe"|replace:"&auml;":"ae"|replace:"&Uuml;":"ue"|replace:"&Ouml;":"oe"|replace:"&Auml;":"ae"}
					{/if}{/strip}</author>
			<pubDate>{if isset($eintrag.changed)}{$eintrag.changed|date_format:"%a, %d %b %Y %H:%M:%S %Z"}{else}{$eintrag.created|date_format:"%a, %d %b %Y %H:%M:%S %Z"}{/if}</pubDate>
			<guid isPermaLink="true">{$homepage_url}&amp;termin={$eintrag.id}</guid>
		</item> 
{/foreach}
{/foreach}
	</channel>
</rss>
