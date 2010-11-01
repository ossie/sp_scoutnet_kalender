<?php
/*
 * 	View: Echo Inline-Date...
 *	View for sp_scounetKalendarViaXML_CalendarView.php
 *	Created on 08.12.2006
 */
		$d = $calendarData->dateFunc;
		// CC in Kalender-Liste
		if(function_exists('mygallery'))
		{	// Die Detaillinks werden auf Scriptaculaus eingestellt das von MyGallery included wird
			$dateDetailsHref = "javascript:return true;";
			$dateDetailsOnclick = "Effect.toggle('beschreibungInline".$terminID."', 'slide'); new Effect.Highlight('beschreibungInline".$terminID."', 'Yellow', '#D6E5F0')";
		}
		else
		{	// Die Detaillinks werden mit handischem JS umgesetzt
			$dateDetailsHref = "document.getElementById('beschreibungInline".$terminID."').style.display = 'block';";
			$dateDetailsOnclick = "return false;";
		}
?>
	<!-- 
		Generator:     ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender
		Kalender-Name: <?php echo $calendarData->calFunc->get_ident(); ?>
		Eintrag-ID:    <?php echo $calendarData->calendarID; ?>
		Eintrag-Title: <?php echo $calendarData->dateFunc->get_title($terminID); ?>
		Cache:         <?php echo $calendarData->cache_status; ?> 
	-->
	<div class="kalenderliste eintermin">
		<div class="kalenderlistehead">
			<span class="headflash">
				<object type="application/x-shockwave-flash" data="<?php bloginfo('template_directory'); ?>/images/kalender-uhr.swf" width="36" height="36">
				<param name="movie" value="<?php bloginfo('template_directory'); ?>/images/kalender-uhr.swf" />
				<?php echo date("H:i"); ?>
				</object><br />
				<?php echo date("d.m.Y"); ?>
			</span>
			<div class="vevent">
				<a class="url" href="<?php echo $dateDetailsHref; ?>" onclick="<?php echo $dateDetailsOnclick; ?>">
					<?php
							if($d->get_timeStart($terminID, 'Ymd')!=$d->get_timeEnd($terminID, 'Ymd')) { 
								$d->echo_timeStartWithABBR($terminID, "l, d.m.y - H:i"); 
								echo " &ndash; ";
								$d->echo_timeEndWithABBR($terminID, "l, d.m.y"); 
							} else { 
								$d->echo_timeStartWithABBR($terminID, "l, d.m.Y - H:i");
							} 
						?>
					 - <span class="summary">
					<?php echo $d->get_title($terminID); ?>
					</span> - in
					<span class="location">
					<?php echo $d->get_locationIfLocation($terminID); ?>
					</span>
				</a>
				<div id="beschreibungInline<?php echo $terminID; ?>" class="description" style="display: none; background-color: #D6E5F0; padding: 10px;">
<?php $d->echo_groupImagesWithOptionalGroupname($terminID, "					<strong>Stufen:</strong> ", "", TRUE); ?>
					<br />
<?php echo $d->get_desc($terminID); ?>
					<br />
					<small class="meta">
						Erstellt von <?php echo $d->get_author($terminID);?> am <?php echo $d->get_timeCreate($terminID, "D, d.m.Y, H:i"); ?> h.<br /> 
						<?php echo $d->echo_updateInfoIfUpdated($terminID, "D, d.m.Y, H:i", "Zuletzt aktualisiert am ", " durch ", ".<br />") ?>
						<?php echo $calendarData->calFunc->get_ident(); ?>
					</small>
					<br />
					<a class="url" href="<?php echo $this->urlToCalendarPage ?>&amp;termin=<?php echo $terminID ?>">» mehr Informationen in der Einzelansicht</a>
				</div>
			</div>
		</div>
	</div>
<?php
?>
