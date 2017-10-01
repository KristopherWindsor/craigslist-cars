<?php

$path = $argv[1];
$urls = json_decode(file_get_contents('php://stdin'));

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

// no rush to hit CL again...
if (!$totalAdded)
	sleep(300);
