<?php
/*
 *	Class for:     ScoutPress Scoutnet-Kalender
 *	Class-Name:    Error-Wrapper
 *	Description:   Klasse enthält Funktionen die Fehlermeldungen ausgeben.
 *  Version        0.2 / 06-12-10
 *	View-Author:   Tobias Jordans

 *	Copyright:     Siehe at sp_scoutnetKalender.php
 */
class sp_ScoutnetCalenderErrors
{
	function NoDataError() 
	{
?>
	<div class="page-and-category-content">
		<div class="post-content">
			<h2 class="status protected">
				Die Daten konnten nicht geladen werden. <br />
				Entweder besteht ein Problem mit dem Scoutnet-Kalender-Dienst oder die URL-Anfrage war fehlerhaft.
			</h2>
		</div>
	</div>
<?php
	}
	function NoCalendarPageError() 
	{
?>
	<div class="page-and-category-content">
		<div class="post-content">
			<h2 class="status protected">
				Es konnte weder ein Menüpunkt 'Kalender', 'Termin' oder 'Termine' gefunden werden. <br />
				Der Kalender kann nicht angezeigt werden. Bitte wendet euch an das ScoutPress-Team.
			</h2>
		</div>
	</div>
<?php		
	}
	function xmlError($botschaft='') 
	{
?>
	<div class="page-and-category-content">
		<div class="post-content">
			<h2 class="status protected">
				Ein Fehler ist aufgetreten beim Interpretieren der XML-Daten für den Kalender.
			</h2>
			<p>
				<?php echo $botschaft; ?>
			</p>
		</div>
	</div>
<?php		
	}
	
	/**
	 *	Fehlermeldung
	 *	- wenn Kalender-Seite aufgerufen wird (mit Kalender-Template)
	 *	- aber Kalender noch nicht konfiguriert ist...
	 */
	function ConfigureCalenderFirst($botschaft='')
	{
?>
	<div class="page-and-category-content">
		<div class="post-content">
			
			<div class="help">
				<?php echo $botschaft; ?>
				<h2>Euer Scoutnet-Kalender ist noch nicht konfiguriert</h2>
				<p>
					Bitte benutzt die <a href="<?php bloginfo('home'); ?>/wp-admin/admin.php?page=sp_scoutnetKalender\sp_scoutnetcalendar.php">Kalender-Seite der 
					Administrationsoberfläche (nur für Webmaster)</a>. Dort werden ihr eingeführt, wie der Kalender konfiguriert werden muss.
					<br />
					Fragen bitte ins <a href="http://www.scoutpress.de/forum/">ScoutPress-Forum</a>!
				</p>
				<p>
					Nach der Konfiguration, sieht der Kalender in ScoutPress ungefähr so aus:
				</p>
				<p>
					<a href="http://www.dpsg-langerwehe.de/kalender/" title="So sieht es beim Stamm Langerwehe aus...">
						<em>Beispiel!</em><br />
						<img src="<?php bloginfo('home'); ?>/wp-content/plugins/sp_scoutnetKalender/bilder/sp_scoutnetKalender_Beispielscreenshot.png" alt="Beispiel des Scoutnet-Kalenders in Scoutnet" />
					</a>
				</p>
				<p>
					PS: Bitte beachtet auch <a href="http://www.scoutpress.de/lernen/">die Lernvideos zu ScoutPress</a>.
				</p>
			</div>

		</div>
	</div>
<?php
	}
	
	/**
	 *	Fehlermeldung 
	 *	- wenn Artikel mit [TerminListe=x] etc aufgerufen wird
	 *	- aber Kalender noch nicht konfiguriert ist...
	 */
	// Funktion echot die Fehlermeldung
	function echo_ConfigureCalenderFirst_InlineMessage()
	{
		echo ConfigureCalenderFirst_InlineMessage();
	}
	// Funktion returnt die Fehlermeldung 
	// Andernfalls kann sie durch das aufrufende Skript nicht richtig verarbeitet (meint im Text positioniert) werden.
	function ConfigureCalenderFirst_InlineMessage()
	{
		$message = "<p><em>Kalender-Hinweis: Ihr müsst den Kalender erst im Administrationsbereich konfigurieren bevor Termine angezeigt werden können.</em></p>";
		return $message;
	}
}

?>