<?php

$csvInputFile = $argv[1];

// load CSV

function loadCSV($filename) {
    $rows   = array_map('str_getcsv', file($filename));
    $header = array_shift($rows);
    $csv    = [];
    foreach($rows as $row) {
        $csv[] = array_combine($header, $row);
    }
    return $csv;
}

$csv = loadCSV(__DIR__ . '/' . $csvInputFile);

// make series

function makeSeries($bunchOfRows) {
    $dataPoints = [];
    foreach ($bunchOfRows as $i)
        $dataPoints[] =
            '{ location: "' .         $i['Location'] . '"' .
            ', postTitle: "' .        $i['Post Title'] . '"' .
            ', carMake: "' .          $i['Car Make'] . '"' .
            ', carModel: "' .         $i['Car Model'] . '"' .
            ', modelSize: "' .        $i['Model Size'] . '"' .
            ', modelYear: ' .         round($i['Model Year']) .
            ', vehicleTitle: "' .     $i['Vehicle Title'] . '"' .
            ', transmission: "' .     $i['Transmission'] . '"' .
            ', mileage: ' .           round($i['Mileage'] / 1000) .
            ', myScore: ' .           round($i['My Score (miles + age)']) .
            ', price: ' .             round($i['Price']) .
            ', expectedPrice: ' .     round($i['Expected Price']) .
            ', priceLessExpected: ' . round($i['Price-Expected']) .
            ', link: "' .             $i['Link'] . '"' .
            ', firstImage: "' .       $i['First Image'] . '"' .
            '}';

    return implode(",\n", $dataPoints);
}

$series = makeSeries($csv);

// render template

exec('php "' . __DIR__ . '/template.php"', $output);
$template = implode("\n", $output);

echo str_replace('DATA_GOES_HERE', $series, $template);
