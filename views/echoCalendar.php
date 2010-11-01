<?php
/*
 * 	View: Echo Calendar...
 *	View for sp_scounetKalendarViaXML_CalendarView.php
 *	Created on 08.12.2006
 *	Version 1.1 THX@Rocky
 */
		//print_r($calendarData); // DEBUG
		$d = $calendarData->dateFunc; // Shortcut

		$options = new sp_ScoutnetCalendarOption;
		if(empty($calendarID)) $this->calendarID = $options->get_calendarID();

		if(empty($ebenenup))
		{
			$ebenenup=empty($_REQUEST['ebenenup'])?0:$_REQUEST['ebenenup'];
		}

		if(empty($startDatum))
		{	// Startdatum auf vorgestern setzen
			$startDatum = date('Y-m-d', strtotime("-2 days"));
		}
		else
		{	// Startdatum auf Wert aus der URL setzen (wird an Funktion übergeben)
			$startDatum = date('Y-m-d', strtotime($startDatum));
		}

		$dateCounterMax = 12; // zum Zählen des Maximalwerts
		$dateCounterCurrent = 0; // zum Zählen des Maximalwerts
		$prevTimeStart = ''; // für die Anzeige der Monats-TH
		$urlOfThisSite = $this->urlToCalendarPage;

?>
	<!--
		Generator:     ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender
		Kalender-Name: <?php echo $calendarData->calFunc->get_ident(); ?>
		Kalender-ID:   <?php echo $calendarData->calFunc->get_id(); ?>
		URL:           <?php echo $calendarData->calendarURL; ?>
		Cache:         <?php echo $calendarData->cache_status; ?>
	-->
<?php
	// Kalender-URL-Ausgeben wenn cache manuell ein-/ausgeschaltet wird
	if($_REQUEST['cache'])
	{
?>
	<p>
		Debug Calendar-Data: <?php echo $calendarData->cache_status; ?>
		<a href="<?php echo $calendarData->calendarURL; ?>"><?php echo $calendarData->calendarURL; ?></a>
	</p>
<?php
	}

?>
	<div class="kalenderliste">
		<h2 class="kalenderlistehead">
			<span class="headflash">
				<small style="font-size: 14px;">am <?php echo date("d.m.Y"); ?> um <?php echo date("H:i"); ?> Uhr</small>
			</span>
