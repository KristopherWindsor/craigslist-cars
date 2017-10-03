<?php

require_once __DIR__ . '/SourceStats.php';

$path = $argv[1];
list($sourceUrl, $urls) = json_decode(file_get_contents('php://stdin'));

$totalAdded = 0;
foreach ($urls as $url) {
  $filename = $path . '/' . preg_replace("/[^A-Za-z0-9 ]/", '', $url) . '.html';
  if (file_exists($filename))
    continue;

  $page = @file_get_contents($url);
  if (!$page)
    continue;

  file_put_contents($filename, $page);
  $totalAdded++;
  sleep(1);
}

if ($totalAdded) {
	$stat = SourceStats::loadForSource($sourceUrl);
	$hour = date('G');
	$stat->resultsFound($hour, $totalAdded);
	$stat->save();
}
