<?php
/*
 *	Plugin Name:  Scoutnet-Kalender für ScoutPress
 *	Plugin URI:   http://lernen.scoutpress.de/plugin/sp_scoutnetKalender
 *	Description:  Integriert den Scoutnet-Kalender in ScoutPress. Mehr erfahrt ihr <a href="/wp-admin/admin.php?page=sp_scoutnetKalender/sp_scoutnetKalenderViaXML.php">auf der Kalender-Seite</a>.
 *
 *	Version:      0.4
 *	Changelog:    0.1 / 2006-04
 *                0.2 / 2006-11-27: nur noch ein Optionen-Wert in der Datenbank als array
 *                0.3 / 2006-11-27: umlaute-kodierungs-problem gelöst und xml-line fertig eingerichtet
 *	Demo:         http://blog.scoutpress.de
 *
 *	Author:       Tobias Jordans
 *	Author URI:   http://www.scoutpress.de


 *	Template-Integration: TODO

 *	Copyright 2006 / Tobias Jordans (http://www.fly.ingsparks.de)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

 *	Notes:
	- Documentation
	  I tried to follow the WP-Inline-Documentation-Convention // http://codex.wordpress.org/Inline_Documentation
	  @param	type	$varname	description
	  @return	type	description
	  @deprecated	description
	  @deprec	alias for deprecated
	  @todo		description of what todo
	  @var		type	a data type for a class variable


 *	Hilfe-Seiten
	-	pers. Einstellungen als "option" speichern
		siehe http://codex.wordpress.org/Writing_a_Plugin#Saving_Plugin_Data_to_the_Database
		add_option($name, $value, $description, $autoload);
	-	Tabellen erstellen: http://codex.wordpress.org/Creating_Tables_with_Plugins
	-	PluginAPI: http://codex.wordpress.org/Plugin_API
		Hooks-Direktory: http://wphooks.flatearth.org/

 */

require_once (dirname(__FILE__).'/sp_calendar_data.php');
require_once (dirname(__FILE__).'/sp_calendar_adminview.php');
require_once (dirname(__FILE__).'/sp_calendar_calendarview.php');
require_once (dirname(__FILE__).'/sp_calendar_calendarview_simile.php'); // SIMILE-Timeline
require_once (dirname(__FILE__).'/sp_calendar_error.php');
require_once (dirname(__FILE__).'/sp_calendar_help.php');
require_once (dirname(__FILE__).'/sp_buttonsnap-integration.php'); // fügt den TinyMCE-Button hinzu

sp_ScoutnetCalendar_setupOptions();

/**
 *	Admin-Menu anzeigen
 *	Referenz: http://codex.wordpress.org/Adding_Administration_Menus
 */
add_action('admin_menu', 'sp_scoutnetKalenderMenu');

function sp_scoutnetKalenderMenu() {
	//Help: add_menu_page(page_title, menu_title, access_level, file, [function]);
	add_menu_page('Scoutnet-Kalender / Übersicht', 'Kalender', 2, __FILE__, 'sp_ScoutnetCalendar_MenuUebersicht');

	//Help: add_submenu_page(parent, page_title, menu_title, access_level/capability, file, [function]);
	add_submenu_page(__FILE__, 'Scoutnet-Kalender / Optionen', 'Optionen', 2, 'kalender_optionen', 'sp_ScoutnetCalendar_MenuOptionen');
	add_submenu_page(__FILE__, 'Scoutnet-Kalender / Hilfe', 'Hilfe', 2, 'kalender_hilfe', 'sp_ScoutnetCalendar_MenuHilfe');

	sp_ScoutnetCalendar_MenuVerschieben();
	sp_ScoutnetCalendar_setupOptions();
}

/**
 *	Menüpunkt: Übersicht
 */
function sp_ScoutnetCalendar_MenuUebersicht() {
	$adminView = new sp_ScoutnetCalendarAdminView;
	$adminView->MenuUebersicht();
}
/**
 *	Menüpunkt: Optionen
 */
function sp_ScoutnetCalendar_MenuOptionen() {
	$adminView = new sp_ScoutnetCalendarAdminView;
	$adminView->MenuOptionen();
}
/**
 *	Menüpunkt: Hilfe
 */
