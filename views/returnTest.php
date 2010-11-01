<?php
/*
 * 	View: Return Text-Strings...
 *	View for sp_scounetKalendarViaXML_CalendarView.php
 *	Created on 08.12.2006
 */

//print_r(get_option('sp_ScoutnetCalendar_cacheForID424'));
$xhtml = "URL: ".(!empty($calendar_data->calendarURL) ? $calendar_data->calendarURL : "aus Cache!");

$xhtml = "<br />start: "; $xhtml = $calendar_data->calFunc->get_dateStart();
$xhtml = "<br />end: "; $xhtml = $calendar_data->calFunc->get_dateEnd();
$xhtml = "<br />update: "; $xhtml = $calendar_data->calFunc->get_dateUpdate();
$xhtml = "<br />title: "; $xhtml = $calendar_data->calFunc->get_title();
$xhtml = "<br />ident: "; $xhtml = $calendar_data->calFunc->get_ident();

$xhtml = "<br /><br />";
foreach($calendar_data->calFunc->get_dates() as $dateID=>$date)
{
	//$xhtml = "<pre>";
	$xhtml = "Datum / Zeit:   ".$calendar_data->dateFunc->get_timeStart($dateID);
	$xhtml = "    bis    ".$calendar_data->dateFunc->get_timeEnd($dateID);
	$xhtml = "<br />Titel (Ort):   ".$calendar_data->dateFunc->get_title($dateID)."  (".$calendar_data->dateFunc->get_zip($dateID)." ".$calendar_data->dateFunc->get_place($dateID).")";
	$xhtml = "<br />FullLoc:  ".$calendar_data->dateFunc->get_locationIfLocation($dateID);
	$xhtml = "<br />MapLink:  "; $calendar_data->dateFunc->echo_maplinkIfLocation($dateID);
	$xhtml = "<br />Kategorien:   ".$calendar_data->dateFunc->get_categories($dateID);
	$xhtml = "<br />LinkedKategroies:   "; $calendar_data->dateFunc->echo_linkedcategories($dateID);
	$xhtml = "<br />Gruppen:   ".$calendar_data->dateFunc->get_groups($dateID);
	$xhtml = "<br />LinkedGruppen:   "; $calendar_data->dateFunc->echo_linkedgroups($dateID);
	$xhtml = "<br />Beschreibung:   ".$calendar_data->dateFunc->get_desc($dateID);
	$xhtml = "<br />Updatedif...:  "; $calendar_data->dateFunc->echo_updateInfoIfUpdated($dateID);
	$xhtml = "<br />Autor:   ".$calendar_data->dateFunc->get_author($dateID);
	$xhtml = "<br />Updater:   ".$calendar_data->dateFunc->get_updater($dateID);
	$xhtml = "<br />Author/Updater:   "; $calendar_data->dateFunc->echo_updaterOrAuthor($dateID);
	$xhtml = "<hr style='border:2px solid black;' /><br />";
	//$xhtml = "</pre>";
}
$xhtml = "<br /><br />";

$xhtml = "<br /><pre>\n";
$xhtml = "calendar_data print_r<br />"; 
print_r($calendar_data);
$xhtml = "</pre>\n";

return $xhtml;
?>