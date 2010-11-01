<?php
/*
 *
 *	BETA! Noch nicht fertig und noch nicht im Einsatz!
 *
 *	Class for:     ScoutPress Scoutnet-Kalender
 *	Class-Name:    SIMILE-Timeline http://simile.mit.edu/timeline/
 *	Description:   Die Klasse für die SMILE-Timeline
 *	Changelog:     0.1 / 06-12-17: 
 *	View-Author:   Tobias Jordans

 *	Copyright:     Siehe at sp_scoutnetcalendar.php
 *
 */



class sp_scoutnetcalendar_simile
{
	var $options; // ScoutnetCalendar-Options
	
	function sp_scoutnetcalendar_simile()
	{
		// get the calendar-options
		$this->options = sp_ScoutnetCalendar_getOptions();
		// show the timeline
		//$this->show_js();
		//$this->show_timeline();
	}
	
	/**
	 *	echo the JS for SIMILE-Timeline
	 */
	function show_js()
	{
?>
	<!-- SIMILE-Timle-Integration for Scoutnet-Calendar // http://simile.mit.edu/timeline/ -->
	<script src="http://simile.mit.edu/timeline/api/timeline-api.js" type="text/javascript"></script>
	<script type="text/javascript">
	var tl;
	function onLoad() {
 		var eventSource = new Timeline.DefaultEventSource();
		var bandInfos = [
			Timeline.createBandInfo({
				eventSource:    eventSource,
				date:           "Jun 28 2006 00:00:00 GMT",
				width:          "70%", 
				intervalUnit:   Timeline.DateTime.MONTH, 
				intervalPixels: 100
			}),
			Timeline.createBandInfo({
				eventSource:    eventSource,
				date:           "Jun 28 2006 00:00:00 GMT",
				width:          "30%", 
				intervalUnit:   Timeline.DateTime.YEAR, 
				intervalPixels: 200
			})
		];
		bandInfos[1].syncWith = 0;
		bandInfos[1].highlight = true;
		tl = Timeline.create(document.getElementById("my-timeline"), bandInfos);
		Timeline.loadXML("<?php echo get_settings('home')."/wp-content/plugins/sp_scoutnetKalender/simile_timline_xml.php" ?>", function(xml, url) { eventSource.loadXML(xml, url); });
		//Timeline.loadXML("http://simile.mit.edu/timeline/docs/example1.xml", function(xml, url) { eventSource.loadXML(xml, url); });
		//Timeline.loadXML("<?php echo get_settings('home');?>/wp-content/plugins/sp_scoutnetKalender/example1.xml", function(xml, url) { eventSource.loadXML(xml, url); });
	}
	
	var resizeTimerID = null;
	function onResize() {
		if (resizeTimerID == null) {
			resizeTimerID = window.setTimeout(function() {
				resizeTimerID = null;
				tl.layout();
			}, 500);
		}
	}
	</script>
<?php	
	}
	
	/**
	 *	echo the XHTML for the SIMILE-Timeline
	 */
	function show_timeline()
	{
?>
	<!-- SIMILE-Timle-Integration for Scoutnet-Calendar // http://simile.mit.edu/timeline/ -->
	<div id="my-timeline" style="height: 150px; border: 1px solid #aaa"></div>
<?php	
	}
	
	/**
	 *	echo the output of the scoutnet-calendar-data as XML for the simile-timeline
	 */
	function show_xml()
	{
		// doesnt work // $calendar_data = new sp_ScoutnetCalendarData($this->options->id, 'yes');
		//$this->options = get_option('sp_ScoutnetCalendar_options');
		$calendar_data = get_option('sp_ScoutnetCalendar_cache_for_id_'.$this->options['id']);
		///* DEBUG: */ print_r($calendar_data);
		//<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
?>
<data 
    wiki-url="<?php bloginfo('home'); ?>/kalender/"
    wiki-section="Simile Scoutnet-Kalender für <?php bloginfo('title'); ?>"
    >
    <!-- Sources:
    	<?php bloginfo('home'); ?>
    	extention for the scoutnet-calendar-plugin by tobias jordans 
    	inspired by http://roswell.fortunecity.com/angelic/96/pctime.htm
    -->
<?php
	foreach($calendar_data['calendar']['dates'] as $date)
	{
		//Jan 01 1978 00:00:00 GMT-0600
		// funktion übersetzt gleichzeitig; das ist hier nachteilig...: mysql2date('F j Y G:i:s T-Y', $date['timestart']);
?>
    <event start="<?php echo date('D F j Y G:i:s T-Y', strtotime($date['timestart'])); ?>" 
    	<?php if($date['timeend']) { ?>end="<?php echo date('D F j Y G:i:s T-Y', strtotime($date['timeend'])); ?>"<?php } ?> 
        title="<?php echo $date['title']; ?>"
        >
<?php //echo $date['description']; ?> 
        </event>
<?php
	} // foreach
?>
</data>
<?php
	}
	
}

?>
