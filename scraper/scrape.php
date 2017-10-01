<?php

require_once dirname(__DIR__) . '/metadata/CraigslistSites.php';

$pagesDir = dirname(__DIR__) . '/pages';

exec('ps aux | grep scrape.php | grep -v grep | grep -v craigslist-cars', $output);
if (count($output) > 1)
    die();

$sites = new CraigslistSites();

foreach ($sites->getAllSiteUrls() as $site) {
    $command = 'php "' . __DIR__ . '/scrape-rss.php" "' .
        $site . 'search/cto?format=rss&sort=date" | ' .
        'php "' . __DIR__ . '/scrape-urls.php" "' . $pagesDir . '"';
    exec($command);
}
