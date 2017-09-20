<?php

require_once __DIR__ . '/../metadata/CarModels.php';

class HtmlParser
{
    private $html;
    private $carModels;

    public function __construct($filename, CarModels $carModels)
    {
        $this->html = file_get_contents($filename);
        $this->carModels = $carModels;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getMakeAndModel()
    {
        $scores = [];
        $postTitle = $this->getPostTitle();

        foreach ($this->carModels->getAll() as list($make, $model)) {
            $score = 0;

            // Search make + model (consecutive words)
            if (stripos($postTitle, "$make $model") !== false)
                $score += 6 * strlen($make . $model);
            elseif (stripos($this->html, "$make $model") !== false)
                $score += 5 * strlen($make . $model);

            // Search make + model (non-consecutive)
            if (stripos($postTitle, $make) !== false && stripos($postTitle, $model) !== false)
                $score += 4 * strlen($make . $model);
            elseif (stripos($this->html, $make) !== false && stripos($this->html, $model) !== false)
                $score += 3 * strlen($make . $model);

            // Search model
            if (stripos($postTitle, $model) !== false)
                $score += 2 * strlen($model);
            elseif (stripos($this->html, $model) !== false)
                $score += 1 * strlen($model);

            $scores["$make:$model"] = $score;
        }

        arsort($scores, SORT_NUMERIC);
        foreach ($scores as $makeModel => $_)
            return explode(':', $makeModel);
    }

    public function getCraigslistLocation()
    {
        return $this->between('<link rel="canonical" href="https://', '.craigslist');
    }

    public function getPostTitle()
    {
        return html_entity_decode($this->between('<span id="titletextonly">', '</span>'));
    }

    private function between($startText, $endText) {
        $a = strpos($this->html, $startText) + strlen($startText);
        if ($a <= strlen($startText)) return '';

        $b = strpos($this->html, $endText, $a + 2);
        if ($b <= 0) return '';

        return substr($this->html, $a, $b - $a);
    }
}
