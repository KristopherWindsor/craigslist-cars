<?php

require_once dirname(__DIR__) . '/metadata/CraigslistSites.php';

$pagesDir = dirname(__DIR__) . '/pages';

exec('ps aux | grep scrape.php | grep -v grep | grep -v craigslist-cars', $output);
if (count($output) > 1)
    die();

$scrapeTerms = [
    //'bmw',
    //'chevrolet',
    //'chevy',
    'ford',
    'honda',
    'hyundai',
    //'mazda',
    //'mini',
    'toyota',
    //'volkswagen',
];

$sites = new CraigslistSites();

foreach ($sites->getAllSiteUrls() as $site) {
    foreach ($scrapeTerms as $term) {
        $command = 'php "' . __DIR__ . '/scrape-rss.php" "' .
            $site . 'search/cto?auto_make_model=' . $term . '&format=rss&sort=date" | ' .
            'php "' . __DIR__ . '/scrape-urls.php" "' . $pagesDir . '"';
        exec($command);
    }
}
