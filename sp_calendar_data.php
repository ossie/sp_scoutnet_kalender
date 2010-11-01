<?
/*
 *	Class for:     ScoutPress Scoutnet-Kalender
 *	Class-Name:    Data, CalendarFunctions, DateFunctions, CalendarOption
 *	Description:   Enthält die Klassen, für das Erstellen und Manipulieren der Kalenderdaten
 *	View-Version:  1.2
 *  Changelog:     0.91 / 2006-05-03
 *                 1.0 / 2006-11-27:
 *                 - sp_ScoutnetCalendarFunctionsHelper: Encoding deaktiviert.
 *                 - get_calendarPage() in get_calendarPageIDFromDB() verschoben und ID in Optionen gespeichert.
 *                 - caching funktioniert jetzt wirklich (sp_ScoutnetCalendarData())
 *                 - neue XML-Funktion XML-line eingebaut
 *                 1.1 / 2006-11-27: umlaute-kodierungs-problem gelöst und xml-line fertig eingerichtet
 *                 1.2 / 2007-12-00:
 *                 neuer Scoutnetaufruf, Bugfix von rocky, class snoopy zum Holen der Daten
 *                 1.3 / 2010-03-20: Ebenenup-Funktionalität hinzugefügt
 *	View-Author:   Tobias Jordans
 *
 *  Danke an:      Peter Bieling (http://www.media-palette.de) für seinen tolles xml-Parser "xml-line"
 *                 und seine Hilfe bei der Behebung der Probleme bei der Zeickenkodierung.

 *	Copyright:     Siehe at sp_scoutnetKalenderViaXML.php
 *
 *

	// http://kalender.scoutnet.de/2.0/show.php?id=5&template=dyndate/dyndate.tpl&monate_im_nachhinein=0&monate_im_voraus=20&other_template=http://org.scoutnet.de/dpsg/bergischland/kalender/old_style/old_style.tpl
	 * http://kalender.scoutnet.de/2.0/
	 * show.php
	 * ?id=5	// Kalender-ID
	 * ?eintryids=TERMINID	// einzelner Eintrag
	 * &template=dyndate/dyndate.tpl // wird zuerst aufgerufen, keine Ausgabe, verändert Auswahl
	 * &monate_im_nachhinein=0 // zurückliegende Monate, wird an Dyndate übergeben
	 * &monate_im_voraus=20
	 * &other_template=http://org.scoutnet.de/dpsg/bergischland/kalender/old_style/old_style.tpl
	 *
	//$KalenderURLtemplateZwei = "http://www.gandalf.wtal.de/kalender_template/gandalf_info.tpl";
*/


// nötig damit ABSPATH etc funktioniert. Wenn class z.B. aus buttonsnap-popup.php aufgerufen wird fehlt diese nämlich =(
if(!function_exists('bloginfo'))
	require_once('../../../wp-config.php');
// z.B. die FlattenArray-Funktion
require_once(ABSPATH.'/wp-content/plugins/up_includes/flattenarray.php');

/**
 *	Funktionen die Kalender-Informationen aus dem Kalender-Array holen
 *	helper: http://de3.php.net/manual/de/function.strtotime.php
 *	helper: http://de3.php.net/manual/de/ref.array.php
 */
class sp_ScoutnetCalendarFunctionsCal
{
	var $calendarID;
	var $calendarData;
	var $calendarArray;
	var $helper;

	// Konstruktor
	function sp_ScoutnetCalendarFunctionsCal($calendarID, $calendarData)
	{
		$this->calendarID = $calendarID;
		$this->calendarData = $calendarData;
		$this->calendarArray = $calendarData['calendar']['head'];

		$this->helper = new sp_ScoutnetCalendarFunctionsHelper;
	}

	/**
	 *	TODO
	 */
	function get_dates()
	{
		return $this->calendarData['calendar']['dates'];
	}
	function get_id()
	{
		return $this->calendarID;
	}
	function get_dateStart()
	{
		return date("d.m.Y", strtotime($this->calendarArray['datestart']));
	}
	function get_dateEnd()
	{
		return date("d.m.Y", strtotime($this->calendarArray['dateend']));
	}
	function get_dateUpdate()
	{
		return date("d.m.Y h:m:s", strtotime($this->calendarArray['update']));
	}
	function get_title()
	{
		return $this->helper->decodeCharacter($this->calendarArray['title']);
	}
	function get_association()
	{
		return $this->helper->decodeCharacter($this->calendarArray['association']);
	}
	function get_level()
	{
		return $this->helper->decodeCharacter($this->calendarArray['level']);
	}
	function get_name()
	{
		// Der Scoutnet-Kalender übergibt Stämme in der Form NAME ORT. Daraus folgt das einige Stämme "Langerwehe Langerwehe" heissen.
		// Diese Funktionen entfernen diese Dopplung.
		return implode(" ", array_unique(split(" ", $this->calendarArray['name'])));
	}
	function get_ident()
	{
		return $this->get_association()." ".$this->get_level()." ".$this->get_name();
	}