<?php 		$this->get_newLink() ?>
			<span class="headtitle">Termine<?php
				if(!empty($filter))	echo " für »".$filter."«";
				?><br /><small><?php
				if($filter) {
				?><a href="<?php echo $urlOfThisSite; ?>&amp;start_datum=<?php echo $startDatum; if (!empty($ebenenup)) echo '&ebenenup='.$ebenenup; ?>
				?>">Alle Termine anzeigen...</a><?php
				}
				else
				{
				    //bloginfo('name');
        } ?></small></span>
				<div class="clear"></div>
		</h2>
		<div class="kalendercontent">
	 	    <?php  sp_ScoutnetCalender_getMonatsubersichtInline($startDatum,$filter,$ebenenup); ?>
	
			<div class="clear"></div>
			<table>
			<thead>
				<tr>
					<!--th>Datum/Uhrzeit</th>
					<th class="terminbeschreibung">Beschreibung (Gruppe)</th-->
					<td colspan="2"></td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2" class="kalenderzusatzinfo">
						Übrigens: Ihr könnt diesen Kalender
						<a href="<?php echo $this->get_rssKalenderLink($calendarData, $d->calendarID); ?>">als RSS-Feed abonnieren</a>
						oder in <a href="<?php echo $this->get_icalKalenderLink($calendarData, $d->calendarID); ?>">iCal-kompatible Kalendersoftware importieren</a>
						(<a href="http://kalender.scoutnet.de/infos/in_Kalenderprogramme.html">Mehr Info...</a>).
					</td>
				</tr>
			</tfoot>
			<tbody>
	<?php	
			/**
			 *	Die Schleife, die die einzelnen Termine ausgibt...
			 */
	
			foreach($d->dateArray as $terminID => $date)
			{
				// CC in InlineDate
				if(function_exists('mygallery'))
				{	// Die Detaillinks werden auf Scriptaculaus eingestellt das von MyGallery included wird
					$dateDetailsHref = "javascript:return true;";
					$dateDetailsOnclick = "Effect.toggle('beschreibung".$terminID."', 'slide'); Effect.toggle('mehrLink".$terminID."', 'appear'); Effect.toggle('wenigerLink".$terminID."', 'appear'); new Effect.Highlight('beschreibung".$terminID."')";
				}
				else
				{	// Die Detaillinks werden mit händischem JS umgesetzt
					$dateDetailsOnclick = "if (document.getElementById('beschreibung".$terminID."').style.display == 'none') { document.getElementById('beschreibung".$terminID."').style.display = 'block'; } else { document.getElementById('beschreibung".$terminID."').style.display = 'none'; }";
					$dateDetailsHref = "javascript:return false;";
	//				$dateDetailsHref = $urlOfThisSite."&amp;termin=".$terminID;
	//				$dateDetailsOnclick = "";
				}
				$dateDetailsOnclick = "$('#beschreibung".$terminID."').slideDown();$('#wenigerLink".$terminID."').show();$('#mehrLink".$terminID."').hide(); return false;";
				$dateDetailsHref = "";
				
				$dateDetailsOnclickWeniger = "$('#beschreibung".$terminID."').slideUp();$('#wenigerLink".$terminID."').hide();$('#mehrLink".$terminID."').show(); return false;";
				$dateDetailsHrefWeniger = "";
				
				// Termine nur ausgeben wenn sie älter als $startDatum sind
				// UND Maximal 15 Termine ausgeben
				// DEBUG
	//			echo $d->get_timeStart($terminID, 'Y-m-d')." >= ".$startDatum."<br>";
	//			echo $dateCounterCurrent." < ".$dateCounterMax."<br/>";
	//			echo $d->in_filter($terminID, $filter)."<br/>";
				if($d->get_timeStart($terminID, 'Y-m-d') >= $startDatum
					&& $dateCounterCurrent < $dateCounterMax
					&& $d->in_filter($terminID, $filter))
				{
					// Monate-TH nur anzeigen wenn Monatswechsel im vgl. zum vorherigen Post.
					if($prevTimeStart < $d->get_timeStart($terminID, 'Ym'))
					{
	?>
				<tr class="monat<?php
					if($d->get_timeStart($terminID, 'Ym') == date('Ym')) {
						?> aktuellerMonat<?php }
					?>">
					<th colspan="2"><span class="monat"><?php echo $d->get_timeStart($terminID, 'F Y')." </span><span class=\"monatAlsZahl\">(".$d->get_timeStart($terminID, 'm').")</span>" ?></th>
				</tr>
	<?php				} ?>
				<tr class="vevent<?php
					if($d->get_timeStart($terminID, 'Ym') == date('Ym')) {
						?> aktuellerMonat<?php
					} elseif($d->get_timeStart($terminID, 'Ymd') == date('Ymd')) {
						?> aktuellerTag<?php }
					?>">
					<td>
						<a name="beschreibungLink<?php echo $terminID ?>"></a>
						<strong class="datum"><?php
							if($d->get_timeStart($terminID, 'Ymd')!=$d->get_timeEnd($terminID, 'Ymd')) {
								$d->echo_timeStartWithABBR($terminID, "D, d.m.y");
								echo "&nbsp;&ndash;<br />";
								$d->echo_timeEndWithABBR($terminID, "D, d.m.y");
							} else {
								$d->echo_timeStartWithABBR($terminID, "D, d.m.Y");
							} ?></strong>
						<br />
						<span class="zeit"><?php
						if($d->get_timeStart($terminID, 'H')!='00') {
							if($d->get_timeEnd($terminID, 'H')!='00') {
								$d->echo_timeStartWithABBR($terminID, 'H:i');
								echo "&ndash;";
								$d->echo_timeEndWithABBR($terminID, 'H:i');
							} else {
								$d->echo_timeStartWithABBR($terminID, 'H:i');
								echo " h";
							}
						}
						?></span>
					</td>
					<td class="beschreibung">
						<strong class="summary"><?php echo $d->get_title($terminID); ?></strong>
						<span class="gruppe">&nbsp;<?php $d->echo_groupImages($terminID); ?></span>
						<br />
						<a href="<?php echo $dateDetailsHref; ?>" onclick="<?php echo $dateDetailsOnclick; ?>">
							<span class="mehrLink" id="mehrLink<?php echo $terminID ?>">
								<?php echo $d->get_dateExcerpt($terminID); ?>
								// Details...
							</span>
						</a>
					</td>
				</tr>
				<tr class="description<?php
					if($d->get_timeStart($terminID, 'Ym') == date('Ym')) {
						?> aktuellerMonat<?php
					} elseif($d->get_timeStart($terminID, 'Ymd') == date('Ymd')) {
						?> aktuellerTag<?php }
					?>">
					<td colspan="2">
						<div id="beschreibung<?php echo $terminID ?>" style="display: none;">
							<a class="url" href="<?php echo $urlOfThisSite ?>&amp;termin=<?php echo $terminID ?>&ebenenup=<?php echo $ebenenup; ?>">» mehr Informationen in der <strong>Einzelansicht</strong></a>
							<ul>
								<li>
									<strong>Zielgruppe:</strong>
	<?php echo $d->get_targetgroup($terminID); ?>
								</li>
								<li>
									<strong>Link:</strong>
	<?php echo $d->get_link($terminID); ?>
								</li>
								<li>
									<strong>Organisator:</strong>
	<?php echo $d->get_organizer($terminID); ?>
								</li>
								<li>
									<strong>Beschreibung:</strong>
	<?php echo $d->get_desc($terminID); ?>
								</li>
								<li><?php echo $d->get_categories($terminID, "<strong>Kategorien:</strong> ", ""); ?></li>
								<li class="location">
									<strong>Treffpunkt/Ort:</strong> <?php
									echo $d->get_locationIfLocation($terminID, '', '');
									$d->echo_maplinkIfLocation($terminID); ?>
								</li>
							</ul>
							<a href="<?php echo $dateDetailsHrefWeniger; ?>" onclick="<?php echo $dateDetailsOnclickWeniger; ?>" style="display:none;" class="wenigerLink" id="wenigerLink<?php echo $terminID ?>">
								einklappen...
							</a>
	<?php					$this->get_editLink($terminID) ?>
						</div>
					</td>
				</tr>
	<?php
					$prevTimeStart = $d->get_timeStart($terminID, 'Ym'); // fügt den Monat am foreach-kopf ein
					$lastTimeStart = $d->get_timeStart($terminID, 'Y-m-d');	// setzt das zuletzt aufgerufene Datum für den Weiter-Link
					$dateCounterCurrent++; // für den Max-Counter
				}
			} // Ende Date-Foreach
	?>
				<tr class="kalenderlisteWeiter">
					<td colspan="4">
						<a href="<?php
							echo $urlOfThisSite."&amp;start_datum=".$lastTimeStart."&amp;filter=".$filter.(!empty($ebenenup)?'&ebenenup='.$ebenenup:'');
							?>">Die nächsten <?php echo $dateCounterMax ?> Termine<?php
					if(!empty($filter))	echo " für »".$filter."«";
					?> anzeigen &ndash;»</a>
					</td>
				</tr>
			</tbody>
			</table>
		</div>
	</div> <!-- #kalenderliste -->
<?php
?>
