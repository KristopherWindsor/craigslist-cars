<?php

$DISABLED = false;

class Datastore {
	public $data, $filename;

	public function __construct() {
		$this->filename = __DIR__ . '/data/datastore.json';
		$content = @file_get_contents($this->filename);
		$this->data = @json_decode($content, true) ?: [
			'pageQueue' => [],
			'rssSources' => $this->getInitialRssSources(),
		];
	}

	public function getInitialRssSources() {
		$sources = [
			"https://redding.craigslist.org/search/cta?nearbyArea=1&nearbyArea=12&nearbyArea=187&nearbyArea=189&nearbyArea=216&nearbyArea=233&nearbyArea=373&nearbyArea=454&nearbyArea=456&nearbyArea=459&nearbyArea=675&nearbyArea=707&nearbyArea=708&nearbyArea=92&nearbyArea=94&nearbyArea=96&nearbyArea=97&searchNearby=2&sort=date&format=rss&s=",
			"https://bakersfield.craigslist.org/search/cta?nearbyArea=102&nearbyArea=103&nearbyArea=104&nearbyArea=191&nearbyArea=208&nearbyArea=209&nearbyArea=26&nearbyArea=285&nearbyArea=346&nearbyArea=43&nearbyArea=62&nearbyArea=7&nearbyArea=709&nearbyArea=710&nearbyArea=8&searchNearby=2&sort=date&format=rss&s=",
			/*
			"https://saltlakecity.craigslist.org/search/cta?nearbyArea=292&nearbyArea=351&nearbyArea=448&nearbyArea=469&nearbyArea=652&searchNearby=2&sort=date&format=rss&s=",
			"https://stgeorge.craigslist.org/search/cta?nearbyArea=565&searchNearby=2&sort=date&format=rss&s=",
			"https://yakima.craigslist.org/search/cta?nearbyArea=2&nearbyArea=217&nearbyArea=232&nearbyArea=321&nearbyArea=322&nearbyArea=324&nearbyArea=325&nearbyArea=350&nearbyArea=368&nearbyArea=461&nearbyArea=466&nearbyArea=655&nearbyArea=9&nearbyArea=95&searchNearby=2&sort=date&format=rss&s=",
			"https://phoenix.craigslist.org/search/cta?nearbyArea=244&nearbyArea=370&nearbyArea=419&nearbyArea=455&nearbyArea=468&nearbyArea=57&nearbyArea=651&searchNearby=2&sort=date&format=rss&s=",
			"https://butte.craigslist.org/search/cta?nearbyArea=424&nearbyArea=52&nearbyArea=654&nearbyArea=656&nearbyArea=657&nearbyArea=658&nearbyArea=659&nearbyArea=660&nearbyArea=662&searchNearby=2&sort=date&format=rss&s=",
			"https://cosprings.craigslist.org/search/cta?nearbyArea=13&nearbyArea=197&nearbyArea=218&nearbyArea=287&nearbyArea=288&nearbyArea=315&nearbyArea=319&nearbyArea=320&nearbyArea=568&nearbyArea=669&nearbyArea=687&nearbyArea=713&searchNearby=2&sort=date&format=rss&s=",
			"https://roswell.craigslist.org/search/cta?nearbyArea=132&nearbyArea=267&nearbyArea=268&nearbyArea=269&nearbyArea=334&nearbyArea=50&nearbyArea=653&searchNearby=2&sort=date&format=rss&s=",
			"https://bismarck.craigslist.org/search/cta?nearbyArea=192&nearbyArea=195&nearbyArea=196&nearbyArea=435&nearbyArea=667&nearbyArea=680&nearbyArea=681&nearbyArea=682&searchNearby=2&sort=date&format=rss&s=",
			"https://grandisland.craigslist.org/search/cta?nearbyArea=280&nearbyArea=282&nearbyArea=341&nearbyArea=347&nearbyArea=428&nearbyArea=55&nearbyArea=668&nearbyArea=679&nearbyArea=688&nearbyArea=690&nearbyArea=99&searchNearby=2&sort=date&format=rss&s=",
			"https://sanangelo.craigslist.org/search/cta?nearbyArea=15&nearbyArea=270&nearbyArea=327&nearbyArea=364&nearbyArea=449&nearbyArea=53&nearbyArea=647&nearbyArea=648&searchNearby=2&sort=date&format=rss&s=",
			"https://lawton.craigslist.org/search/cta?nearbyArea=21&nearbyArea=308&nearbyArea=365&nearbyArea=433&nearbyArea=54&nearbyArea=649&nearbyArea=650&nearbyArea=70&searchNearby=2&sort=date&format=rss&s=",
			"https://eauclaire.craigslist.org/search/cta?nearbyArea=165&nearbyArea=19&nearbyArea=241&nearbyArea=243&nearbyArea=255&nearbyArea=262&nearbyArea=316&nearbyArea=362&nearbyArea=363&nearbyArea=369&nearbyArea=421&nearbyArea=458&nearbyArea=47&nearbyArea=552&nearbyArea=553&nearbyArea=571&nearbyArea=631&nearbyArea=663&nearbyArea=664&nearbyArea=665&nearbyArea=692&nearbyArea=693&searchNearby=2&sort=date&format=rss&s=",
			"https://columbiamo.craigslist.org/search/cta?nearbyArea=190&nearbyArea=221&nearbyArea=224&nearbyArea=225&nearbyArea=29&nearbyArea=293&nearbyArea=30&nearbyArea=307&nearbyArea=339&nearbyArea=340&nearbyArea=344&nearbyArea=345&nearbyArea=423&nearbyArea=425&nearbyArea=445&nearbyArea=566&nearbyArea=567&nearbyArea=569&nearbyArea=689&nearbyArea=691&nearbyArea=694&nearbyArea=695&nearbyArea=696&nearbyArea=697&nearbyArea=698&nearbyArea=699&nearbyArea=98&searchNearby=2&sort=date&format=rss&s=",
			"https://victoriatx.craigslist.org/search/cta?nearbyArea=23&nearbyArea=263&nearbyArea=264&nearbyArea=265&nearbyArea=266&nearbyArea=271&nearbyArea=326&nearbyArea=470&nearbyArea=645&searchNearby=2&sort=date&format=rss&s=",
			"https://monroe.craigslist.org/search/cta?nearbyArea=100&nearbyArea=134&nearbyArea=199&nearbyArea=206&nearbyArea=230&nearbyArea=283&nearbyArea=284&nearbyArea=31&nearbyArea=358&nearbyArea=359&nearbyArea=374&nearbyArea=375&nearbyArea=46&nearbyArea=641&nearbyArea=642&nearbyArea=643&nearbyArea=644&searchNearby=2&sort=date&format=rss&s=",
			"https://muskegon.craigslist.org/search/cta?nearbyArea=11&nearbyArea=129&nearbyArea=172&nearbyArea=212&nearbyArea=22&nearbyArea=223&nearbyArea=226&nearbyArea=228&nearbyArea=259&nearbyArea=260&nearbyArea=261&nearbyArea=309&nearbyArea=426&nearbyArea=434&nearbyArea=555&nearbyArea=563&nearbyArea=572&nearbyArea=627&nearbyArea=628&nearbyArea=630&searchNearby=2&sort=date&format=rss&s=",
			"https://owensboro.craigslist.org/search/cta?nearbyArea=133&nearbyArea=202&nearbyArea=220&nearbyArea=227&nearbyArea=229&nearbyArea=32&nearbyArea=342&nearbyArea=348&nearbyArea=360&nearbyArea=361&nearbyArea=377&nearbyArea=45&nearbyArea=465&nearbyArea=558&nearbyArea=58&nearbyArea=670&nearbyArea=671&nearbyArea=672&nearbyArea=674&searchNearby=2&sort=date&format=rss&s=",
			"https://columbusga.craigslist.org/search/cta?nearbyArea=127&nearbyArea=14&nearbyArea=186&nearbyArea=200&nearbyArea=203&nearbyArea=207&nearbyArea=231&nearbyArea=256&nearbyArea=257&nearbyArea=258&nearbyArea=371&nearbyArea=372&nearbyArea=467&nearbyArea=559&nearbyArea=560&nearbyArea=562&nearbyArea=635&nearbyArea=636&nearbyArea=637&nearbyArea=640&searchNearby=2&sort=date&format=rss&s=",
			"https://zanesville.craigslist.org/search/cta?nearbyArea=131&nearbyArea=194&nearbyArea=204&nearbyArea=251&nearbyArea=252&nearbyArea=27&nearbyArea=33&nearbyArea=35&nearbyArea=42&nearbyArea=436&nearbyArea=437&nearbyArea=438&nearbyArea=439&nearbyArea=440&nearbyArea=441&nearbyArea=442&nearbyArea=443&nearbyArea=573&nearbyArea=632&nearbyArea=700&nearbyArea=701&nearbyArea=703&nearbyArea=706&searchNearby=2&sort=date&format=rss&s=",
			"https://fayetteville.craigslist.org/search/cta?nearbyArea=101&nearbyArea=128&nearbyArea=171&nearbyArea=253&nearbyArea=254&nearbyArea=272&nearbyArea=274&nearbyArea=289&nearbyArea=290&nearbyArea=291&nearbyArea=323&nearbyArea=335&nearbyArea=336&nearbyArea=353&nearbyArea=36&nearbyArea=366&nearbyArea=367&nearbyArea=41&nearbyArea=446&nearbyArea=447&nearbyArea=457&nearbyArea=462&nearbyArea=464&nearbyArea=48&nearbyArea=60&nearbyArea=61&nearbyArea=634&nearbyArea=712&searchNearby=2&sort=date&format=rss&s=",
			"https://fairbanks.craigslist.org/search/cta?sort=date&format=rss&s=",
			"https://rochester.craigslist.org/search/cta?nearbyArea=130&nearbyArea=201&nearbyArea=247&nearbyArea=248&nearbyArea=275&nearbyArea=337&nearbyArea=40&nearbyArea=452&nearbyArea=453&nearbyArea=683&nearbyArea=684&nearbyArea=685&nearbyArea=704&searchNearby=2&sort=date&format=rss&s=",
			"https://orlando.craigslist.org/search/cta?nearbyArea=125&nearbyArea=20&nearbyArea=205&nearbyArea=219&nearbyArea=237&nearbyArea=238&nearbyArea=330&nearbyArea=331&nearbyArea=332&nearbyArea=333&nearbyArea=37&nearbyArea=376&nearbyArea=427&nearbyArea=557&nearbyArea=570&nearbyArea=638&nearbyArea=639&nearbyArea=80&searchNearby=2&sort=date&format=rss&s=",
			"https://baltimore.craigslist.org/search/cta?nearbyArea=10&nearbyArea=166&nearbyArea=167&nearbyArea=17&nearbyArea=170&nearbyArea=193&nearbyArea=276&nearbyArea=277&nearbyArea=278&nearbyArea=279&nearbyArea=286&nearbyArea=328&nearbyArea=329&nearbyArea=349&nearbyArea=355&nearbyArea=356&nearbyArea=357&nearbyArea=444&nearbyArea=460&nearbyArea=463&nearbyArea=556&nearbyArea=561&nearbyArea=633&nearbyArea=705&nearbyArea=711&searchNearby=2&sort=date&format=rss&s=",
			"https://worcester.craigslist.org/search/cta?nearbyArea=168&nearbyArea=169&nearbyArea=173&nearbyArea=198&nearbyArea=239&nearbyArea=249&nearbyArea=250&nearbyArea=281&nearbyArea=3&nearbyArea=338&nearbyArea=354&nearbyArea=378&nearbyArea=38&nearbyArea=4&nearbyArea=44&nearbyArea=451&nearbyArea=59&nearbyArea=686&nearbyArea=93&searchNearby=2&sort=date&format=rss&s=",
			*/
		];

		$result = [];
		foreach ($sources as $url)
			$result[$url] = [
				'polledDate' => null,
				'newestItem' => null,
			];
		return $result;
	}

