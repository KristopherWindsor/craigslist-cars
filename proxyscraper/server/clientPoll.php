<?php

$urlsToScrape = [];

foreach (new DirectoryIterator(__DIR__ . '/data') as $fileInfo) {
    if ($fileInfo->isDot()) continue;
    $filename = $fileInfo->getFilename();

    // A client is already busy on the URL
    if (strpos($fileInfo->getFilename(), 'inprogress') !== false) {
        // Client is actual busy on URL
        if ($fileInfo->getMTime() > time() - 120)
            continue;
        touch(filename);
        // Client failed to process the URL
    } else {
        touch($filename);
        // Resolve potential race condition issue
        if (!rename($filename, $filename . 'inprogress'))
            continue;
        $filename .= 'inprogress';
    }

    $info = explode("\n", file_get_contents($filename));
    $urlsToScrape[] = $info[0];

    if (count($urlsToScrape) >= 50) break;
}

header('Content-Type: application/json');
echo json_encode([
    'action'        => 'get',
    'target'        => '',
    'urls'          => $urlsToScrape,
    'sleepDuration' => 1000 * 1000,
]);