	/**
	 *	Gibt alle Filter-Wörter, ihr Startdatum + ihre Überschrift aus
	 */
	function getAll_FilterWordsAsOption($before="", $divide="", $after="")
	{
		$dateFunc = new sp_ScoutnetCalendarFunctionsDate($this->calendarID, $this->calendarData);
		$allCategories = array();
		$allGroups = array();
		$allLocations = array();
		// Prepare the filterWords
		foreach($this->get_dates() as $dateID => $date)
		{
			$allGroups[]     = $dateFunc->get_groupArray($dateID);
			$allCategories[] = $dateFunc->get_categoryArray($dateID);
			$allLocations[]  = $dateFunc->get_locationArray($dateID);
			//echo '$allGroups: ';print_r($allGroups);echo '$allCategories: ';print_r($allCategories);echo '$allLocations: ';print_r($allLocations);
		}
		$allGroups = array_unique(flattenArray($allGroups));
		$allCategories = array_unique(flattenArray($allCategories)); // TODO: Leider funktioniert hier kein sort() oder so drauf da dadurch das Array zerstört wird...
		$allLocations = array_unique(flattenArray($allLocations)); // TODO: Leider funktioniert hier kein sort() oder so drauf da dadurch das Array zerstört wird...

		// Create the OptionGroups
		$string = $before;
		// for the Groups
		$string .= "<optgroup label=\"Gruppen\">\n";
		foreach($allGroups as $group)
		{
			if(strlen($group) > 3)
			{	// nur Werte ausgeben die mehr als 3 Zeichen haben...
				$string .= "	<option value=\"$group\">$group</option>\n";
			}
		}
		$string .= "</optgroup>\n";
		// and Categories
		$string .= "<optgroup label=\"Kategorien\">\n";
		foreach($allCategories as $category)
		{
			if(strlen($category) > 3)
			{	// nur Werte ausgeben die mehr als 3 Zeichen haben... alle anderen sind als Filter ungeeignet (Filterwörter werden ja automatisch generiert...)
				$string .= "	<option value=\"$category\">$category</option>\n";
			}
		}
		$string .= "</optgroup>\n";
		// and Locations
		$string .= "<optgroup label=\"Orte/Treffpunkte/PLZ\">\n";
		foreach($allLocations as $location)
		{
			if(strlen($location) > 3)
			{	// nur Werte ausgeben die mehr als 3 Zeichen haben... alle anderen sind als Filter ungeeignet (Filterwörter werden ja automatisch generiert...)
				$string .= "	<option value=\"$location\">$location</option>\n";
			}
		}
		$string .= "</optgroup>\n";
		return $string.$after;
	}

	/**
	 *	Gibt alle Termin-IDs, ihr Startdatum + ihre Überschrift aus
	 */
	function getAll_DatesAsOption($before="", $divide="", $after="")
	{
		$dateFunc = new sp_ScoutnetCalendarFunctionsDate($this->calendarID, $this->calendarData);
		$startDate = date('Y-m-d', strtotime("-2 days")); // Daten ab vorgestern
		// Create the OptionGroups
		$string = $before;
		foreach($this->get_dates() as $dateID => $date)
		{
			if($dateFunc->get_timeStart($dateID, "Y-m-d") >= $startDate)
			{
				$string .= "\n<option value=\"$dateID\">";
				$string .= $dateFunc->get_timeStart($dateID, "Y-m-d")."&nbsp;&nbsp;";
				if(strlen($dateFunc->get_title($dateID))>17)
				{	// schneidet den String zu
					$string .= substr($dateFunc->get_title($dateID), 0, 20)."...&nbsp;";
				} else
				{	// erweitert den String so dass die DateID eine Spalte ergibt.
					//$string .= str_pad($dateFunc->get_title($dateID), 20, "&nbsp;", STR_PAD_RIGHT)."&nbsp;&nbsp;";
					$string .=$dateFunc->get_title($dateID)."&nbsp;&nbsp;";
				}
				$string .= "[".$dateID."]";
				$string .= "</option>";
			}
		}
		return $string.$after;
	}
}


/**
 *	Funktionen die Datums-Informationen aus dem Kalender-Array holen
 */
class sp_ScoutnetCalendarFunctionsDate
{
	var $calendarID;
	var $calendarData;
	var $dateArray;
	//var $dateID;