	public function save() {
		$tmpname = uniqid();
		file_put_contents($this->filename . $tmpname, json_encode($this->data));
		rename($this->filename . $tmpname, $this->filename);
	}
}

class LogRequest {
	public $requestHeaders, $requestBody;

	public function __construct($requestHeaders, $requestBody) {
		$this->requestHeaders = $requestHeaders;
		$this->requestBody    = $requestBody;
	}

	public function logWithResponse($code, $response) {
		file_put_contents(__DIR__ . '/data/requestresponse.log', json_encode([
			'reqHeaders' => $this->requestHeaders,
			'reqBody'    => strlen($this->requestBody) > 5000 ? '<snip ' . strlen($this->requestBody) . ' chars>' : $this->requestBody,
			'resCode'    => $code,
			'resBody'    => @json_decode($response) ?: $response,
		]) . "\n", FILE_APPEND);
	}
}

$requestBody    = file_get_contents('php://input');
$requestHeaders = getallheaders();
$contentType    = $requestHeaders['Content-Type'] ?? '';
$datastore      = new Datastore();
$logRequest     = new LogRequest($requestHeaders, $requestBody);

if ($DISABLED) {
	$response = provideHibernateResponse();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$response = provideInstructions($requestBody, $requestHeaders, $datastore);
} elseif ($contentType == 'application/json') {
	$response = acceptRss($requestBody, $requestHeaders, $datastore);
} elseif ($contentType == 'text/html') {
	$response = acceptPage($requestBody, $requestHeaders, $datastore);
} else {
	http_response_code(400);
	$response = '';
}