function sp_ScoutnetCalendar_MenuHilfe() {
	$adminView = new sp_ScoutnetCalendarAdminView;
	$adminView->MenuHilfe();
}
/**
 *	Verschieben des Menueintrages:
 *	Daten aus dem oben angelegten Kalender-Array in ein neues Array mit der ID = Position 8 füllen
 *	und das alte array löschen // Referenz: http://de3.php.net/manual/de/function.array-search.php
 */
function sp_ScoutnetCalendar_MenuVerschieben() {
	global $menu; //, $submenu;
	for ($i = 0; $i < 60; $i ++) {
		if ($menu[$i][0] == "Kalender") {
			$menu[8] = $menu[$i];
			unset ($menu[$i]);
		}
	}
}

/**
 *	Richtet die Default-/Leeren Datenbankeinträge ein.
 *	add_option($name, $value, $description, $autoload);
 */
function sp_ScoutnetCalendar_setupOptions() {
	$options = get_option('sp_ScoutnetCalendar_options');
	if(empty($options))
	{

		// Holt die Kalender-ID aus der Datenbank. Dabei wird nach Schlüsselwörtern gesucht.
		// Wird keine ID gefunden, erscheint die Fehlermeldung die auch in sp_scounetKalenderViaXML_CalendarView aufgerufen wird
		$calendaroptions = new sp_ScoutnetCalendarOption;
		$calendar_page_id = $calendaroptions->get_calendarPageIDFromDB();
		if(!$calendar_page_id)
		{	// Fehlermeldung ausgeben
			$calendar_admin = new sp_ScoutnetCalenderErrors();
			$calendar_admin->NoCalendarPageError();
		}

		// Das Optionen-Array
		$options['id'] = '';
		$options['cache_duration'] = '300';
		$options['calendar_page_id'] = $calendar_page_id; // Speichert die page_id damit einzelne Termine richtig angezeigt werden...

		update_option('sp_ScoutnetCalendar_options', $options, 'default calendar-id, refresh-time in minutes, calendar-page-id', 'yes');
	}
}


/**
 *	Holt die Kalender-Optionen aus der Datenbank
 */
function sp_ScoutnetCalendar_getOptions()
{
	$options = get_option('sp_ScoutnetCalendar_options');
	return $options;
}


/**
 *	Überprüft ob der Kalender konfiguriert wurde / die Kalender-ID geändert wurde.
 */
function sp_ScoutnetCalendar_isConfigured() {
	$options = sp_ScoutnetCalendar_getOptions();
	if ($options['id'])
		return TRUE;
	else
		return FALSE;
}

function sp_ScoutnetCalender_getFilters() 
{
	
    $view = new sp_ScoutnetCalenderView();
    $urlOfThisSite = $view->urlToCalendarPage;

	$ebenenup = empty($_REQUEST['ebenenup'])?0:$_REQUEST['ebenenup'];
	$filter = empty($_REQUEST['filter'])?'':$_REQUEST['filter'];
	$startdatum = (empty($_REQUEST['start_datum'])?'':$_REQUEST['start_datum']);
		
	$filters=array(
		'wölflinge' => 'Wölflinge',
		'jungpfadfinder' => 'Juffis',
		'pfadfinder' => 'Pfadis',
		'rover' => 'Rover',
		'vorstände' => 'Vorstände',
		'' => 'Alle'
	);
	
	$ebenen=array(
		'0' => 'Bezirk Ruhr-Sauerland',
		'1' => 'Diözesanverband Paderborn',
		'2' => 'Region West',
		'3' => 'DPSG',
		'4' => 'RDP / RdP'
	);
?>
	<form method="POST" action="<?php echo $urlOfThisSite; ?>">bis <select onchange="this.form.submit();" name="ebenenup">
<?php
	foreach ($ebenen as $id => $name)
	{
		echo '<option value="'.$id.'"'.((intval($ebenenup)==intval($id))?'selected ':'').'> '.$name.' </option>';
	}
?>	 </select> anzeigen
	<input type="hidden" name="start_datum" value="<?php echo $startdatum;?>"/>
	<input type="hidden" name="filter" value="<?php echo $filter;?>"/>
	<input type="hidden" name="p" value="<?php echo $_REQUEST['p'];?>"/>
	<input type="hidden" name="c" value="<?php echo $_REQUEST['c'];?>"/>
	</form>
	<br />
	für die Stufe<br /><br />
	
	<ul id="filter-list">
<?php
	foreach ($filters as $url => $name)
	{
?>
		<li<?php if ($filter==$url) echo ' class="current-cat"';?>><a href="<?php echo $urlOfThisSite.'&start_datum='.$startdatum.'&filter='.$url.'&ebenenup='.$ebenenup; ?>"><?php echo $name;?></a></li>
<?php		
	}
?>
	</ul>
<?php

}

