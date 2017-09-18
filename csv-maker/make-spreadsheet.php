"Location","Date Posted","Post Title","Car Make","Car Model","Model Size","Model Year","Vehicle Title","Transmission","Mileage","Price","Link","File Link","First Image","Greylist"
<?php

require_once __DIR__ . '/HtmlParser.php';
require_once __DIR__ . '/../metadata/CarModels.php';

$pagesPath = dirname(__DIR__) . '/pages';

$carModels = new CarModels();

// greylist.txt has Craigslist IDs or substrings of the filenames to greylist
$greylist = @array_filter(explode("\n", file_get_contents(__DIR__ . '/greylist.txt') ?: ''));
function isGreyListed($filename, $greylist) {
    foreach ($greylist as $item)
        if (strpos($filename, $item) !== false)
            return true;
    return false;
}

$dir = new DirectoryIterator($pagesPath);
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot() && $fileinfo->getFilename() != '.DS_Store') {
        go(
            $pagesPath . '/' . $fileinfo->getFilename(),
            isGreyListed($fileinfo->getFilename(), $greylist),
            $carModels
        );
    }
}

function go($fileName, $isGreyListed, $carModels) {
  $fields = [];

  $htmlParser = new HtmlParser($fileName, $carModels);
  $z = $htmlParser->getHtml();

  // Location
  $fields[] = between($z, '<a href="/">', '</a>');

  // Date Posted
  $fields[] = between($z, '<time class="date timeago" datetime="', '">');

  // Post Title
  $postTitle = $fields[] = html_entity_decode(between($z, '<span id="titletextonly">', '</span>'));

  // Car Make
  list($carMake, $carModel) = $htmlParser->getMakeAndModel();
  $fields[] = $carMake;

  // Car Model
  $fields[] = $carModel;

  // Model Size
  $modelSize = between($z, '<span>size: <b>', '</b>') ?: 'unknown';
  $fields[] = $modelSize;

  // Model Year
  $year = (int) between($z, '<span><b>', '</b>');
  $fields[] = $year;

  // Vehicle Title
  $fields[] = between($z, '<span>title status: <b>', '</b>');

  // Transmission
  $fields[] = between($z, '<span>transmission: <b>', '</b>');

  // Mileage
  $mileage = between($z, '<span>odometer: <b>', '</b>');
  if ($mileage <= 0 && strpos($z, 'k miles') > 0) {
    $mileage = abs((int) filter_var(substr($z, strpos($z, 'k miles') - 4, 4), FILTER_SANITIZE_NUMBER_FLOAT));
  }
  if ($mileage < 500 && $year < 2016)
      $mileage *= 1000;
  $fields[] = $mileage ?: '';
  if ($mileage > 500000)
      $isGreyListed = true;

  // Price
  $price = between($z, '<span class="price">$', '</span>')
      ?: (int) substr(strstr($z, '$'), 1);
  if ($price <= 1)
      $isGreyListed = true;
  if ($price < 30)
      $price *= 1000; // $5k or $8,000 (comma breaks parsing)
  $fields[] = $price;

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

  $b = strpos($string, $endText, $a);
  if ($b <= 0) return '';

  return substr($string, $a, $b - $a);
}

