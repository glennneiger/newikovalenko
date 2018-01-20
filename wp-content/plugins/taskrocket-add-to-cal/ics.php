<?php
// Get the query strings and set vars
$title          = $_GET["title"] . "\r\n";
$filename       = $_GET["filename"] . ".ics\r\n";
$datestart      = $_GET["datestart"] . "\r\n";
$dateend        = $_GET["dateend"] . "\r\n";
$description    = $_GET["description"] . "\r\n";
$uniqid         = $_GET["uniqid"] . "\r\n";
$uri            = $_GET["uri"] . "\r\n";
$address        = $_GET["uri"] . "\r\n";

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

function escapeString($string) {
  return preg_replace('/([\,;])/','\\\$1', $string);
}

?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:<?php echo $dateend; ?>
UID:<?php echo $uniqid; ?>
DTSTAMP:<?php echo time(); ?>
LOCATION:<?php echo escapeString($address); ?>
DESCRIPTION:<?php echo escapeString($description); ?>
URL:<?php echo escapeString($uri); ?>
SUMMARY:<?php echo escapeString($title); ?>
DTSTART:<?php echo $datestart; ?>
END:VEVENT
END:VCALENDAR
