<?php

class SourceStats
{
	public $url;
	public $lastHitTime;
	public $totalResultsFoundPerHour = [];
	public $totalTimeGapPerHour = [];

	private static function getFileName($url)
	{
		return __DIR__ . '/sourcestats/' . preg_replace("/[^A-Za-z0-9 ]/", '', $url) . '.txt';
	}

	public static function loadForSource($url)
	{
		$data = @file_get_contents(self::getFileName($url));

		if (!$data)
			return new SourceStats($url);

		return unserialize($data);
	}

	public function __construct($url)
	{
		$this->url = $url;
	}

	public function recordHit($hour)
	{
		$now = time();
		if ($this->lastHitTime)
			$timeGap = min(3600 * 48, $now - $this->lastHitTime);
		else
			// When we do the first hit, gap is unknown
			// We give a small (30 min) gap to make the score high, so that the source will be prioritized until an accurate score can be calculated
			$timeGap = 1800;

		$this->totalTimeGapPerHour[$hour] += $timeGap;
		$this->lastHitTime = $now;
	}

	public function resultsFound($hour, $resultsFound)
	{
		$this->totalResultsFoundPerHour[$hour] += $resultsFound;
	}

	public function save()
	{
		$data = serialize($this);

		file_put_contents(self::getFileName($this->url), $data);
	}

	public function getScore($hour)
	{
		// If we don't have data for this hour but we do have data for a previous hour, so use the previous hour's data
		while ($hour && empty($this->totalTimeGapPerHour[$hour]))
			$hour--;

		$expectedResultsPerHour = 0;
		if (!empty($this->totalResultsFoundPerHour[$hour]) && !empty($this->totalTimeGapPerHour[$hour])) {
			$expectedResultsPerHour = $this->totalResultsFoundPerHour[$hour] / ($this->totalTimeGapPerHour[$hour] / 3600);
			// We have limited data / low confidence in the expected result.
			// Let's give a high score so that this source is prioritized until an accurate score can be calculated
			if ($this->totalTimeGapPerHour[$hour] < 7200)
				$expectedResultsPerHour += 1;
		} elseif (empty($this->totalTimeGapPerHour[$hour]))
			// We've never attempted the source at this hour, so let's guess we can get X new results
			// Give a high score so that this source is prioritized until an accurate score can be calculated
			$expectedResultsPerHour = 100;

		$hoursSinceLastHit = (time() - $this->lastHitTime) / 3600;

		return ($expectedResultsPerHour + 1) * $hoursSinceLastHit + $bonus;
	}
}