function sp_ScoutnetCalender_getMonatsubersicht($startDatum='')
{
    $view = new sp_ScoutnetCalenderView();
    $urlOfThisSite = $view->urlToCalendarPage;

    if(empty($startDatum))
		{	// Startdatum auf vorgestern setzen
			$startDatum = date('Y-m-d', strtotime("-2 days"));
		}
		else
		{	// Startdatum auf Wert aus der URL setzen (wird an Funktion übergeben)
			$startDatum = date('Y-m-d', strtotime($startDatum));
		}
?>
  		<div class="monateauswahl">
      <p>
    		<span class="head">
<?php
    		  echo "Monate ".date('Y').": ";
?>
    		</span>
<?php
			// Monats-Liste oberhalb des Kalenders
			$monthListMax = 12;
			$monthListeYear2 = date('Y')+1;
			for($i=1; $i<=12; $i++)
			{
				$currentMonth = date('Y')."-$i-01";
				if(date("Y-m", strtotime($startDatum))==date('Y-m', strtotime($currentMonth)))
				{	echo "\t\t\t".'<span class="current">'.mysql2date('M', $currentMonth)."</span>\n";
				}	else
				{	echo "\t\t\t".'<a title="Nur Daten ab '.mysql2date('F', $currentMonth).' anzeigen..." href="'.$urlOfThisSite.'&amp;start_datum='.mysql2date('Y-m-d', $currentMonth).'">';
					echo mysql2date('M', $currentMonth)."</a>\n";
				}
				if($i == $monthListMax)
				{
					// Zeile für zweites Jahr nicht ausblenden, wenn Startdatum im zweiten Jahr liegt.
					if(date("Y", strtotime($startDatum))==date('Y', strtotime("$monthListeYear2-$i-01")))
					{
?>
			<span id="mehrtermine">
<?php
					}
					else
					{
?>
			<span id="mehrtermineLink">(<a href="#nurjavascript" onclick="document.getElementById('mehrtermine').style.display = 'inline';document.getElementById('mehrtermineLink').style.display = 'none';"><?php echo $monthListeYear2 ?>...</a>)</span>

			<span id="mehrtermine" class="ausblenden">
<?php
					}
?>

<?php
				}
			}
?>
			<br />
      <span class="head">
<?php
			echo "Monate ".$monthListeYear2.": ";
?>
      </span>
<?php
			for($i=1; $i<=12; $i++)
			{
				//if(date("$monthListeYear2-m", strtotime($startDatum))==date('Y-m', strtotime("2006-$i-01")))
				if(date("Y-m", strtotime($startDatum))==date('Y-m', strtotime("$monthListeYear2-$i-01")))
				{	echo "\t\t\t".'<span class="current">'.mysql2date('M', "$monthListeYear2-$i-01")."</span>\n";
				}	else
				{	echo "\t\t\t".'<a class="mehrtermine" href="'.$urlOfThisSite.'&amp;start_datum='.mysql2date('Y-m-d', "$monthListeYear2-$i-01").'">';
					echo mysql2date('M', "$monthListeYear2-$i-01")."</a>\n";
				}
			}
?>
			</span>
			</p>
		</div> <!-- .monateauswahl -->
<?php
}