	function mysql2date( $dateformatstring, $mysqlstring, $translate = true ) {
		global $wp_locale;
		$m = $mysqlstring;
		if (substr($m,strlen($m)-1,1)=='Z')
		{
			$m=substr($m,0,-1);
		}
		$m=str_replace('T',' ',$m);
		if ( empty( $m ) )
			return false;

		if( 'G' == $dateformatstring ) {
			return strtotime( $m . ' +0000' );
		}
	
		$i = strtotime( $m );
	
		if( 'U' == $dateformatstring )
			return $i;

		if ( $translate)
		{
		    return date_i18n( $dateformatstring, $i );
		}
		else
		{
		    return date( $dateformatstring, $i );
		}
	}

	// Konstruktor
	function sp_ScoutnetCalendarFunctionsDate($calendarID, $calendarData)
	{
		$this->calendarID = $calendarID;
		$this->calendarData = $calendarData;
		//$this->dateID = $dateID;
		//$this->dateArray = usort($calendarData[$calendarID]['DATES'], "compareDates");
		$this->dateArray = $calendarData['calendar']['dates'];
	}

	/**
	 *	TODO
	 */
	function get_timeStart($dateID, $format="d.m.Y h:m")
	{
		return $this->mysql2date($format, $this->dateArray[$dateID]['timestart']);
	}
	function get_timeEnd($dateID, $format="d.m.Y h:m")
	{
		if($time = $this->dateArray[$dateID]['timeend'])
				return $this->mysql2date($format, $time);
		else	return FALSE;
	}
	function echo_timeStartWithABBR($dateID, $format="d.m.Y h:m")
	{
		echo '<abbr class="dtstart" title="'.$this->get_timeStart($dateID, "Ymd\THis").'+0100">';
		echo $this->get_timeStart($dateID, $format);
		echo '</abbr>';
	}
	function echo_timeEndWithABBR($dateID, $format="d.m.Y h:m")
	{
		echo '<abbr class="dtend" title="'.$this->get_timeEnd($dateID, "Ymd\THis").'+0100">';
		echo $this->get_timeEnd($dateID, $format);
		echo '</abbr>';
	}
	function get_timeCreate($dateID, $format="d.m.Y h:m")
	{
		if($time = $this->dateArray[$dateID]['timecreated'])
				return $this->mysql2date($format, $time);
		else	return FALSE;
	}
	function get_timeUpdate($dateID, $format="d.m.Y h:m")
	{
		if($time = $this->dateArray[$dateID]['timeupdated'])
				return $this->mysql2date($format, $time);
		else	return FALSE;
	}
	function echo_updateInfoIfUpdated($dateID, $format="", $before="Aktualisiert am ", $middle=" von ", $after=".")
	{
		if($this->get_timeCreate($dateID, "U") < $this->get_timeUpdate($dateID, "U"))
		{
			echo $before.$this->get_timeUpdate($dateID, $format).$middle.$this->get_updater($dateID).$after;
		}
	}
	function get_title($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['title']);
	}
	function get_place($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['place']);
	}
	function get_zip($dateID)
	{
		return $this->dateArray[$dateID]['zipcode'];
	}
	function get_locationIfLocation($dateID, $before=" (", $after=") ")
	{
		if($this->get_zip($dateID) || $this->get_place($dateID))
		{
			$location  = $before;
			$location .= ($this->get_zip($dateID)?$this->get_zip($dateID)." ":'');
			$location .= $this->get_place($dateID);
			$location .= $after;
			return $location;
		}
	}
	function echo_maplinkIfLocation($dateID, $before=" (", $after=") ", $maptype="google", $linkText="Luftbild")
	{
		if($this->get_locationIfLocation($dateID))
		{
			$maptype = (($maptype=='goyellow') ? 'http://goyellow.de/map/' : $maptype);
			$maptype = (($maptype=='google')   ? 'http://maps.google.de/maps?f=q&hl=de&om=1&q=' : $maptype);
			echo $before."<a href=\"".$maptype.$this->get_locationIfLocation($dateID, '', '')."\" title=\"Luftbild anzeigen...\">$linkText</a>".$after;
		}
	}
	function get_categories($dateID, $before="", $after="")
	{
		if($categories = $this->dateArray[$dateID]['categories'])
		{
			return $before.$this->decodeCharacter($categories).$after;
		}
	}
	function echo_linkedcategories($dateID, $before="", $after="", $beforeLinText="", $afterLinText="", $class="grouplink")
	{
		$cat_array = split(", ", $this->get_categories($dateID));
		foreach($cat_array as $cat)
		{
			echo $before."<a title=\"Filter: Nur Einträge der Kategorie $cat anzeigen...\" class=\"$class\" href=\"?dateID=$dateID&filter=$cat\">".$beforeLinText.$cat.$afterLinText."</a>\n".$after;
		}
	}
	function get_groups($dateID, $before="", $after="")
	{
		if($groups = $this->dateArray[$dateID]['groups'])
		{
			return $before.$this->decodeCharacter($groups).$after;
		}
		//return $this->dateArray[$dateID]['GROUPS'];
	}
	function echo_linkedgroups($dateID, $before="", $after="", $class="grouplink")
	{
		$groups_array = split(", ", $this->get_groups($dateID));
		foreach($groups_array as $group)
		{
			echo "<a title=\"Filter: Nur Einträge der Gruppe $group anzeigen...\" class=\"$class\" href=\"?dateID=$dateID&filter=$group\">".$before.$group.$after."</a>\n";
		}
	}
	function echo_groupImages($dateID, $before='', $after='')
	{
		$this->echo_groupImagesWithOptionalGroupname($dateID, $before, $after);
	}
	function echo_groupImagesWithOptionalGroupname($dateID, $before="", $after="", $showGroupName=FALSE)
	{
		if($groups = $this->get_groups($dateID, '', ''))
		{
			$groups_array = split(", ", $groups);
			echo $before;
			foreach($groups_array as $group)
			{
				// blöder Workaround da durch Encodingprobleme das ö in Wöflinge nicht richtig ausgegeben wird...
				$pos = strpos($group, "l");
				if($pos==2||$group=='Wölflinge'||$group=='Woelflinge')
					echo '<img width="12" height="12" src="http://kalender.scoutnet.de/2.0/images/1.gif" alt="Wölflinge" />'.($showGroupName ? " $group, " : '');
				elseif($group=='Jungpfadfinder')
					echo '<img width="12" height="12" src="http://kalender.scoutnet.de/2.0/images/2.gif" alt="Jungpfadfinder" />'.($showGroupName ? " $group, " : '');
				elseif($group=='Pfadfinder')
					echo '<img width="12" height="12" src="http://kalender.scoutnet.de/2.0/images/3.gif" alt="Pfadfinder" />'.($showGroupName ?" $group, " : '');
				elseif($group=='Rover')
					echo '<img width="12" height="12" src="http://kalender.scoutnet.de/2.0/images/4.gif" alt="Rover" />'.($showGroupName ? " $group, " : '');
				elseif($group=='Leiter')
					echo '<img width="12" height="12" src="http://kalender.scoutnet.de/2.0/images/5.gif" alt="Leiter" />'.($showGroupName ? " $group" : '');
				elseif($group=='')
					echo '';
				else
					echo "<abbr title='Unbekannte Gruppe. Bitte wende dich an das ScoutPress-Team!'>?</abbr>";
			}
			echo $after;
		}
	}
	function get_desc($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['description']);
	}
	function get_organizer($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['organizer']);
	}
	function get_targetgroup($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['targetgroup']);
	}
	function get_linktext($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['linktext']);
	}
	function get_linkurl($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['linkurl']);
	}
	function get_link($dateID)
	{
		$linktext = $this->get_linktext($dateID);
		$linkurl = $this->get_linkurl($dateID);
		$link = '<a href="'.$linkurl.'">'.$linktext.'</a>';
		return $link;
	}
	function get_dateExcerpt($dateID)
	{
		$text  = $this->get_locationIfLocation($dateID, '', '').' // ';
		$text .= $this->get_desc($dateID);
		$text = strip_tags($text);
		return substr($text, 0, 40)."...";
	}
	function get_author($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['createdby']);
	}
	function get_updater($dateID)
	{
		return $this->decodeCharacter($this->dateArray[$dateID]['updatedby']);
	}
	function echo_updaterOrAuthor($dateID)
	{
		if($this->get_updater($dateID))
		{
			echo $this->get_updater($dateID);
		} else {
			echo $this->get_author($dateID);
		}
	}

	/**
	 *	Buchstaben dekodieren
	 */
	function decodeCharacter($string)
	{
		$helper = new sp_ScoutnetCalendarFunctionsHelper;
		return $helper->decodeCharacter($string);
	}
	function encodeCharacter($string)
	{
		$helper = new sp_ScoutnetCalendarFunctionsHelper;
		return $helper->encodeCharacter($string);
	}
	function encodeCharacterForLink($string)
	{
		$helper = new sp_ScoutnetCalendarFunctionsHelper;
		return $helper->encodeCharacterForLink($string);
	}

	/**
	 *	Überprüft of der Termin zu einem Filter-Kriterium gehört
	 *	Gefiltert wird nach Grupp, Kategorie und Ort
	 */
	function in_filter($dateID, $filterString)
	{
		$filterQuelleCat = $this->get_categoryArray($dateID);
		$filterQuelleGroup = $this->get_groupArray($dateID);
		$filterQuelleLocation = $this->get_locationArray($dateID);
		$filterQuellen = array_unique(array_merge($filterQuelleCat, $filterQuelleGroup, $filterQuelleLocation));

		return (in_array($filterString, $filterQuellen) || empty($filterString));
	}

	/**
	 * strtolower bei UTF8-encodiertem Inhalt führt zu ungültigem Inhalt
	 * Das ganze unten deshalb nochmal für die Filter neu implementiert
	 * 20100118 OK
	 */
	function get_categoryArray($dateID)
	{
		return split(", ", mb_strtolower($this->encodeCharacterForLink($this->get_categories($dateID, '', ''))));
	}
	function get_groupArray($dateID)
	{
		return split(", ", mb_strtolower($this->encodeCharacterForLink($this->get_groups($dateID, '', ''))));
	}
	function get_locationArray($dateID)
	{
		return split(" ", mb_strtolower($this->encodeCharacterForLink(strtr($this->get_locationIfLocation($dateID, '', ''), ",;:-_", "     "))));
	}
	
}

