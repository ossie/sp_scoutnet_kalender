<?php
/*
 *	Class for:     ScoutPress Scoutnet-Kalender
 *	Class-Name:    View-Classes mit XHTML-Ausgabe für
 *	               - Test
 *	               - Kalender-Liste incl. Filter + Excerpt-Variante
 *	               - Einzel-Termin
 *	               - Termin Inline + Excerpt-Variante
 *	Description:   Enthält die Klassen, für das Erstellen und Manipulieren der Kalenderdaten
 *	View-Version:  0.9
 *	Changelog:     1.0 / 2006-12-09: Funktionsinhalte in einzelne Dateien ausgelagert im view-ordner. Kleine Verbesserungen.
 *	               0.9 / 2006-05-03
 *	View-Author:   Tobias Jordans

 *	Copyright:     Siehe at sp_scoutnetcalendar.php
 *
 */

class sp_ScoutnetCalenderView
{
	/**
	 *	@var	string	$calendarPageID			ID der Page mit dem Namen 'kalender', 'termine' oder 'termin'
	 *	@var	string	$urlToCalendarPage		Setzt den Pfad der Kalender-Page. 
	 *											Wird beim Link auf die Einzel-Terminansicht verwendet
	 *	@var	string	$calendarID				Hält die Scoutnet-Kalender-ID
	 */
	var $calendarPageID;
	var $urlToCalendarPage;
	var $calendarSmartyURL;
	var $calendarID;
	
