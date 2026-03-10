<?php
$_GET['id'] = 4;
ob_start();
include 'service-details.php';
$html = ob_get_clean();
echo "HTML Length: " . strlen($html) . "\n";
if (strpos($html, 'good copper') !== false) {
    echo "Found title 'good copper' in HTML.\n";
} else {
    echo "Did NOT find 'good copper' in HTML.\n";
}
if (strpos($html, 'copper bbusines') !== false) {
    echo "Found content 'copper bbusines' in HTML.\n";
} else {
    echo "Did NOT find 'copper bbusines' in HTML.\n";
}
echo "HTML Snippet: " . substr(strip_tags($html), 0, 500) . "...\n";