/**
 *	Hilfsfunktionen für Kalender und Termin
 *	Update 1.0: De/Encoding-Funktionen deaktiviert da die XML-Datei jetzt Umlaute enthält.
 */
class sp_ScoutnetCalendarFunctionsHelper
{
	/**
	 *	Buchstaben dekodieren
	 */
	function decodeCharacter($string)
	{
		/*$string = str_replace("&lt;", "<", $string);
		$string = str_replace("&gt;", ">", $string);
		$string = str_replace("&quot;", "\"", $string);
		$string = str_replace("yae", "ä", $string);
		$string = str_replace("yoe", "ö", $string);
		$string = str_replace("yue", "ü", $string);
		$string = str_replace("ysz", "ß", $string);
		$string = str_replace("yaye", "Ä", $string);
		$string = str_replace("yoye", "Ö", $string);
		$string = str_replace("yuye", "Ü", $string);
		$string = str_replace("ysyz", "SS", $string);*/
		return utf8_decode($string);
	}
	function encodeCharacterForLink($string)
	{
		/*$string = str_replace("yae", "ae", $string);
		$string = str_replace("yoe", "oe", $string);
		$string = str_replace("yue", "ue", $string);
		$string = str_replace("ysz", "ss", $string);
		$string = str_replace("yaye", "ae", $string);
		$string = str_replace("yoye", "oe", $string);
		$string = str_replace("yuye", "ue", $string);
		$string = str_replace("ysyz", "ss", $string);
		$string = str_replace("ä", "ae", $string);
		$string = str_replace("ö", "oe", $string);
		$string = str_replace("ü", "ue", $string);
		$string = str_replace("ß", "ss", $string);
		$string = str_replace("ä", "ae", $string);
		$string = str_replace("ö", "oe", $string);
		$string = str_replace("ü", "ue", $string);
		$string = str_replace("ß", "ss", $string);*/
		return $string;
	}
	function encodeCharacter($string)
	{
		/*$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);
		$string = str_replace("", "&quot;", $string);
		$string = str_replace("ä", "yae", $string);
		$string = str_replace("ö", "yoe", $string);
		$string = str_replace("ü", "yue", $string);
		$string = str_replace("ß", "ysz", $string);
		$string = str_replace("ä", "yaye", $string);
		$string = str_replace("ö", "yoye", $string);
		$string = str_replace("ü", "yuye", $string);
		$string = str_replace("ß", "ysyz", $string);*/
		return $string;
	}
}


