<?php
/*
 *	ViewClass 
 	for Plugin:    ScoutPress Scoutnet-Kalender
 *	View-Name:     Standard
 *	Description:   Enthält die Ausgabe-Informationen für den Kalender  
 *	View-Version:  0.1
 *	View-Updated:  2006-11-27
 *	Changelog:     0.031 / 2006-11-03
 *                 0.1 / 2006-11-27: Kleinere Änderungen während der Testphase / Debug-Bereich verbessert
 *	View-Author:   Tobias Jordans

 *	Copyright:     Siehe at sp_scoutnetKalender.php
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

/**
 *	Funktionen, die die Admin-Menüpunkte ausgeben
 *	@var	ARRAY	$options	Das Array aus dem Options-Table
 *	@var	OBJECT	$option_functions	Ein sp_ScoutnetCalendarOption-Objekt
 */
class sp_ScoutnetCalendarAdminView
{
	var $options;
	var $option_functions;
	
	/**
	 *	Konstruktor füllt die Klassenvariablen
	 */
	function sp_ScoutnetCalendarAdminView()
	{
		$this->option_functions = new sp_ScoutnetCalendarOption;
		$this->get_options();
	}
	
	/**
	 *	Aktualisiert die $options-Variable
	 */
	function get_options()
	{
		$this->options = get_option('sp_ScoutnetCalendar_options');
	}
	
