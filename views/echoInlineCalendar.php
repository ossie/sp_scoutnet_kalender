<?php
/*
 * 	View: Echo inline Calendar...
 *	View for sp_scounetKalendarViaXML_CalendarView.php
 *	Created on 08.12.2006
 */
		//echo "katze".$calendarData, $calendarID, $startDatum, $endDatum, $filter;
		//echo "hund";print_r($calendarData);
		
		$d = $calendarData->dateFunc; // Shortcut
		
		//$options = new sp_ScoutnetCalendarOption;
		//if(empty($calendarID)) $this->calendarID = $options->get_calendarID();
		$this->calendarID = $calendarID;

		if(empty($startDatum))
		{	// Startdatum auf vorgestern setzen
			$startDatum = date('Y-m-d', strtotime("-2 days"));
		}
		else
		{	// Startdatum auf Wert aus der URL setzen (wird an Funktion übergeben)
			$startDatum = date('Y-m-d', strtotime($startDatum));
		}
		$dateCounterMax = 4; // zum Zählen des Maximalwerts
		$dateCounterCurrent = 0; // zum Zählen des Maximalwerts
		$prevTimeStart = ''; // für die Anzeige der Monats-TH
		$urlOfThisSite = $this->urlToCalendarPage;
?>

	<!-- 
		Generator:     ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender [echoInlineCalender()]
		Kalender-Name: <?php echo $calendarData->calFunc->get_ident(); ?> 
		Kalender-ID:   <?php echo $calendarData->calFunc->get_id()." ".$calendarID; ?> 
		Filter:        <?php echo $filter ?> 
		URL:           <?php echo $calendarData->calendarURL; ?> 
		Cache:         <?php echo $calendarData->cache_status; ?> 
	-->
	<div class="kalenderliste inline">
		<h2 class="kalenderlistehead">
			<span class="headflash">
				<object type="application/x-shockwave-flash" data="<?php bloginfo('template_directory'); ?>/images/kalender-uhr.swf" width="36" height="36">
				<param name="movie" value="<?php bloginfo('template_directory'); ?>/images/kalender-uhr.swf" />
				<?php echo date("H:i"); ?>
				</object><br />
				<?php echo date("d.m.Y"); ?>
			</span>
<?php 		/* FIXME: macht leider probleme im CSS... sollte behoben werden da Link hier eigentlich praktisch! *///	$this->get_newLink() ?>
			<span class="headtitle"><?php // echo $dateCounterMax; // echo count($d->dateArray); ?> Termine<?php 
				if(!empty($filter))	echo " für »".$filter."«"; 
				?><br />
			<small><a href="<?php echo $urlOfThisSite."&amp;filter=".$filter; ?>" title="zur Kalender-Seite wechseln">alle Termine <?php 
				if(!empty($filter))	echo " für »".$filter."«"; 
				?> anzeigen</a></small></span>
		</h2>
		
		<div class="clear"></div>
		<table>
		<tbody>
<?php

		/**
		 *	Die Schleife, die die einzelnen Termine ausgibt...
		 */
		foreach($d->dateArray as $terminID => $date)
		{
			// CC in InlineDate
			/*if(function_exists('mygallery'))
			{	// Die Detaillinks werden auf Scriptaculaus eingestellt das von MyGallery included wird
				$dateDetailsHref = "javascript:return true;";
				$dateDetailsOnclick = "Effect.toggle('beschreibung".$terminID."', 'slide'); Effect.toggle('mehrLink".$terminID."', 'appear'); Effect.toggle('wenigerLink".$terminID."', 'appear'); new Effect.Highlight('beschreibung".$terminID."')";
			}
			else
			{	// Die Detaillinks werden mit handischem JS umgesetzt
				$dateDetailsHref = "document.getElementById('beschreibung".$terminID."').style.display = 'block';";
				$dateDetailsOnclick = "return false;";
			}*/
			// Termine nur ausgeben wenn sie älter als $startDatum sind
			// UND Maximal X Termine ausgeben
			if($d->get_timeStart($terminID, 'Y-m-d') >= $startDatum 
				&& $dateCounterCurrent < $dateCounterMax
				&& $d->in_filter($terminID, $filter))
			{
?>

			<tr class="vevent<?php 
				if($d->get_timeStart($terminID, 'Ym') == date('Ym')) { 
					?> aktuellerMonat<?php  
				} elseif($d->get_timeStart($terminID, 'Ymd') == date('Ymd')) {
					?> aktuellerTag<?php } 
				?>">
				<td>
					<a name="beschreibungLink<?php echo $terminID ?>"></a>
					<span class="datum"><?php
						if($d->get_timeStart($terminID, 'Ymd')!=$d->get_timeEnd($terminID, 'Ymd')) { 
							$d->echo_timeStartWithABBR($terminID, "D, d.m.y"); 
							echo "&nbsp;&ndash; ";
							$d->echo_timeEndWithABBR($terminID, "D, d.m.y"); 
						} else { 
							$d->echo_timeStartWithABBR($terminID, "D, d.m.Y");
						} ?></span>
				</td>
				<td class="beschreibung">
					<strong class="summary"><?php echo $d->get_title($terminID); ?></strong>
					<a class="url" href="<?php echo $urlOfThisSite ?>&amp;termin=<?php echo $terminID ?>">Einzelansicht...</a>
				</td>
			</tr>
<?php
				$prevTimeStart = $d->get_timeStart($terminID, 'm'); // fügt den Monat am foreach-kopf ein
				$lastTimeStart = $d->get_timeStart($terminID, 'Y-m-d');	// setzt das zuletzt aufgerufene Datum für den Weiter-Link
				$dateCounterCurrent++; // für den Max-Counter
			}
		} // Ende Date-Foreach
?>

		</tbody>
		</table>
	</div>
<?php
?>