/**
 *	Datenbank-Optionen holen oder schreiben
 *	@var	ARRAY	$options	Das Array aus dem Options-Table
 */
class sp_ScoutnetCalendarOption
{
	var $options;

	function sp_ScoutnetCalendarOption()
	{
		$this->options = get_option('sp_ScoutnetCalendar_options');
		// TODO aus irgend einem Grund ist die Page-ID an dieser Stelle nicht gefüllt.
		// Workaround: erneut füllen:
		if(empty($this->options['calendar_page_id']))
		{
			$this->options['calendar_page_id'] = $this->get_calendarPageIDFromDB();
			update_option('sp_ScoutnetCalendar_options', $this->options);
		}
	}

	/**
	 *	holt Kalender-ID aus den Optionen
	 */
	function get_calendarPageID()
	{
		return $this->options['calendar_page_id'];
	}
	/**
	 *	holt Kalender-ID aus DB
	 *	Sucht nach  post_title kalender, termine, termin, calendar
	 *	und         post_name kalender (nicename)
	 *	Wird von sp_ScoutnetCalendar_setupOptions() verwendet
	 */
	function get_calendarPageIDFromDB()
	{
		global $wpdb;
		$query = "SELECT ID FROM $wpdb->posts
				 WHERE (post_name = 'kalender'
				 	OR post_name = 'calendar'
				 	OR post_name = 'termine'
				 	OR post_name = 'termin'
				 	OR post_title = 'Kalender')
				 	AND post_status = 'static'";
		$result = $wpdb->get_results( $query );
		return $result[0]->ID;
	}
	/**
	 *	return Option "id"
	 */
	function get_calendarID()
	{
		return $this->options['id'];
	}
	/**
	 *	return Option "cache_duration"
	 */
	function get_cacheminutes()
	{
		return $this->options['cache_duration'];
	}
	/**
	 *	return Option "time_of_last_cache"
	 */
	function get_cachetime($calendarID)
	{
		$current_cache = get_option('sp_ScoutnetCalendar_cache_for_id_'.$calendarID);
		return $current_cache['time_of_last_cache'];
	}
	/**
	 *	return Option "ebenenup of last cache"
	 */
	function get_cacheebene($calendarID)
	{
		$current_cache = get_option('sp_ScoutnetCalendar_cache_for_id_'.$calendarID);
		return $current_cache['ebenenup'];
	}
	/**
	 *	return cached Calendar-Object
	 */
	function get_cacheObject($calendarID)
	{
		$current_cache = get_option('sp_ScoutnetCalendar_cache_for_id_'.$calendarID);
		return $current_cache;
	}
	/**
	 *	update and return "id"-Value
	 *	@param	int	$value	neuer Kalender-ID-Wert
	 */
	function update_and_return_calendarID($value)
	{
		$this->options['id'] = $value;
		update_option('sp_ScoutnetCalendar_options', $this->options);
		return $value;
	}
	/**
	 *	update and return "cache_duration"-Value
	 *	@param	int	$value	neuer Minuten-Wert für die Dauer des Cachens
	 */
	function update_and_return_cacheminutes($value)
	{
		$this->options['cache_duration'] = $value;
		update_option('sp_ScoutnetCalendar_options', $this->options);
		return $value;
	}
}

