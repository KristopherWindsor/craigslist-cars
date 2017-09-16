<?php

$url = $argv[1];
$rss = file_get_contents($url);
$results = new SimpleXMLElement($rss);

$data = [];
foreach ($results->item as $item) {
  $data[] = (string) $item->link;
}

echo json_encode($data);

sleep(1);