echo $response;
$logRequest->logWithResponse(http_response_code(), $response);

function provideHibernateResponse() {
	return json_encode(['action' => 'hibernate', 'seconds' => 300]);
}

function provideInstructions($requestBody, $requestHeaders, $datastore) {
	header('Content-Type: application/json');

	// Pending pages have priority
	$queue = array_filter($datastore->data['pageQueue'], function ($item) {
		if (!$item)
			return true;
		$date = new \DateTime($item);
		return $date < new \DateTime('2 minutes ago');
	});
	if ($queue) {
		$urls = [];
		foreach ($queue as $pageUrl => $_) {
			$datastore->data['pageQueue'][$pageUrl] = date(DateTime::ATOM);
			$urls[] = $pageUrl;
			if (count($urls) > 30)
				break;
		}
		$datastore->save();
		return json_encode([
			'action'                => 'getPages',
			'sleepDurationMicrosec' => 1000 * 1000,
			'urls'                  => $urls,
		]);
	}

	// Which RSS source has been polled the least recently?
	uasort($datastore->data['rssSources'], function ($a, $b) {
		$a = $a['polledDate']; if (!$a) return -1;
		$b = $b['polledDate']; if (!$b) return 1;
		$a = new \DateTime($a);
		$b = new \DateTime($b);

	    if ($a == $b) return 0;
	    return ($a > $b) ? -1 : 1;
	});
	foreach ($datastore->data['rssSources'] as $rssSource => $data) {
		if (!$data['polledDate'] || new \DateTime($data['polledDate']) < new \DateTime('5 minutes ago')) {
			$datastore->data['rssSources'][$rssSource]['polledDate'] = date(\DateTime::ATOM);
			$datastore->save();
			return json_encode([
				'action'    => 'getRSS',
				'url'       => $rssSource,
				'loopUntil' => $data['newestItem'] ?: (new \DateTime('7 days ago'))->format(\DateTime::ATOM),
			]);
		}
	}

	// Nothing to do --> hibernate
	return provideHibernateResponse();
}