	/**
	 *	Ausgabe der Adminseite "Übersicht"
	 */
	function MenuUebersicht() { 
		if (isset($_POST['Submit'])) 
		{	// schreibt die Kalender-ID wenn sie auf der Hilfe-Seite geändert wurde.
			$this->option_functions->update_and_return_calendarID(intval($_POST['scoutnetkalender_id']));
			$this->get_options();
		}
	
		if($this->options['id']==0) 
		{	// Erster Aufruf // Erklärung...
			$this->InhaltUebersichtErklaerung();
		} else 
		{	// Übersichts-Seite // Kalender-ID bereits vorhanden...
			$this->InhaltUebersichtIframe();
		}
	}
	
	
	/**
	 *	"Erster Aufruf"-Inhalt für die Adminseite "Übersicht"
	 */
	function InhaltUebersichtErklaerung()
	{
		if (isset($_POST['Submit'])) : 
?>
	<div class="updated">
		<p><strong>Bitte überprüfe deine Kalender-Nummer. Sie darf nicht »0« sein!</strong></p>
	</div>
<?php 	endif; ?>
	<div class="wrap">
		<h2>Gut Pfad beim Scoutnet-Kalender-Plugin für ScoutPress!</h2>
		<p>
			Du ruft die Kalender-Seite zum ersten Mal auf.<br />
			Damit der Kalender funktioniert, musst du bitte folgende Schritte durchführen.
		</p>
		<h3>Übersicht</h3>
		<p>
			<ol>
				<li>Informiere dich, <a href="#ueber">wie der Scoutnet-Kalender in ScoutPress funktioniert</a>.</li>
				<li><a href="#anmelden">Melde dich in der ScoutNet-Community an</a></li>
				<li><a href="#kalender">Kalender-Rechte beantragen und Kalender-Nummer eintragen</a></li>
				<li>Fertig!</li>
			</ol>
		</p>
	</div>
	
<?php	
		$hilfe = new sp_ScoutnetCalendarHelp;
		$hilfe->ueber('zu 1: ');
		$hilfe->anmelden('zu 2: ');
?>
	<div class="wrap">
<?php 
		$hilfe->recht_nummer('zu 3: ','neu');
?>		
 		<form name="scounetkalender" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<p>
				Kalender-Nummer: <input name="scoutnetkalender_id" id="scoutnetkalender_id" type="text" value="<?php echo $this->option_functions->get_calendarID() ?>" size="3" />
				<input class="button" type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
			</p>
 		</form>
	</div>
<?php
	}
	
	
	/**
	 *	Iframe mit Scoutnet-Kalender-Inhalt für die Adminseite "Übersicht"
	 */
	function InhaltUebersichtIframe() 
	{
		$calendarID = $this->options['id'];
		$calendarCachetime = $this->option_functions->get_cachetime($calendarID);
		$calendarCacheminutes = $this->option_functions->get_cacheminutes();
		$time_left_until_fresh_calendar = $calendarCacheminutes*60-(time()-$calendarCachetime);
		
		/**
		 *	URL für das Ifrage
		 *	EDIT:	https://www.scoutnet.de/community/kalender/events.php?task=modify&SSID=424&Events_ID=120601
		 *	DELETE: https://www.scoutnet.de/community/kalender/events.php?task=delete&SSID=424&Events_ID=120601
		 *	VORLAGE:https://www.scoutnet.de/community/kalender/events.php?task=create&SSID=424&Events_ID=120601
		 */
		$taskViaURL = $_REQUEST['task'];
		$dateViaURL = $_REQUEST['date'];
		if($taskViaURL == 'modify' && !empty($dateViaURL))
			//$iframeURL = "https://www.scoutnet.de/community/kalender/events.php?task=modify&SSID=".$calendarID."&Events_ID=".$dateViaURL;
			$iframeURL = "https://www.scoutnet.de/community/kalender/events.html?task=modify&contentonly=true&SSID=".$calendarID."&Events_ID=".$dateViaURL;
		elseif($taskViaURL == 'delete' && !empty($dateViaURL))
			//$iframeURL = "https://www.scoutnet.de/community/kalender/events.php?task=delete&SSID=".$calendarID."&Events_ID=".$dateViaURL;
			$iframeURL = "https://www.scoutnet.de/community/kalender/events.html?task=delete&contentonly=true&SSID=".$calendarID."&Events_ID=".$dateViaURL;
		elseif($taskViaURL == 'create' && !empty($dateViaURL))
			//$iframeURL = "https://www.scoutnet.de/community/kalender/events.php?task=create&SSID=".$calendarID."&Events_ID=".$dateViaURL;
			$iframeURL = "https://www.scoutnet.de/community/kalender/events.html?task=create&contentonly=true&SSID=".$calendarID."&Events_ID=".$dateViaURL;
		elseif($taskViaURL == 'create')
			//$iframeURL = "https://www.scoutnet.de/community/kalender/events.php?task=create&SSID=".$calendarID;
			$iframeURL = "https://www.scoutnet.de/community/kalender/events.html?task=create&contentonly=true&SSID=".$calendarID;
		else // overview 
			//$iframeURL = "https://www.scoutnet.de/community/kalender/events.php?task=overview&SSID=".$calendarID;
			$iframeURL = "https://www.scoutnet.de/community/kalender/events.html?task=overview&contentonly=true&amp;SSID=".$calendarID;
		
		if (isset($_POST['Submit'])) :
			$calendarID = $this->option_functions->update_and_return_calendarID(intval($_POST['scoutnetkalender_id']));
			$this->get_options();
?>
	<div class="updated">
		<p>
			<strong>Deine Kalender-Nummer <?php echo $calendarID; ?> wurde gespeichert.<br />
			Du kannst sie jederzeit <a href="admin.php?page=kalender_optionen">in den Optionen ändern</a>.</strong><br />
			Unter <a href="admin.php?page=kalender_hilfe">»Hilfe«</a> findest du die Informationen der vorherigen Seite.
		</p>
	</div>
<?php 	endif; ?>
	<div class="wrap">
		<h2>neuer Termin / Terminübersicht</h2>
	
		<p>
			<a href="https://www.scoutnet.de/community/kalender/events.html?task=create&SSID=<?php echo $calendarID; ?>" target="kalenderFrame" class="button">Neuer Termin</a>
			<a style="margin-left: 20px;" href="https://www.scoutnet.de/community/kalender/events.html?task=overview&amp;SSID=<?php echo $calendarID; ?>" target="kalenderFrame" class="button">Termine anzeigen</a>
			<a style="margin-left: 540px;" href="<?php bloginfo('siteurl'); ?>?page_id=8" title="Das Admin-Interface verlassen und die Kalender-Seite anzeigen...">Kalender-Seite ansehen »</a>
		</p>
		
		<iframe id="kalenderFrame" name="kalenderFrame" src="<?php echo $iframeURL; ?>" width="100%" height="520">
			Dein Browser unterstützt keine IFRAMEs. Bitte verwende <a href="https://www.scoutnet.de/community/kalender/events.html?task=overview&amp;SSID=<?php echo $calendarID; ?>">diesen Link zur Scoutnet-Kalender-Seite</a>.
		</iframe>
		<p>
			Wiederkehrende Ereignisse wie eure Gruppenstunden- und Leiterrundentermine könnt ihr zusätzlich <a href="<?php bloginfo('siteurl'); ?>/wp-admin/up_menumanager.php">in die Menü-Seite</a> "Kalender" schreiben.<br />
			Der Text dieser Seite wird über dem Kalender angezeigt.<br /> 
			<small>Euer Kalender <?php echo $calendarID; ?> wurde vor <?php 
				echo "".(round((time() - $calendarCachetime)/60))." Minuten zuletzt aktualisiert";
				echo " (Cache-Dauer ".($calendarCacheminutes);
				//echo (time() - $calendarCachetime) // - $calendarCacheminutes*60 
				?> Minute). Die nächste Aktualisierung ist <?php if($time_left_until_fresh_calendar>0) { ?>also in <?php 
					echo $time_left_until_fresh_calendar; ?> Sekunden bzw. <?php 
					echo round($time_left_until_fresh_calendar/60); ?> Minuten<?php } 
					else { ?>sobald der Kalender erneut aufgerufen wurde<?php } ?>.
			<!-- Fuer Localhost muss der \ durch einen / ersetzt werden. Fuer Online ist er richtig... -->
			<a href="<?php bloginfo('home')?>/wp-admin/admin.php?page=sp_scoutnetKalender/sp_scoutnetcalendar.php&amp;debug=anzeigen">debug</a>.
			</small>
		</p>
	</div>
	
<?php
			//$current_cache = get_option('sp_ScoutnetCalendar_cache_for_id_'.$calendarID);
			//$current_cache['time_of_last_cache'] = time();
			//update_option('sp_ScoutnetCalendar_cache_for_id_'.$calendarID, $current_cache);
			//echo "hund".$current_cache['time_of_last_cache'];
		/**
		 *	Debugging-View zeigt alle Options-Tabellen-Einträge und die Ausgabe des Caches im Vergleich zur Orginalausgabe via XML.
		 */
		if($_GET['debug']=='anzeigen')
		{
			$calendarData = $this->option_functions->get_cacheObject($calendarID);
			$data = new sp_ScoutnetCalendarData($calendarID, 'yes');
			$calendarURL = "bug: unbekannt";// TODO: Funktioniert nicht: $this->option_functions->calendarURL;//$data->calendarURL;
			$freshCalendarData = $data->getDataFromScoutnetAsArray();
?>
	<div class="wrap">
		<h2>Debug</h2>
<pre>
KalenderID: <?php print_r($calendarID); ?> 
Orginal-XML-URL: <?php print_r($calendarURL); ?> 

Cache-Time: <?php print_r($calendarCachetime); ?> // Cache zuletzt vor <?php echo (time() - $calendarCachetime)/60; ?> Minuten aktualisiert.
Cache-Minutes: <?php print_r($calendarCacheminutes); ?> // Cache alle <?php echo $this->options['cache_duration']; ?> Minuten aktualisieren. 300 = 5 Std.
Cached-Kalender-Array: 
<?php print_r($calendarData); ?> 

========================================================================================================
Orginal-XML-URL: <?php print_r($calendarURL); ?> 
New-Kalender-Array from XML: 
<?php print_r($freshCalendarData); ?> 

========================================================================================================
Unterschied zwischen "Cached-Kalender-Array" und "Orginal-XML-Kalender-Array": 
(Kann nur eine Hierarchieebene vergleichen daher Array geflattened flattenArray())
<?php print_r(array_diff(flattenArray($calendarData), flattenArray($freshCalendarData))); ?> 

</pre>
	</div>
<?php
		} // if debug
	}
	
	
	/**
	 *	Ausgabe der Adminseite "Optionen"
	 */
	function MenuOptionen() {
		$calendarID = $this->option_functions->get_calendarID();
		$kalenderCacheminutes = $this->option_functions->get_cacheminutes();
		if (isset($_POST['Submit'])) 
		{
			$calendarID = $this->option_functions->update_and_return_calendarID(intval($_POST['scoutnetkalender_id']));
			$kalenderCacheminutes = $this->option_functions->update_and_return_cacheminutes(intval($_POST['scoutnetkalender_cacheminutes']));
			$this->get_options();
?>
		<div class="updated">
			<p><strong><?php _e('Options saved.') ?></strong> ID=<?php echo $calendarID ?> and CacheTime=<?php echo $kalenderCacheminutes ?></p>
		</div>
<?php
		} 
?>

	<div class="wrap">
		<h2>Optionen für den Scoutnet-Kalender</h2>
 		<form name="scounetkalender" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<p>
				<strong>
					Kalender-Nummer: 
					<input name="scoutnetkalender_id" id="scoutnetkalender_id" type="text" value="<?php echo $calendarID ?>" size="3" />
				</strong>
			</p>
			<p class="hilfetext">
				<em>Wie finde ich meine Kalender-Nummer?</em><br />
				Deine Kalender-Nummer wird von Scoutnet "SSID" genannt. Bitte verwende die Seite 
				<a class="button" href="https://community.scoutnet.de/kalender/request-permissions.php?search_string=<?php sp_OrtsnameAusBloginfo(); ?>">"SSID finden" bei Scoutnet</a>
				um deine Kalender-Nummer/SSID zu erhalten.<br />
				Eventuell musst du das Suchwort auf dieser Seite verändern um deinen Stammes-/Bezirks-/...-Kalender zu finden.<br />
				Wie immer wirst du zuerst nach deinem Scoutnet-Benutzernamen und -Passwort gefragt (<a href="<?php bloginfo('siteurl'); ?>/wp-admin/admin.php?page=kalender_hilfe#accout">Hilfe hierzu</a>).<br />
				Übrigens: Auf der Such-Seite musst du auch die <strong>Rechte für deinen Kalender beantragen</strong>.
			</p>
			<p>
				<strong>
					Kalender nach 
					<input name="scoutnetkalender_cacheminutes" id="scoutnetkalender_cacheminutes" type="text" value="<?php echo $kalenderCacheminutes; ?>" size="5" /> 
					Minuten aktualisieren.
				</strong>
			</p>
			<p class="hilfetext">
				Der Kalender von Scoutnet wird in Wordpress zwischengespeichert.<br />
				Empfohlen ist ein Wert von 5 Stunden = 300 Minuten. 
			</p>
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
			</p>
		</form>
	</div>
<?php
	}

	
	/**
	 *	Ausgabe der Adminseite "Hilfe"
	 */
	function MenuHilfe() {
		$hilfe = new sp_ScoutnetCalendarHelp;
?>
	<div class="wrap">
		<h2>Hilfe</h2>
		<h3>Übersicht</h3>
		<ul>
			<li><a href="#ueber">Über den Kalender</a></li>
			<li><a href="#anzeigen">Kalender in der Website anzeigen</a></li>
			<li><a href="#anmelden">In der Scoutnet-Community anmelden</a></li>
			<li><a href="#kalender">Kalender-Rechte und Kalender-Nummer</a></li>
		</ul>
	</div>
<?php
		$hilfe->ueber();
?>
	<div class="wrap">
		<h3><a name="anzeigen"></a>Kalender in der Website anzeigen</h3>
		<p>
			Es gibt drei Möglichkeiten, den Kalender in dieser Website anzeigen zu lassen:
		</p>
		<h4>Stammes-Kalender</h4>
		<p>TODO: Hilfe-Text muss noch geschrieben werden. Wer hilft?</p> 
		<h4>Termine für eine Gruppe</h4>
		<p>TODO: Hilfe-Text muss noch geschrieben werden. Wer hilft?</p> 
		<p>[TerminListe=Filterkriterium]</p>
		<h4>Einzelne Termin in einem Artikel</h4>
		<p>TODO: Hilfe-Text muss noch geschrieben werden. Wer hilft?</p> 
		<p>[Termin=0000] (0000 = Termin-ID)</p>
	</div>
<?php
		$hilfe->anmelden();
		$hilfe->recht_nummer('','hilfe');
?>
<?php 
	}
}




/**
 *	Bloginfo bereinigen für die Kalender-Suchein Scoutnet...
 */
function sp_OrtsnameAusBloginfo() {
	$bloginfo = get_bloginfo('name');
	$bloginfo = str_replace("Pfadfinder", "", $bloginfo);
	$bloginfo = trim($bloginfo);
	echo $bloginfo;
}

?>