<?php

/*
 *	Class for:     ScoutPress Scoutnet-Kalender
 *	Class-Name:    Hilfe-Wrapper
 *	Description:   Klasse enthält Funktionen die Hilfemdeldungen ausgeben.
 *  Version        0.21 / 07-11-25
 *	View-Author:   Tobias Jordans

 *	Copyright:     Siehe at sp_scoutnetKalender.php
 */
class sp_ScoutnetCalendarHelp 
{

	/**
	 *	Hilfe: Über
	 */
	function ueber($vorH3='') 
	{
?>
<div class="wrap">
	<h3><a name="ueber"></a><?php echo $vorH3; ?>Der Scoutnet-Kalender in ScoutPress</h3>
	<p>
		Der Scoutnet-Kalender ist ein Service von Scoutnet. Er ist in ScoutPress integriert um das Eintragen und Verwalten von Terminen zu vereinfachen.<br />
		Der große Vorteil des Scoutnet-Kalenders ist: Er bildet unsere Verbandsstruktur ab. Das bedeutet, dass jeder Termin, der von einem
		Stamm eingetragen wird, auf Bezirks-, Diözesan- und Bundesebene angezeigt werden kann. Oder ihr beispielsweise alle Ausbildungstermine aus eurem Bezirks anzeigen könnt...<br />
	</p>
	<p>
		In ScoutPress ist der Kalender auf die folgende Art integriert:
		<ol>
			<li>Es gibt eine Kalender-Seite die alle Stammestermine anzeigt.</li>
			<li>Ihr könnt die Termine eine Gruppe in euren Artikel anzeigen lassen (<a href="<?php bloginfo('home'); ?>/wp-admin/admin.php?page=kalender_hilfe#anzeige">mehr hierzu in der Hilfe...</a>)</li>
			<li>Ihr könnt einzelne Termine in euren Artikeln anzeigen lassen (<a href="<?php bloginfo('home'); ?>/wp-admin/admin.php?page=kalender_hilfe#anzeige">mehr hierzu in der Hilfe...</a>)</li>
		</ol>
	</p>
</div>
<?php
	}

	/**
	 *	Hilfe: Anmelden
	 */
	function anmelden($vorH3='') 
	{
?>
<div class="wrap">
	<h3><a name="anmelden"></a><?php echo $vorH3; ?>In der Scoutnet-Community anmelden</h3>
	<p>
		Solltet ihr noch nicht Mitglied der Scoutnet-Communty sein, <a class="button" href="https://community.scoutnet.de/create.php">registriere dich jetzt</a>.
		Da dieser Kalender ein Service von Scoutnet ist, müsst ihr euch immer, wenn ihr die Scoutnet-Community besucht, einloggen um eure Identität zu bestätigen.<br />
		<small>Wenn euch das ständige Einloggen nervt, <a href="mailto:kalender@scoutnet.de" title="eMail-Kontakt">schreibt es bitte dem Kalenderteam</a>. Je mehr Menschen sich melden, desto eher eher ist das Kalenderteam motiviert, eine einfachere Lösung bereitzustellen...</small>
	</p>
</div>
<?php
	}

	/**
	 *	Hilfe: Adminrechte und Kalendernummer
	 */
	function recht_nummer($vorH3='', $seiteDesAufrufs="") 
	{
?>
<?php if($seiteDesAufrufs!='neu') { ?>
<div class="wrap">
<?php } ?>
	<h3><a name="kalender"></a><?php echo $vorH3; ?>Kalender-Rechte und Kalender-Nummer</h3>
	<p>
		Nachdem du deine Anmeldung in der Communty bestätigt hast, benutzte die 
		<a class="button" href="https://www.scoutnet.de/community/kalender/request-permissions.html?search_string=<?php sp_OrtsnameAusBloginfo(); ?>">
		Kalender-Suche von Scoutnet</a> und euren Kalender zu finden.<br />
		Eventuell musst du das Suchwort auf dieser Seite verändern um deinen Stammes-/Bezirks-/...-Kalender zu finden.<br />
		<br />
		Wenn du deinen Kalender gefunden hast:
		<ol>
			<li>Notiere dir bitte die so genannte SSID aus der ersten Spalte und 
			<?php if($seiteDesAufrufs=='hilfe') { ?><a href="<?php bloginfo('siteurl'); ?>/wp-admin/admin.php?page=kalender_optionen"><?php } ?>
			trag sie später in das Feld "Kalender-Nummer" ein
			<?php if($seiteDesAufrufs=='hilfe') { ?></a><?php } ?>.</li>
			<li>
				Beantrage Administrations-Rechte für deinen Kalender.<br />
				Der Kalender-Admin des gewählten Kalender erhält den Antrag per E-Mail. <br />
				Nutze den Kommentar des Antrags um dem Admin mitzuteilen wer du bist, damit er dich freischaltet.<br />
				<small>(Wenn du nach ein paar Tagen keine Antwort erhalten hast, frag das <a href="mailto:kalender@scoutnet.de">Kalender-Team</a>.)</small>
			</li>
			<li>
				Sobald du freigeschaltet bist, kannst du Termine eintragen.<br />
				<?php if($seiteDesAufrufs!='hilfe') { ?>Und jetzt nur noch die SSID eintragen:<?php } ?>
			</li>
		</ol>
	</p>
<?php if($seiteDesAufrufs!='neu') { ?>
</div>
<?php } ?>
<?php
	}
}
?>