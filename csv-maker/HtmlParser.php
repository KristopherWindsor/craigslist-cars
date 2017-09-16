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

    public function getMakeAndModel()
    {
        $scores = [];
        $postTitle = $this->getPostTitle();

        foreach ($this->carModels->getAll() as list($make, $model, $info)) {
            $score = 0;

            // Search make + model (consecutive words)
            if (stripos($postTitle, "$make $model") !== false)
                $score += 6;
            elseif (stripos($this->html, "$make $model") !== false)
                $score += 5;

            // Search make + model (non-consecutive)
            if (stripos($postTitle, $make) !== false && stripos($postTitle, $model) !== false)
                $score += 4;
            elseif (stripos($this->html, $make) !== false && stripos($this->html, $model) !== false)
                $score += 3;

            // Search model
            if (stripos($postTitle, $model) !== false)
                $score += 2;
            elseif (stripos($this->html, $model) !== false)
                $score += 1;

            $scores["$make:$model"] = $score;
        }

        arsort($scores, SORT_NUMERIC);
        foreach ($scores as $makeModel => $_)
            return explode(':', $makeModel);
    }

    public function getPostTitle()
    {
        return html_entity_decode($this->between($z, '<span id="titletextonly">', '</span>'));
    }

    private function between($string, $startText, $endText) {
        $a = strpos($string, $startText) + strlen($startText);
        if ($a <= strlen($startText)) return '';

        $b = strpos($string, $endText, $a + 2);
        if ($b <= 0) return '';

        return substr($string, $a, $b - $a);
    }
}
