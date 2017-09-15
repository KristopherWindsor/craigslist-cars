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

$csv = loadCSV($csvInputFile);

// make series

function makeSeries($bunchOfRows) {
    $dataPoints = [];
    foreach ($bunchOfRows as $i)
        $dataPoints[] = '{x: ' . intval($i['My Score (miles + age)']) .
            ', y: ' .       intval($i['Price']) .
            ', postTitle: "' .     $i['Post Title'] . '"' .
            ', carModel: "' .      $i['Car Model'] . '"' .
            ', modelYear: "' .     $i['Model Year'] . '"' .
            ', vehicleTitle: "' .  $i['Vehicle Title'] . '"' .
            ', transmission: "' .  $i['Transmission'] . '"' .
            ', mileage: ' . intval($i['Mileage'] / 1000) .
            ', expectedPrice: "' . number_format($i['Expected Price']) . '"' .
            ', firstImage: "' .    $i['First Image'] . '"' .
            ', link: "' .          $i['Link'] . '"' .
            '}';

    return implode(",\n", $dataPoints);
}

$series = makeSeries($csv);

// render template
$template = file_get_contents(__DIR__ . '/template.html');
echo str_replace('DATA_GOES_HERE', $series, $template);
