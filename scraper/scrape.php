<?php

require_once dirname(__DIR__) . '/metadata/CarModels.php';
require_once dirname(__DIR__) . '/metadata/CraigslistSites.php';
require_once __DIR__ . '/SourceStats.php';

$pagesDir = dirname(__DIR__) . '/pages';

exec('ps aux | grep scrape.php | grep -v grep | grep -v craigslist-cars', $output);
if (count($output) > 1)
    die();

$hour = date('G');
$sites = new CraigslistSites();
$models = new CarModels();

$bestStat = null;
foreach ($sites->getAllSiteUrls() as $site) {
    foreach ($models->getAllMakes() as $make) {
        $url = $site . 'search/cto?query=' . urlencode($make) . '&format=rss&sort=date';
        $stat = SourceStats::loadForSource($url);
        if (!$bestStat || $stat->getScore($hour) > $bestStat->getScore($hour))
            $bestStat = $stat;
    }
}

$debugScore = $bestStat->getScore($hour, $debugInfo);
echo $bestStat->url . "\tscored\t" . $debugScore . "\t" . json_encode($debugInfo) . "\n";

if ($bestStat->getScore($hour) > 1) {
    $command = 'php "' . __DIR__ . '/scrape-rss.php" "' . $bestStat->url . '" | ' .
        'php "' . __DIR__ . '/scrape-urls.php" "' . $pagesDir . '"';
    // echo $command . "\n";
    exec($command);
}