function sp_ScoutnetCalender_getMonatsubersichtInline($startDatum='',$filter,$ebenenup)
{
    $view = new sp_ScoutnetCalenderView();
    $urlOfThisSite = $view->urlToCalendarPage;

    if(empty($startDatum))
		{	// Startdatum auf vorgestern setzen
			$startDatum = date('Y-m-d', strtotime("-2 days"));
		}
		else
		{	// Startdatum auf Wert aus der URL setzen (wird an Funktion übergeben)
			$startDatum = date('Y-m-d', strtotime($startDatum));
		}
		$startJahr = date('Y',strtotime($startDatum));
?>
  		<div class="monateauswahl_inline">
      <p>
    		<span class="head">
				Jahr&nbsp;<select id='jahr' name='jahr' onchange="$('#monate_<?php echo date('Y');?>').hide(); $('#monate_<?php echo (date('Y')+1);?>').hide(); $('#monate_' + $('#jahr').val()).show(); ">;
<?php
				  echo "<option value='".date('Y')."'>".date('Y')."</option>";
				  echo "<option value='".(date('Y')+1)."'".(($startJahr==(date('Y')+1))?" selected=\"1\"":"").">".(date('Y')+1)."</option>";
			  echo "</select>";
			  
?>
    		</span>
			<span id="monate_<?php echo date('Y');?>">
<?php
			// Monats-Liste oberhalb des Kalenders
			$monthListMax = 12;
			$monthListeYear2 = date('Y')+1;
			for($i=1; $i<=12; $i++)
			{
				$currentMonth = date('Y')."-$i-01";
				if(date("Y-m", strtotime($startDatum))==date('Y-m', strtotime($currentMonth)))
				{	echo "\t\t\t".'<span class="current">'.mysql2date('M', $currentMonth)."</span>\n";
				}	else
				{	echo "\t\t\t".'<a title="Nur Daten ab '.mysql2date('F', $currentMonth).' anzeigen..." href="'.$urlOfThisSite.'&amp;start_datum='.mysql2date('Y-m-d', $currentMonth).'&filter='.$filter.'&ebenenup='.$ebenenup.'">';
					echo mysql2date('M', $currentMonth)."</a>\n";
				}
			}
?>
			</span>
			<span id="monate_<?php echo date('Y')+1;?>">
<?php
			for($i=1; $i<=12; $i++)
			{
				//if(date("$monthListeYear2-m", strtotime($startDatum))==date('Y-m', strtotime("2006-$i-01")))
				if(date("Y-m", strtotime($startDatum))==date('Y-m', strtotime("$monthListeYear2-$i-01")))
				{	echo "\t\t\t".'<span class="current">'.mysql2date('M', "$monthListeYear2-$i-01")."</span>\n";
				}	else
				{	echo "\t\t\t".'<a class="mehrtermine" href="'.$urlOfThisSite.'&amp;start_datum='.mysql2date('Y-m-d', "$monthListeYear2-$i-01").'&filter='.$filter.'&ebenenup='.$ebenenup.'">';
					echo mysql2date('M', "$monthListeYear2-$i-01")."</a>\n";
				}
			}
?>
			</span>
			</p>
		</div> <!-- .monateauswahl -->
<?php
				// Zeile für zweites Jahr nicht ausblenden, wenn Startdatum im zweiten Jahr liegt.
				if($startJahr==(date('Y')+1))
				{
?>
		<script>
			$('#monate_<?php echo date('Y');?>').hide();
			$('#monate_<?php echo date('Y')+1;?>').show();
		</script>
<?php
				}
				else
				{
?>
		<script>
			$('#monate_<?php echo date('Y');?>').show();
			$('#monate_<?php echo date('Y')+1;?>').hide();
		</script>
<?php
				}

}

/**
 *	Ausgabe der Kalender/Termin/Test-Daten zur Verwendung im Template
 *	@param	string	$viewType	Schaltet die Ausgabe um. Werte: test, kalender (default), termin, terminExcerpt, inlineTermin, inlineTerminExcerpt
 *	@param	int		$kalenderID	Nummer des ScoutnetKalenders der angezeigt werden soll. Default-Wert wird auf den Kalender der Optionen gesetzt
 *	@param 	int		$termindID	Nummer des Termins der angezeigt werden soll.
 *	@param	string	$startDate	Datumsstring Y-m-d. Wird von der kalender.php aus der URL ausgelesen und übergeben.
 *	@param	string	$endDate	Datumsstring // zur Zeit inaktiv
 *	@param	string	$filter		Für die Kalender-Ansicht. Filtert die Einträge. Default: Aus.
 */