	/**
	 *	Konstruktormethode
	 */
	function sp_ScoutnetCalenderView()
	{	
		// Page-ID für den Kalender holen
		$options = new sp_ScoutnetCalendarOption;
		$this->calendarPageID = $options->get_calendarPageID();
		
		if($this->calendarPageID)
		{	// Page-URL für den Kalender setzen
			$permalink = get_settings('permalink_structure');
			if ($permalink=='')
			{	// Permalinks inaktiv
				$this->urlToCalendarPage = get_permalink($this->calendarPageID)."";
			} else
			{	// Permalinks aktiv
				$this->urlToCalendarPage = get_permalink($this->calendarPageID)."?placebo=true";
			}
		}
		else
		{	// Keine Kalender-Seite gefunden
			$adminError = new sp_ScoutnetCalenderErrors;
			$adminError->NoCalendarPageError();
		}
	}
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Test-Ausgabe der Kalender- und Termin-Funktionen
	 *	Nicht besonders gute Testausgabe der Funktionen... Zur Zeit nicht vollständig.
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	@param	object	$calendar_data	... siehe unten
	 */
	function echoTest($calendar_data, $kalenderID, $startDate, $endDate, $filter)
	{
		echo returnTest($calendar_data, $kalenderID, $startDate, $endDate, $filter);
	}
	function returnTest($calendar_data, $kalenderID, $startDate, $endDate, $filter)
	{
		include('views/returnTest.php');
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Ausgabe der Kalender-Übersicht
	 *	
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	/**
	 *	For Excerpt-View
	 *	Kalender-Termin-Liste für den Aufruf im Excerpt. Anderfalls würde der Excerpt mit Kalender-HTML zugemüllt.
	 *	Gibt nur die Information aus, dass ein Kalender in der Detailansicht existiert...
	 */
	function returnCalendarExcerpt($calendarData, $kalenderID, $startDatum, $endDatum, $filter)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoCalendarExcerpt($calendarData, $kalenderID, $startDatum, $endDatum, $filter);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoCalendarExcerpt($calendarData, $kalenderID, $startDatum, $endDatum, $filter)
	{
?>
		<p>
			Kalender mit Termin-Filter »<?php echo $filter; ?>«
		</p>
<?php
	}
	
	
	/**
	 *	For FullPost-View
	 *	Kalender-Termin-Liste
	 *	@param	object	$calendarData	Die gesamten Kalender-Daten incl dateFunc und calFunc für Daten-Manipulationen
	 *	@param	int		$kalenderID		TODO: optionale ID des Kalenders // Muss noch aktiviert werden damit auch andere Kal angezeigt werden können
	 *	@param	string	$startDate		optionales Startdatum Y-m-d
	 *	@param	string	$endDate		TODO inaktiv
	 *	@param	string	$filter			optionaler Filter // Nur Termine dieses Filters werden gelistet
	 */
	function returnInlineCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoInlineCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoInlineCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter)
	{
		include('views/echoInlineCalendar.php');
	}
	
	
	/**
	 *	For Kalender-Page-View
	 *	Kalender-Termin-Liste
	 *	@param	object	$calendarData	Die gesamten Kalender-Daten incl dateFunc und calFunc für Daten-Manipulationen
	 *	@param	int		$kalenderID		TODO: optionale ID des Kalenders // Muss noch aktiviert werden damit auch andere Kal angezeigt werden können
	 *	@param	string	$startDate		optionales Startdatum Y-m-d
	 *	@param	string	$endDate		TODO inaktiv
	 *	@param	string	$filter			optionaler Filter // Nur Termine dieses Filters werden gelistet
	 */
	function returnCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoCalender($calendarData, $calendarID, $startDatum, $endDatum, $filter)
	{
		require_once('views/echoCalendar.php');
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Ausgabe der Termin-Ansicht
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	// For Excerpt-View
	function returnDateExcerpt($calendarData, $terminID)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoDateExcerpt($calendarData, $terminID);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoDateExcerpt($calendarData, $terminID)
	{
		$d = $calendarData->dateFunc;
?>
		<p>
			Termin: <?php	echo $d->get_timeStart($terminID, "l, d.m.y")."  ";
							echo $d->get_title($terminID); ?> (<?php echo $calendarData->calendarID; ?>)
		</p>
<?php
	}
	
	
	// For FullPost-View
	function returnDate($calendarData, $terminID)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoDate($calendarData, $terminID);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoDate($calendarData, $terminID)
	{
		include('views/echoDate.php');
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Ausgabe eines Termins innerhalb eines Artikels
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	// For Excerpt-View
	function returnInlineDateExcerpt($calendarData, $terminID)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoInlineDateExcerpt($calendarData, $terminID);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoInlineDateExcerpt($calendarData, $terminID)
	{
		$d = $calendarData->dateFunc;
?>
		<p>
			Termin: <?php	echo $d->get_timeStart($terminID, "l, d.m.y")."  ";
							echo $d->get_title($terminID); ?> (<?php echo $calendarData->calendarID."/".$terminID; ?>)
		</p>
<?php
	}
	
	// For FullPost-View
	function returnInlineDate($calendarData, $terminID)
	{
		ob_start();  // deaktiviert das Echoing
		$this->echoInlineDate($calendarData, $terminID);
		$result = ob_get_contents();  // holt das nicht-geechote
		ob_end_clean();  // aktiviert Echoing wieder
		return $result;
	}
	function echoInlineDate($calendarData, $terminID)
	{
		include('views/echoInlineDate.php');
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Gibt einen RSS-Feed-Link zurück.
	 *	Verwendet wird die Scoutnet-Show.php der Anfangs- und Endzeit sowie die RSS-Smarty-Template-Datei auf dieser Domain übergeben wird
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	function get_rssKalenderLink($calendarData, $calendarID)
	{
		$calendarXMLurl = $calendarData->calendarSmartyURL."rss2.tpl";//"http://www.scoutpress.de/kalender/rss2.tpl";
		$calendarXMLStartDate = date("Y-m-d", strtotime("-2 days"));  // vor 2 Tagen
		$calendarXMLEndDate =  date("Y-m-d", strtotime("+2 month"));  // in 2 Monaten

		return "http://kalender.scoutnet.de/2.0/show.php?id=".$calendarID."&amp;template=".$calendarXMLurl."&amp;startdate=".$calendarXMLStartDate."&amp;enddate=".$calendarXMLEndDate."&amp;homepage_url=".$this->urlToCalendarPage;
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Gibt einen iCal-Feed-Link zurück.
	 *	Verwendet wird die Scoutnet-Show.php der Anfangs- und Endzeit sowie die iCal-Smarty-Template-Datei auf dieser Domain übergeben wird
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	function get_icalKalenderLink($calendarData, $calendarID)
	{
		$calendarXMLurl = $calendarData->calendarSmartyURL."ical.tpl";//"http://www.scoutpress.de/kalender/ical.tpl";
		$calendarXMLStartDate = date("Y-m-d", strtotime("-2 days"));  // vor 2 Tagen
		$calendarXMLEndDate =  date("Y-m-d", strtotime("+12 month"));  // in 12 Monaten

		return "http://kalender.scoutnet.de/2.0/show.php?id=".$calendarID."&amp;template=".$calendarXMLurl."&amp;startdate=".$calendarXMLStartDate."&amp;enddate=".$calendarXMLEndDate."&amp;homepage_url=".$this->urlToCalendarPage;
	}
	
	
	
	/********************************************************************************************************************************
	 ********************************************************************************************************************************
	 *	Bearbeiten- und Neuer-Termin-Links werden durch das up_adminLink-Plugin bereitgestellt.
	 ********************************************************************************************************************************
	 ********************************************************************************************************************************/
	function get_newLink()
	{
		if(function_exists('up_adminLink')) { up_adminLink('newdate'); } 
	}
	function get_editLink($terminID)
	{
		if(function_exists('up_adminLink')) { up_adminLink('editdate', $terminID); } 
	}
}


/**
 *	TODO: Soll den folgenden Array-Eintrag zurückgeben...
 *	Sinn: Um in der Einzelansicht eines Termins zum nächsten Termin springen zu können
 *	Diese drei Scripte funktionieren alle nicht...
 */
/*
 *	LÖSUNG für dieses Problem:
 *	Funktion(currentKey, {prev/next})
 *		for(i=currentKey, i<100000, i++)
 *			if(in_array(i))
 *				return i
 *			endif
 *		endfor
 *	endfunc
 */
/*function array_getNextKey($array, $currentKey)
{
	echo key($array);
	//array_set_pointer($array, $currentKey);
	echo key(next($array));
echo "array_______:"; print_r($array);
	$keys = array_keys($array);
	$keyIndexes = array_flip($keys);
echo "keys_______:"; print_r($keys);
echo "keys_______:"; print_r($keyIndexes);
	if (isset($keys[$keyIndexes[$currentKey]+1])) 
	{
		$return = $keys[$keyIndexes[$currentKey]+1];
	}
	else 
	{
		$return = FALSE;
	}
	return $return;
}*/
/**
 *	Soll das Array auf die Position $key setzen...
 */
/*function array_set_pointer(&$array, $key)
{
   reset($array);
   while($curKey=key($array))
   {
       if($curKey==$key)
           break;
       next($array);
   }
}
function tobias($array, $currentKey)
{
	echo key($array);
	echo "/".$currentKey;
	$keys = array_keys($array);
	$keyIndexes = array_flip($keys);
echo "keys_______:"; print_r($keys);
echo "keys_______:"; print_r($keyIndexes);
echo "aaa"; echo key($keyIndexes);
	while(key($keyIndexes)!=$currentKey)
	{
		echo key($keys)."ungleich$currentKey<br />";
	}
	
}*/

?>
