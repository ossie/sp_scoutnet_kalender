<?php
/**
 *	Scoutnet-Kalender für ScoutPress
 *
 *	Fenster wird vom RTE-Editor aufgerufen.
 *	Listet alle Kalender-/Termin-Daten und fügt sie in das Editorfenster ein.
 *
 *	Version: 0.3
 *	Autor: Tobias Jordans / tobias@jordans-online.de
 *	
 *	FIXME: Bug: JavaScript fügt den Ausgabewert immer 2x ins den RTE ein. Keine Ahnung warum...
 */

// WP-Standard-Funktionen einfügen
require_once('../../../wp-config.php');

// Funktionen für die Kalender-Daten einfügen
require_once('../../../wp-content/plugins/sp_scoutnetKalender/sp_calendar_data.php');

// Kalender-Infos holen
$calendar_options = new sp_ScoutnetCalendarOption();
$calendar_id = $calendar_options->get_calendarID();
// neues Kalender-Daten-Objekt
$calendar_data = new sp_ScoutnetCalendarData($calendar_id, 'no'); // caching auf 'no' gestellt so dass Daten immer aktuell heruntergeladen werden.

?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Scoutnet-Kalender für ScoutPress</title>
	<script language="javascript" type="text/javascript" src="<?php bloginfo('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php bloginfo('siteurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function insertCalendar() {
		if (window.opener) {
			//var dateOrCalToInsert = "<br />"+document.forms[0].alt.value+"<br />";
			//var dateOrCalToInsert = alt;
			var filter = document.forms[0].filter.options[document.forms[0].filter.selectedIndex].value;
			var terminid = document.forms[0].terminid.options[document.forms[0].terminid.selectedIndex].value;
			var dateOrCalToInsert = "";
			
			if(filter!='') {
				dateOrCalToInsert += "</p><p>[TerminListe="+filter+"]";
			}
			if(terminid!='') {
				dateOrCalToInsert += "</p><p>[Termin="+terminid+"]";
			}
			
			tinyMCE.execCommand('mceBeginUndoLevel');
		
			if (dateOrCalToInsert == "")
				return;
		
			html = dateOrCalToInsert;
			tinyMCE.execCommand("mceInsertContent", false, html);
		
			tinyMCE.execCommand('mceEndUndoLevel');
			top.close();
		}
	}
	</script>
	<style type="text/css">
		#insert, #cancel {
			font: 13px Verdana, Arial, Helvetica, sans-serif;
			height:auto;
			width: auto;
			background-color: transparent;
			background-image: url(<?php bloginfo('siteurl'); ?>/wp-admin/images/fade-butt.png);
			background-repeat: repeat;
			border: 3px double;
			border-right-color: rgb(153, 153, 153);
			border-bottom-color: rgb(153, 153, 153);
			border-left-color: rgb(204, 204, 204);
			border-top-color: rgb(204, 204, 204);
			color: rgb(51, 51, 51);
			padding: 0.25em 1em;
		}
		#insert:active, #cancel:active {
			background: #f4f4f4;
			border-left-color: #999;
			border-top-color: #999;
		}
		/*#terminid {
			font-family: monospace;	font-size: 110%;		
		}*/
		TD.auswahl {
		}
		SELECT.auswahl {
			font-size: 1.3em;
		}
	</style>

</head>
<body onload="tinyMCEPopup.executeOnLoad('init();');document.getElementById('src').focus();" ><!--style="display: none"-->
<form onsubmit="insertCalendar();return false;">
	<table border="0" cellpadding="0" cellspacing="5" width="200">
		<tr>
			<td colspan="2" nowrap class="title">Termin-Liste einfügen:</td>
		</tr>
		<tr>
			<td class="auswahl" nowrap="nowrap">Listen-Filter:</td>
			<td>	
				<select class="auswahl" name="filter" id="filter">	
					<option value="">--auswaehlen--</option>
<?php echo $calendar_data->calFunc->getAll_FilterWordsAsOption("								"); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" nowrap class="title">
				<br />
				<br />
				Einen einzelnen Termin einfügen:
			</td>
		</tr>
		<tr>
			<td class="auswahl" nowrap="nowrap">Termin-Nummer:</td>
			<td>
				<select class="auswahl" name="terminid" id="terminid">
					<option value="">--auswaehlen--</option>
<?php echo $calendar_data->calFunc->getAll_DatesAsOption("								"); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap">
			</td>
			<td>
				<br />
				<br />
				<input style="float: left;" type="submit" id="insert" name="insert" value="Einfügen" onclick="insertCalendar();">
				<span style="color: red; float: right;">
				<br />
				<!--input type="button" id="cancel" name="cancel" value="Abbrechen" onclick="tinyMCEPopup.close();"-->
				<a href="#" onclick="tinyMCEPopup.close();" style="color: red;">Abbrechen</a>
				</span>
			</td>
		</tr>
	</table>
</form>
</body>
</html>
