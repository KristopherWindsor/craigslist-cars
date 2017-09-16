"Location","Post Title","Car Make","Car Model","Model Size","Model Year","Vehicle Title","Transmission","Mileage","My Score (miles + age)","Price","Expected Price","Price-Expected","Link","File Link","First Image","Greylist"
<?php

require_once __DIR__ . '/HtmlParser.php';
require_once __DIR__ . '/../metadata/CarModels.php';

$path = $argv[1];

// greylist.txt has Craigslist IDs or substrings of the filenames to greylist
$greylist = @array_filter(explode("\n", file_get_contents(__DIR__ . '/greylist.txt') ?: ''));
function isGreyListed($filename, $greylist) {
    foreach ($greylist as $item)
        if (strpos($filename, $item) !== false)
            return true;
    return false;
}

$dir = new DirectoryIterator(__DIR__ . '/' . $path);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && $fileinfo->getFilename() != '.DS_Store') {
        go(
            __DIR__ . '/' . $path . '/' . $fileinfo->getFilename(),
            isGreyListed($fileinfo->getFilename(), $greylist)
        );
    }
}

function go($fileName, $isGreyListed) {
  $fields = [];
  $z = file_get_contents($fileName);

  $carModels = new CarModels();
  $htmlParser = new HtmlParser($fileName, $carModels);

  // Location
  $fields[] = between($z, '<a href="/">', '</a>');

  // Post Title
  $postTitle = $fields[] = html_entity_decode(between($z, '<span id="titletextonly">', '</span>'));

  // Car Make
  list($carMake, $carModel) = $htmlParser->getMakeAndModel();
  $fields[] = $carMake;

  // Car Model
  $fields[] = $carModel;

  // Model Size
  $fields[] = $carModels->getInfo($carMake, $carModel)['size'];

  // Model Year
  $year = (int) between($z, '<span><b>', '</b>');
  $fields[] = $year;

  // Vehicle Title
  $fields[] = between($z, '<span>title status: <b>', '</b>');

  // Transmission
  $fields[] = between($z, '<span>transmission: <b>', '</b>');

  // Mileage
  $mileage = between($z, '<span>odometer: <b>', '</b>');
  if (!$mileage && strpos($z, 'k miles') > 0) {
    $mileage = (int) filter_var(substr($z, strpos($z, 'k miles') - 4, 4), FILTER_SANITIZE_NUMBER_FLOAT);
  }
  if ($mileage < 500 && $year < 2017)
      $mileage *= 1000;
  $fields[] = $mileage ?: '';
  if ($mileage > 500000)
      $isGreyListed = true;

  // My Score
  $myScore = $fields[] = (2018 - $year) * 5000 + $mileage;

  // Price
  $price = between($z, '<span class="price">$', '</span>')
      ?: (int) substr(strstr($z, '$'), 1);
  if ($price <= 1)
      $isGreyListed = true;
  if ($price < 20)
      $price *= 1000; // $5k or $8,000 (comma breaks parsing)
  $fields[] = $price;

  // Expected Price
  // This formula comes from logarithmic trendline based on observed data (of all car models being watched)
  // It may change as we get more data, watch more car models, and filter out bad data (i.e. non-running cars)
  $expectedPrice = max(100, (int) (66854 - 5142 * log($myScore)));
  $fields[] = $expectedPrice;

  // Price-Expected
  $fields[] = $price - $expectedPrice;

  // Link
  $fields[] = between($z, '<link rel="canonical" href="', '">');

  // Filename
  $fields[] = 'file://' . $fileName;

  // First Image
  $firstImage = between($z, 'class="slide first visible"><img src="', '"') ?:
      'https://sfbay.craigslist.org/favicon.ico';
  $fields[] = $firstImage;

  // Greylist
  $fields[] = $isGreyListed ? 'greylisted' : 'ok';

  foreach ($fields as $index => $field)
      echo ($index ? ',' : '') . '"' . addslashes($field) . '"';
  echo "\n";
}

function between($string, $startText, $endText) {
  $a = strpos($string, $startText) + strlen($startText);
  if ($a <= strlen($startText)) return '';

  $b = strpos($string, $endText, $a + 2);
  if ($b <= 0) return '';

  return substr($string, $a, $b - $a);
}