/**
 *	Holt die Kalenderdaten von Scoutnet oder aus dem Cache und gibt sie zurück.
 *	Ggf. werden option-table-Werte für den cache gesetzt.
 */
class sp_ScoutnetCalendarData
{
	/**
	 *	@var	OBJECT	$calendarData	Enthält alle Informationen aus dem XML
	 *	@var	STRING	$calendarURL	Die URL zur XML-Datei die den Kalender läd.
	 *	@var	STRING	$calendarID 	Die gewünschte ID oder die aus der DB
	 *	@var	OBJECT	$calFunc    	Objekt der CalendarFunction-Klasse. Enthält Funktionen zur Datenausgabe
	 *	@var	OBJECT	$dateFunc    	Objekt der DateFunction-Klasse. Enthält Funktionen zur Datenausgabe
	 *	@var	STRING	$cache_status   Ob der Kalender aus dem Cache oder live erscheint...
	 */
	var $calendarData;
	var $calendarURL;
	var $calendarSmartyURL;
	var $calendarID;
	var $calFunc;
	var $dateFunc;
	var $cache_status;
	var $ebenenup;

	/**
	 *	Konstruktor holt die Kalender-Daten aus dem Cache oder von Scoutnet
	 *	Wichtig ist, dass die Funktion &addids nicht an Scoutnet übergeben wird sondern in gesonderte Aufrufe dieser Klasse gesplittet wird.
	 *	Andernfalls würden mehrere Kalender-Informationen unter dem Namen der primären Kalender-ID gemischt...
	 */
	function sp_ScoutnetCalendarData($calendarID, $cache_yes_or_no, $ebenenup)
	{
		$options = new sp_ScoutnetCalendarOption();

		$this->ebenenup=$ebenenup;
		//if(empty($calendarID)) $this->calendarID = $options->get_calendarID();
		$this->calendarID  = $calendarID;
		$calendarCacheminutes = $options->get_cacheminutes();
		$calendarCachetime = $options->get_cachetime($this->calendarID);
		$calendarCacheEbene = $options->get_cacheebene($this->calendarID);
		$site_url = get_settings('siteurl');

		// URL für die Smarty-Templates setzen
		if(strstr($site_url, '127.0.0.1') == '' && strstr($site_url, 'localhost') == '' && strstr($site_url, 'local') == '' )
		{	// URL enthält kein localhost
			$this->calendarSmartyURL = $site_url."/wp-content/plugins/sp_scoutnetKalender/smarty-templates/";
			//$this->calendarSmartyURL = "http://www.scoutpress.de/kalender/";
		} else
		{	// URL enthält 'localhost'
			$this->calendarSmartyURL = "http://www.scoutpress.de/kalender/";
		}

		// Caching
		if ((((time() - $calendarCachetime <= $calendarCacheminutes*60) && ($this->ebenenup==$calendarCacheEbene)) && $cache_yes_or_no!=='no') || $cache_yes_or_no=='yes')
		{	// Cache
			$this->calendarData = $options->get_cacheObject($this->calendarID);
			$this->cache_status = "cached from the DB";
			//$this->calendarData = $this->getDataFromScoutnetAsArray(); // DEBUG: immer neu laden
			//$this->cache_status = "fresh from scoutnet"; // DEBUG: immer neu laden
		} else
		{	// Online
      		$this->calendarData = $this->getDataFromScoutnetAsArray();
			$this->cache_status = "fresh from scoutnet";
			//$this->calendarData = $options->get_cacheObject($this->calendarID); // DEBUG: immer cashe verwenden
			//$this->cache_status = "cached from the DB"; // DEBUG: immer cashe verwenden
		}

		// Debugging // some function cannot print_r into the page
		//echo"<h1>CalendarData:</h1><pre style='height: 300px; overflow: scroll;'>";print_r($this->calendarData); echo"</pre>";

		// Erstellt die Objekt-Referenzen für die Termin- und Kalender-Funktionen
		$this->calFunc = new sp_ScoutnetCalendarFunctionsCal($this->calendarID, $this->calendarData);
		$this->dateFunc = new sp_ScoutnetCalendarFunctionsDate($this->calendarID, $this->calendarData);

		// Kalender-Inhalt zurückgeben
		return $this->calendarData;
	}