function sp_ScoutnetCalendar($viewType = "kalender", $kalenderID, $terminID = '', $startDate = '', $endDate = '', $filter = '', $ebenenup=0 , $cache_yes_or_no = '')
{
  // Ist Kalender überhaupt konfiguriert?
	if (!sp_ScoutnetCalendar_isConfigured())
	{	// Fehlermeldung ausgeben
		$calendar_error = new sp_ScoutnetCalenderErrors();
		return $calendar_error->ConfigureCalenderFirst_InlineMessage();
	}
	else
	{	// Kalender-Daten holen und Anzeige starten

		// Das Kalender-Template ruft diese Funktion direkt auf und hat daher keine Kalender-ID
		if(empty($kalenderID))
		{	// Kalender-ID setzen wenn keine spezielle ID übermittelt wurde...
			$options = get_option('sp_ScoutnetCalendar_options');
			$kalenderID = $options['id'];
		}

		// Daten holen
		$calendar_data = new sp_ScoutnetCalendarData($kalenderID, $cache_yes_or_no, $ebenenup);

		// Sind daten zum Anzeigen vorhanden, hat also das Daten-Holen zu Beginn funktioniert?
		if (empty($calendar_data->calendarData)) {
			$calendar_admin = new sp_ScoutnetCalenderErrors();
			$calendar_admin->NoDataError();
			return FALSE;

		// Dann können die angefragten Fälle abgearbeitet werden...
		} else {
			$calenderView = new sp_ScoutnetCalenderView();
			switch ($viewType) {
				case "test" :
					$result = $calenderView->returnTest($calendar_data, $kalenderID, $startDate, $endDate, $filter);
					break;
				case "kalender" :
					$result = $calenderView->returnCalender($calendar_data, $kalenderID, $startDate, $endDate, $filter);
					break;
				case "inlineKalender" :
					$result = $calenderView->returnInlineCalender($calendar_data, $kalenderID, $startDate, $endDate, $filter);
					break;
				case "kalenderExcerpt" :
					$result = $calenderView->returnCalendarExcerpt($calendar_data, $kalenderID, $startDate, $endDate, $filter);
					break;
				case "termin" :
					$result = $calenderView->returnDate($calendar_data, $terminID);
					break;
				case "inlineTermin" :
					$result = $calenderView->returnInlineDate($calendar_data, $terminID);
					break;
				case "inlineTerminExcerpt" :
					$result = $calenderView->returnInlineDateExcerpt($calendar_data, $terminID);
					break;
			}
		}
		return $result;
	}
}

/**
 *	Text nach Kalender-Tags filtern
 */
// Funktion am Fuß der Datei.
// Dort auch Info, warum der filter deaktivert ist...
add_filter('the_content', 'sp_ScoutnetCalendar_contentFilter');
add_filter('category_description', 'sp_ScoutnetCalendar_categoryFilter');

/**
 *	Filtert den Post-Text nach dem Suchworten 'TerminListe=' und 'Termin=' und ersetzt sie durch die Ausgabe der Kalender-Filter und inlineDate-Funktion.
 *	Abhängig davon, ob sie auf einer post/page oder in einem excerpt angezeigt werden.
 *	@param	string	$viewType	Schaltet die Ausgabe-Funktionen um. Werte: single und excerpt
 *	@param	string	$content	Inhalt des Posts. Wird normalerweise von add_filter 'the_content' übergeben, hier jedoch manuel im Template
 *	@param	int		$kalenderID	Nummer des ScoutnetKalenders der angezeigt werden soll. Default-Wert wird auf den Kalender der Optionen gesetzt
 */
function sp_ScoutnetCalendar_categoryFilter($content, $viewType = 'single', $kalenderID = '') {
	return sp_ScoutnetCalendar_contentFilter($content, $viewType, $kalenderID, $isCategoryDescription = TRUE);
}
 // TODO: Abfangen, ob termin=x existiert. Wenn X nicht existiert, Fehler ausgeben...
