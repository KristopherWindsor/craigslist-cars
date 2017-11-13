<?php

$CLIENT_VERSION = 1.0;

// hibernation check
$hibernateFilename = __DIR__ . '/hibernate.dat';
$hibernateUntil = @file_get_contents($hibernateFilename);
if ($hibernateUntil && $hibernateUntil > time())
    die();

// determine endpoint to get/post to
$endpointFilename = __DIR__ . '/api.dat';
if (!file_exists($endpointFilename) || filemtime($endpointFilename) < time() - 3600 * 4) {
    $endpoint = file_get_contents('http://windsorportal.com/acerbox.txt');
    if (!$endpoint)
        die();
    file_put_contents($endpointFilename, $endpoint);
} else {
    $endpoint = file_get_contents($endpointFilename);
}

// determine client ID
$idFile = __DIR__ . '/client_id';
if (!file_exists($idFile))
    file_put_contents($idFile, uniqid());
$clientId = file_get_contents($idFile);

// get instructions
$instructions = @json_decode(file_get_contents($endpoint . '?clientVersion=' . $clientVersion));
if (!$instructions) {
    file_put_contents($hibernateFilename, time() + 600);
    die();
}

if ($instructions->action == 'getPages') {
    foreach ($instructions->urls as $url) {
        $content = file_get_contents($url);
        if (!$content)
            break;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/html',
            'X-SOURCE-URL: ' . $url,
            'X-CLIENT-ID: ' . $clientId,
            'X-CLIENT-VERSION: ' . $CLIENT_VERSION,
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $output = curl_exec($ch);
        curl_close($ch);
        if (!$output)
            break;

        usleep($instructions->sleepDurationMicrosec);
    }
} elseif ($instructions->action == 'getRSS') {
    $loopUntil = new \DateTime($instructions->loopUntil);
    $offset = 0;
    $pages = [];
    do {
        $url = $instructions->url . $offset;

        $rssContent = file_get_contents($url);
        if (!$rssContent)
            die();

        $results = new SimpleXMLElement($rss);
        foreach ($results->item as $item) {
            $date = (string) $item->{'dc:date'};
            if (new \DateTime($date) <= $loopUntil)
                break 2;
            $pages[] = [(string) $item->link, $date];
        }

        $offset += 25;
    } while ($offset < 500); // Want to get all results but need to stop at some point

    // Send pages[] back to server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-SOURCE-RSS: ' . $instructions->url,
        'X-CLIENT-ID: ' . $clientId,
        'X-CLIENT-VERSION: ' . $CLIENT_VERSION,
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pages));
    $output = curl_exec($ch);
    curl_close($ch);

} elseif ($instructions->action == 'updateSource') {
    file_put_contents(__FILE__ . '.tmp', $instructions->newSource);
    rename(__FILE__ . '.tmp', __FILE__);
} elseif ($instructions->action == 'hibernate') {
    file_put_contents($hibernateFilename, time() + $instructions->seconds);
} else {
    file_put_contents($hibernateFilename, time() + 600);
}