	/**
	 *	Holt die Kalender-Daten aus dem XML von Scoutnet,
	 *	wandelt sie in ein Array um und
	 *	speichert das Array als Cache
	 */
	function getDataFromScoutnetAsArray()
	{
		$adminError = new sp_ScoutnetCalenderErrors;

		// ACHTUNG: Das TPL/Smarty-Template muss zusätzlich auf www.scoutpress.de/kalender/ hochgeladen werden!
		// Leider hatte das Original Scoutpress Smarty-Template Bugs, korrigierte Version liegt auf dem ruhrsau-Server
		//$calendarXMLurl = "http://www.scoutpress.de/kalender/scoutpress_xml.tpl";
		$calendarXMLurl = $this->calendarSmartyURL."scoutpress_xml.tpl";
		$calendarXMLurl = "http://www.ruhrsau.de/kalender.xml.tpl";

		$calendarXMLStartDate = date("Y-m-d", strtotime("-36 month"));  // vor 36 Monaten   // help: http://de2.php.net/manual/de/function.strtotime.php
		$calendarXMLEndDate =  date("Y-m-d", strtotime("+36 month"));  // in 36 Monaten
		$this->calendarURL = "http://kalender.scoutnet.de/2.0/show.php?id=".$this->calendarID."&charset=utf8&template=".$calendarXMLurl."&startdate=".$calendarXMLStartDate."&enddate=".$calendarXMLEndDate."&ebenenup=".$this->ebenenup;

		// xml-line wandelt das XML für uns um
		// Vielen Dank!
		require_once(ABSPATH."wp-content/plugins/up_includes/xml-line.php");

		// Problem: wenn save_mode = on oder fopen verboten ist für den Server fuktioniert die direkte URL-Übergabe an xml-line nicht.
		// Lösung: testen ob fopen funktioniert, andernfalls die Daten mittels eines Hilfsscripts einlesen
		// eventuell auch hilfreich: curl-Datenlesen http://de3.php.net/manual/en/function.fopen.php#70288

		/* // deaktiviet zu Gunsten von snoopy
		$buffer = @file_get_contents($this->calendarURL);
		if(!$buffer) {
			$error = "file_get_contents funktioniert nicht. versuche HTTPRequest\n";
			// class ließt die Datei per http-request aus ohne fopen zu verwenden
			require_once(ABSPATH."wp-content/plugins/up_includes/httprequest.php");
			$buffer = new HTTPRequest($this->calendarURL);
			$buffer = $buffer->DownloadToString();
		} else {
			$error = "file_get_contents funktionierte";
		}*/

		// Hilfe zu ini_get und ini_set zur Überprüfung und zum Setzen von Konfigurationswerten
		// http://de2.php.net/manual/de/function.ini-set.php
		// http://de2.php.net/manual/de/function.ini-get.php
		ini_set('allow_url_fopen', 'true'); // kann eigentlich nicht schaden...

		// snoopy holt die Daten (immer für WordPress)
		require_once(ABSPATH.'wp-includes/class-snoopy.php');
		// aus rss-functions.php
		$client = new Snoopy();
		$client->agent = MAGPIE_USER_AGENT;
		$client->read_timeout = MAGPIE_FETCH_TIME_OUT;
		$client->use_gzip = MAGPIE_USE_GZIP;
		@$client->fetch($this->calendarURL);

		$buffer = $client->results;

		// debug-error-Meldung
		if(!$buffer || empty($buffer)) {
			$error = "Snoopy funktioniert nicht. übergebe this->calendarURL direkt an xml-line\n";
			$buffer = $this->calendarURL;
		} else {
			$error = "Snoopy funktionierte";
		}
		// Debugging
		//echo"<h1>Debugging in getData:</h1><h2>Meldung: \"$error\"</h2><h3>ini_get ".ini_get('allow_url_fopen')." / ini_set ".ini_set('allow_url_fopen', 'true')."</h3><pre style='height: 300px; overflow: scroll;'>"; print_r($buffer); echo"</pre>";

		$mylines = new xml_line($buffer);

		//$mylines = new xml_line($this->calendarURL);
		$mylines->get_record(0,'head'); // alle Inhalte des <head>-Tags
		$mylines->get_record(0,'date'); // alle Inhalte der <date>-Tags
		$mylines->xml_stream('utf8', 'lat1'); // Umwandlung der Kodierung

		$xml_head  = $mylines->table_result[0];
		$xml_dates = $mylines->table_result[1];

		// TODO: Funktioniert diese empty()-Abfrage überhaupt?
		if (empty($xml_head))
		{
			$adminError->NoDataError();
		}
		else
		{
			// Jetzt erstellen wir mit Hilfe des XML-Line-Array
			// unser eigenes Array nach unseren Wünschen:
			$calendar_as_array = array();
			foreach ($xml_head as $head)
			{
				$calendar_as_array['head']['id'] = $head['id'];
				$calendar_as_array['head']['title'] = $head['title'];
				$calendar_as_array['head']['association'] = $head['association'];
				$calendar_as_array['head']['name'] = $head['name'];
				$calendar_as_array['head']['update'] = $head['update'];
				$calendar_as_array['head']['datestart'] = $head['datestart'];
				$calendar_as_array['head']['dateend'] = $head['dateend'];
			}
			foreach ($xml_dates as $dates)
			{
				$calendar_as_array['dates'][$dates['_@id']]['title'] = $dates['title'];
				$calendar_as_array['dates'][$dates['_@id']]['timestart'] = $dates['timestart'];
				$calendar_as_array['dates'][$dates['_@id']]['timeend'] = $dates['timeend'];
				$calendar_as_array['dates'][$dates['_@id']]['zipcode'] = $dates['zipcode'];
				$calendar_as_array['dates'][$dates['_@id']]['place'] = $dates['place']; // thx@rocky
				$calendar_as_array['dates'][$dates['_@id']]['createdby'] = $dates['createdby'];
				$calendar_as_array['dates'][$dates['_@id']]['updatedby'] = $dates['updatedby'];
				$calendar_as_array['dates'][$dates['_@id']]['timecreated'] = $dates['timecreated'];
				$calendar_as_array['dates'][$dates['_@id']]['timeupdated'] = $dates['timeupdated'];
				$calendar_as_array['dates'][$dates['_@id']]['categories'] = $dates['categories'];
				$calendar_as_array['dates'][$dates['_@id']]['groups'] = $dates['groups'];
				$calendar_as_array['dates'][$dates['_@id']]['description'] = $dates['description'];
				// neu in show_scoutpress.php
				$calendar_as_array['dates'][$dates['_@id']]['organizer'] = $dates['organizer'];
				$calendar_as_array['dates'][$dates['_@id']]['targetgroup'] = $dates['targetgroup'];
				$calendar_as_array['dates'][$dates['_@id']]['linkurl'] = $dates['linkurl'];
				$calendar_as_array['dates'][$dates['_@id']]['linktext'] = $dates['linktext'];
				$this->calendarData = $calendar_as_array;
			}

			// DEBUG
			//echo "<h2>calendar_as_array</h2>";echo "<pre>";print_r($calendar_as_array);echo "</pre>";

			// Save cache
			$current_cache['time_of_last_cache'] = time();
			$current_cache['calendar'] = $this->calendarData;
			$current_cache['ebenenup'] = $this->ebenenup;
			update_option('sp_ScoutnetCalendar_cache_for_id_'.$this->calendarID, $current_cache);

			// DEBUG
			//echo "<h2>current_cache</h2>";echo "<pre>";print_r($current_cache);echo "</pre>";

			return $current_cache;
		}
	}
}

?>