function acceptPage($requestBody, $requestHeaders, $datastore) {
	$sourceUrl = $requestHeaders['X-SOURCE-URL'] ?? null;
	if (!$sourceUrl || empty($datastore->data['pageQueue'][$sourceUrl])) {
		http_response_code(400);
		return '';
	}

	// Take page out of pending queue
	unset($datastore->data['pageQueue'][$sourceUrl]);
	$datastore->save();

	// Save page
	file_put_contents(__DIR__ . '/pages/' . urlencode($sourceUrl), $requestBody);
	http_response_code(201);
	return 'created';
}

function acceptRss($requestBody, $requestHeaders, $datastore) {
	$rssSource = $requestHeaders['X-SOURCE-RSS'] ?? null;
	$pages = @json_decode($requestBody, true);
	if (!$pages || !$rssSource) {
		http_response_code(400);
		return '';
	}

	foreach ($pages as list($url, $dateUpdated)) {
		// Put new page in the queue
		if (empty($datastore->data['pageQueue'][$url]))
			$datastore->data['pageQueue'][$url] = null;
		// Update newestItem time for the RSS source
		if (!$datastore->data['rssSources'][$rssSource]['newestItem'] || new \DateTime($dateUpdated) > new \DateTime($datastore->data['rssSources'][$rssSource]['newestItem']))
			$datastore->data['rssSources'][$rssSource]['newestItem'] = (new \DateTime($dateUpdated))->format(\DateTime::ATOM);
	}
	$datastore->save();

	return 'ok';
}