function sp_ScoutnetCalendar_contentFilter($content, $viewType = 'single', $kalenderID = '', $isCategoryDescription = FALSE) {

	// Löscht blöde <p>-Tags
	//$content = sp_ScoutnetCalendar_nasty_p_filter($content);

	// Aufruf erfolgt für the_content, nicht für category_description
	if(!$isCategoryDescription)
	{	// Umschalten zwischen Vorschau und Gesamtansicht...
		if(is_single() || is_page())
		{	// Aufruf erfolgt aus einem Artikel oder einer Seite
			$viewType = 'single';
		} else
		{	// Aufruf für einen Excerpt / in einer Liste
			$viewType = 'excerpt';
		}
	}

	// Termin-Liste nach Filter
	// Funktion von MyGallery geliehen
	$search = "/\[TerminListe=([A-Za-z0-9\-\_^ ]+)\]/";
	if (preg_match($search, $content))
	{
		preg_match_all($search, $content, $temp_array);
		if (is_array($temp_array[1]))
		{
			foreach ($temp_array[1] as $filter)
			{
				$search = "/\[TerminListe=".$filter."\]/";
				if($viewType == 'single')
					$replace = sp_ScoutnetCalendar('inlineKalender', $kalenderID, '', '', '', $filter);
				elseif($viewType == 'excerpt')
					$replace = sp_ScoutnetCalendar('kalenderExcerpt', $kalenderID, '', '', '', $filter);
				$content = preg_replace($search, $replace, $content);
			}
		}
	}

	// CC von Termin-Liste nach Filter
	// Funktion von MyGallery geliehen
	$search = "/\[Kalender=([A-Za-z0-9\-\_^ ]+)\]/";
	if (preg_match($search, $content))
	{
		preg_match_all($search, $content, $temp_array);
		if (is_array($temp_array[1]))
		{
			foreach ($temp_array[1] as $filter)
			{
				$search = "/\[Kalender=".$filter."\]/";
				if($viewType == 'single')
					$replace = sp_ScoutnetCalendar('inlineKalender', $kalenderID, '', '', '', $filter);
				elseif($viewType == 'excerpt')
					$replace = sp_ScoutnetCalendar('kalenderExcerpt', $kalenderID, '', '', '', $filter);
				$content = preg_replace($search, $replace, $content);
			}
		}
	}

	// einzelne Termine
	// Funktion von MyGallery geliehen
	$search = "/\[Termin=([A-Za-z0-9\-\_]+)\]/";
	if (preg_match($search, $content))
	{
		preg_match_all($search, $content, $temp_array);
		if (is_array($temp_array[1]))
		{
			foreach ($temp_array[1] as $terminid)
			{
				$search = "/\[Termin=".$terminid."\]/";
				if($viewType == 'single')
					$replace = sp_ScoutnetCalendar('inlineTermin', '', $terminid, '', '', $filter);
				elseif($viewType == 'excerpt')
					$replace = sp_ScoutnetCalendar('inlineTerminExcerpt', '', $terminid, '', '', $filter);
				$content = preg_replace($search, $replace, $content);
			}
		}
	}
	return $content;
}

/**
 *	Script via MyGallery
 *	nasty_p_filter(), mygalleryfunctions.php, Line 238
 */
/*function sp_ScoutnetCalendar_nasty_p_filter($mystring) {

	$search = "/<p>(|\s)(\[TerminListe=\w+\]|\[Termin=\d+\])(|\s)<\/p>/";
	$replace = "$2";
	$mystring = preg_replace ($search, $replace, $mystring);

	$search="/<p>(|\s)(\[TerminListe=\w+\]|\[Termin=\d+\])(|\s||<br \/>\s)(.*)(|\s)<\/p>/";
	$replace = "$2<p>$8</p>";
	$mystring = preg_replace ($search, $replace, $mystring);

	$search="/<p>(.*|.*\n)(\[TerminListe=\w+\]|\[Termin=\d+\])(|\s)(.*|.*\n.*\n)(|\s)<\/p>/";
	$replace = "<p>$1</p>$2<p>$8</p>";
	$mystring = preg_replace ($search, $replace, $mystring);

	$search="/<p>(.*|.*\n)(\[TerminListe=\w+\]|\[Termin=\d+\])(|\s)<\/p>/";
	$replace = "<p>$1</p>$2";
	$mystring = preg_replace ($search, $replace, $mystring);

	$search="/<p>(|\s)(\[TerminListe=\w+\]|\[Termin=\d+\])(.*)(\s<br \/>|<br \/>)/";
	// changed on 15.02.2006 - <br /> causes problems $replace = "$2<br /><p>$7";  //modified on 24.01.2006 18:43 maybe the <br /> is not usefull
	$replace = "$2<p>$7";

	$search="/<p>(.*)(\s<br \/>|<br \/>)/";
	$replace="<p>$2</p>";
	$mystring = preg_replace ($search, $replace, $mystring);

	return $mystring;
}*/
?>