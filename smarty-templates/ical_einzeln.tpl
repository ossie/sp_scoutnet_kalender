{strip}{* Entfernt alle unn�tigen Leerzeichen und Leerzeilen bis {/strip} *}
{*
	Name:		iCal-Einzelansicht / ScoutPress
	Dateiname:	ical_einzeln.tpl
	Autor:		Tobias Jordans
	Letzte Änderung: 20.04.2006 (Tobias Jordans)
	Version:	0.0.1
	Hilfe zu Smarty: http://smarty.php.net/manual/de/
	Hilfe zu ICAL:   http://www.ietf.org/rfc/rfc2445.txt#Page136
	
	iCal-Format basiert auf einem aus Outlook 11. exportierten Termin-Eintrag
	// PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
*}
{/strip}BEGIN:VCALENDAR
PRODID:-//Scoutnet//ScoutPress-iCal 0.1 MIMEDIR//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VEVENT
ORGANIZER:{strip}{if $eintrag.autor.vorname || $eintrag.autor.nachname}{$eintrag.autor.vorname} {$eintrag.autor.nachname}{else}{$eintrag.autor.nickname}{/if}{/strip}
DTSTART:{$eintrag.startdatum|date_format:"%Y%m%d"}T{if $eintrag.startzeit}{$eintrag.startzeit|date_format:"%H%M%S"}{else}00:00:00{/if}Z
DTEND:{if $eintrag.enddatum}{$eintrag.enddatum|date_format:"%Y%m%d"}{else}{$eintrag.startdatum|date_format:"%Y%m%d"}{/if}T{if $eintrag.endzeit}{$eintrag.endzeit|date_format:"%H%M%S"}{else}00:00:00{/if}Z
LOCATION:{$eintrag.plz} {$eintrag.ort}
TRANSP:OPAQUE
SEQUENCE:0
UID:scoutpress.de
DTSTAMP:{if isset($eintrag.changed)}{$eintrag.changed|date_format:"%Y%m%d"}T{$eintrag.changed|date_format:"%H%M%S"}{else}{$eintrag.created|date_format:"%Y%m%d"}T{$eintrag.created|date_format:"%H%M%S"}{/if}Z
CATEGORIES:{strip}{foreach from=$eintrag.stufe.bezeichnungen_indexed_array item=stufe key=index}{if $index==1}Wölflinge; {/if}{if $stufe=="Jungpfadfinder"}Jungpfadfinder; {/if}{if $stufe=="Pfadfinder"}Pfadfinder; {/if}{if $stufe=="Rover"}Rover; {/if}{if $stufe=="Leiter"}Leiter; {/if}{/foreach}{$eintrag.kategorie}{/strip}
DESCRIPTION:{$eintrag.info|nl2br|replace:"<strong>":""|replace:"</strong>":""|replace:"<br />":"\n"|replace:'<a href="':""|replace:'">':" "|replace:"\n\n":'\n'|replace:"\n":'\n'}\n\nStufe: {strip}{foreach from=$eintrag.stufe.bezeichnungen_indexed_array item=stufe key=index}{if $index==1}Wölflinge; {/if}{if $stufe=="Jungpfadfinder"}Jungpfadfinder; {/if}{if $stufe=="Pfadfinder"}Pfadfinder; {/if}{if $stufe=="Rover"}Rover; {/if}{if $stufe=="Leiter"}Leiter; {/if}{/foreach}\n\nKategorien: {$eintrag.kategorie}\n\n{$eintrag.kalender.ebene} {$eintrag.kalender.name}{/strip}
SUMMARY:{$eintrag.titel}
PRIORITY:5
CLASS:PUBLIC
BEGIN:VALARM
TRIGGER:-PT120M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR