<?php

require_once __DIR__ . '/../metadata/CarModels.php';

$pagesDir = dirname(__DIR__) . '/pages';

$scapeSites = [
    'https://bakersfield.craigslist.org/',
    'https://chico.craigslist.org/',
    'https://fresno.craigslist.org/',
    'https://goldcountry.craigslist.org/',
    'https://hanford.craigslist.org/',
    'https://humboldt.craigslist.org/',
    'https://imperial.craigslist.org/',
    'https://inlandempire.craigslist.org/',
    'https://losangeles.craigslist.org/',
    'https://mendocino.craigslist.org/',
    'https://merced.craigslist.org/',
    'https://modesto.craigslist.org/',
    'https://monterey.craigslist.org/',
    'https://orangecounty.craigslist.org/',
    'https://palmsprings.craigslist.org/',
    'https://redding.craigslist.org/',
    'https://reno.craigslist.org/',
    'https://sacramento.craigslist.org/',
    'https://sandiego.craigslist.org/',
    'https://slo.craigslist.org/',
    'https://santabarbara.craigslist.org/',
    'https://santamaria.craigslist.org/',
    'https://sfbay.craigslist.org/',
    'https://siskiyou.craigslist.org/',
    'https://stockton.craigslist.org/',
    'https://susanville.craigslist.org/',
    'https://ventura.craigslist.org/',
    'https://visalia.craigslist.org/',
    'https://yubasutter.craigslist.org/',
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
