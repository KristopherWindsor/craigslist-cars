<?php

if ($argc < 2)
  die('missing arg');

$idFile = __DIR__ . '/client_id';
if (!file_exists($idFile))
  file_put_contents($idFile, uniqid());
$clientId = file_get_contents($idFile);

$url = $argv[1];
$instructions = @json_decode(file_get_contents($url));

if (!$instructions)
  die();

if ($instructions->action == 'get') {
  $sendTo = $instructions->target ?: $url;
  foreach ($instructions->urls as $url) {
    $content = file_get_contents($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sendTo);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'X-SOURCE-URL: ' . $url,
      'X-CLIENT-ID: ' . $clientId
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    usleep($instructions->sleepDuration);
  }
}

