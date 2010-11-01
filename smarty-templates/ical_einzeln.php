<?php
header("Content-type: text/plain");
//header('Content-type: application/pdf');
// Es wird downloaded.pdf benannt
header('Content-Disposition: attachment; filename="termin.ics"');
// http://de.php.net/header
include("http://kalender.scoutnet.de/2.0/show.php?entryids=".$_GET['entryids']."&template=".urldecode($_GET['template'])."ical_einzeln.tpl");
?>