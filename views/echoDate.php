<?php
/*
 * 	View: Echo Date...
 *	View for sp_scounetKalendarViaXML_CalendarView.php
 *	Created on 08.12.2006
 */
		$d = $calendarData->dateFunc;
?>
	<!--
		Generator:     ScoutPress Kalender-Plugin fuer den Scoutnet-Kalender
		Kalender-Name: <?php echo $calendarData->calFunc->get_ident(); ?>
		Kalender-ID:   <?php echo $calendarData->calendarID; ?>
		Eintrag-ID:    <?php echo $terminID; ?>
		Eintrag-Title: <?php echo $calendarData->dateFunc->get_title($terminID); ?>
		URL:           <?php echo $calendarData->calendarURL; ?>
		Cache:         <?php echo $calendarData->cache_status; ?>
	-->
	<div class="vevent">
		<p class="meta top">
			Heute ist <?php echo mysql2date('D, d.m.Y', current_time('mysql')); ?>. Es ist <?php echo date('H:i'); ?> Uhr. KW <?php echo date('W'); ?><br />
			<?php
			if($d->get_timeStart($terminID, 'U') < date('U') && function_exists('time_since'))
			{ // Termin vor in Vergangenheit
				echo "Vor ";
				echo time_since($d->get_timeStart($terminID, "U"), time());
				echo " fand der folgende Termin statt:";
			}
			elseif(function_exists('time_since'))
			{ // Termin in der Zukunft
				echo "In ";
				echo time_since(time(), $d->get_timeStart($terminID, "U"));
				echo " findet dieser Termin statt:";
			} ?>
		</p>

		<h2 class="summary"><?php echo $d->get_title($terminID); ?><span class="kw"> KW <?php echo $d->get_timeStart($terminID, "W"); ?></span></h1>

		<div class="text entry-content">
			<div class="">

				<h2 class="datum"><?php
					if($d->get_timeStart($terminID, 'Ymd')!=$d->get_timeEnd($terminID, 'Ymd')) {
						$d->echo_timeStartWithABBR($terminID, "l, d.m.y");
						echo " &ndash; ";
						$d->echo_timeEndWithABBR($terminID, "l, d.m.y");
					} else {
						$d->echo_timeStartWithABBR($terminID, "l, d.m.Y");
					}
				?></h2>
				<h2 class="zeit"><?php
					if($d->get_timeStart($terminID, 'H')!='00') {
						$d->echo_timeStartWithABBR($terminID, 'H:i');
						if($d->get_timeEnd($terminID, 'H')!='00') {
							echo "&ndash;";
							$d->echo_timeEndWithABBR($terminID, 'H:i');
						}
						echo " Uhr";
					}
				?></h2>

				<p>
					<strong>Treffpunkt/Ort:</strong> <span class="location"><?php echo $d->get_locationIfLocation($terminID, '', ''); ?></span><?php
					$d->echo_maplinkIfLocation($terminID); ?>
				</p>

				<div class="description">
<?php $d->echo_groupImagesWithOptionalGroupname($terminID, "							<p><strong>Stufen:</strong> ", "</p>", TRUE); ?>
<?php echo $d->get_categories($terminID, "							<p><strong>Kategorien:</strong> ", "</p>"); ?>

					<blockquote>
<?php echo $d->get_desc($terminID); ?>
					</blockquote>
				</div>
			</div>

			<?php $this->get_editLink($terminID) ?>


			<p class="meta bottom">
				Erstellt von <?php echo $d->get_author($terminID);?> am <?php echo $d->get_timeCreate($terminID, "D, d.m.Y, H:i"); ?> h.<br />
				<?php echo $d->echo_updateInfoIfUpdated($terminID, "D, d.m.Y, H:i", "Zuletzt aktualisiert am ", " durch ", ".<br />") ?>
				<?php echo $calendarData->calFunc->get_ident(); ?>
			</p>

		</div>
		<?php /* ?><div class="page-and-category-content">
			<a href="?page_id=28&amp;termin="><?php echo array_getNextKey($calendarData->dateFunc->dateArray, $terminID); ?>">nächter Termin</a>
			<a href="?page_id=28&amp;termin=<?php  ?>">vorheriger Termin</a>
		</div><?php */ ?>
		<div class="meta bottom">
			<!--small><a href="<?php echo $calendarData->calendarSmartyURL ?>ical_einzeln.php?entryids=<?php echo $terminID; ?>&amp;template=<?php echo urlencode($calendarData->calendarSmartyURL) ?>" title="Diesen Termin z. B. in Outlook einfügen">
				<img src="<?php bloginfo('siteurl'); ?>/wp-content/plugins/sp_scoutnetKalender/bilder/ical_einzeln.png" alt="Kalender-Logo des Mozilla-Projektes" />
				iCal-Kalender für euren Terminkalender
			</a></small-->
		</div>
	</div>
<?php
?>
