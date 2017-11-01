<?php

$instructions = json_decode(file_get_contents('php://input'));

if (!$instructions)
  die();

$target = $instructions->target;
foreach ($instructions->url as $url) {
  $filename = __DIR__ . '/data/' . md5($url);
  file_put_contents($filename, $url . "\n" . $target);
}

