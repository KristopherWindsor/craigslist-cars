<?php

require_once __DIR__ . '/SourceStats.php';

$url = $argv[1];
$rss = file_get_contents($url);
$results = new SimpleXMLElement($rss);

$stat = SourceStats::loadForSource($url);
$hour = date('G');
$stat->recordHit($hour);
$stat->save();

$data = [];
foreach ($results->item as $item) {
    $data[] = (string) $item->link;
}

echo json_encode([$url, $data]);
