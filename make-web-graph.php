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

// split

function groupRows($splitFields, $csv) {
    $groups = [];
    foreach ($csv as $row) {
        $groupKey = '';
        foreach ($splitFields as $i)
            $groupKey .= $row[$i] . '-';
        $groups[trim($groupKey, '-')][] = $row;
    }
    return $groups;
}

$splitFields = ['Car Model', /* 'Vehicle Title', 'Transmission', 'Location', */];
$groups = groupRows($splitFields, $csv);

// make series

function makeSeries($bunchOfRows, $title) {
    static $markerIndex;
    $markerIndex = ($markerIndex + 1) % 4;
    $markerType = ['circle', 'square', 'triangle', 'cross'][$markerIndex];

    $dataPoints = [];
    foreach ($bunchOfRows as $i)
        $dataPoints[] = '{x: ' . intval($i['My Score (miles + age)']) .
            ', y: ' . intval($i['Price']) .
            ', postTitle: "' . $i['Post Title'] . '"' .
            ', vehicleTitle: "' . $i['Vehicle Title'] . '"' .
            ', transmission: "' . $i['Transmission'] . '"' .
            ', mileage: "' . intval($i['Mileage'] / 1000) . 'k"' .
            ', expectedPrice: "' . number_format($i['Expected Price']) . '"' .
            ', firstImage: "' . $i['First Image'] . '"' .
            ', link: "' . $i['Link'] . '"' .
            '}';

    return '

            {        
                type: "scatter",  
                markerType: "' . $markerType . '", 
                toolTipContent: "<span style=\'\\"\'color: {color};\'\\"\'><strong>{postTitle}</strong></span><br><img src=\'\\"\'{firstImage}\'\\"\' style=\'\\"\'max-width: 200px; max-height: 200px;\'\\"\'><br/><strong>${y}</strong> (expected ${expectedPrice}, score = {x})<br><strong>{mileage}</strong> miles, <strong>{vehicleTitle}</strong> title, <strong>{transmission}</strong> transmission",
                name: "' . $title . '",
                showInLegend: true,  
                dataPoints: [
                    ' . implode(',', $dataPoints) . '
                ],
                click: function(e) {
                    window.open(e.dataPoint.link, \'_blank\');
                }
            }
';
}

$series = [];
foreach ($groups as $title => $group)
    $series[] = makeSeries($group, $title);

// render template
$template = file_get_contents(__DIR__ . '/template.html');
echo str_replace('DATA_GOES_HERE', implode(',', $series), $template);
