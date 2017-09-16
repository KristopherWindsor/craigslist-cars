<?php

require_once __DIR__ . '/../metadata/CarModels.php';

$pagesDir = dirname(__DIR__) . '/pages';

$scapeSites = [
    'https://sfbay.craigslist.org/',
    'https://sacramento.craigslist.org/',
    'https://merced.craigslist.org/',
    'https://redding.craigslist.org/',
];

$models = new CarModels();
$models->onEach(function ($make, $model, $info) use ($pagesDir, $scapeSites) {
    foreach ($scapeSites as $site) {
        $command = 'php "' . __DIR__ . '/scrape-rss.php" "' .
            $site . 'search/cto?auto_make_model=' . $info['makeModelSearchTerm'] . '&format=rss" | ' .
            'php "' . __DIR__ . '/scrape-urls.php" "' . $pagesDir . '"';
        exec($command);
    }
});
