<?php

require_once __DIR__ . '/../metadata/CarModels.php';

class HtmlParser
{
    const ALT_CAR_MAKES = [
        'Chevrolet' => ['Chevy', 'Chev'],
        'Volkswagen' => ['VW'],
    ];

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

    public function getPostTitle()
    {
        return html_entity_decode($this->between('<span id="titletextonly">', '</span>'));
    }

    public function getPostBody()
    {
        return $this->between('<section id="postingbody">', '</section>');
    }

    public function getMakeAndModel()
    {
        $postTitle = $this->getPostTitle();
        $makeModelField = $this->getRawMakeModel();
        $postBody = str_replace('QR Code Link to This Post', '', strip_tags($this->getPostBody()));
        $allHtml = $postTitle . ' ' . $makeModelField . $postBody;

        $bestScore = 0;
        $bestMake = $bestModel = '';
        foreach ($this->carModels->getAll() as list($make, $model)) {
            $score = $this->scoreMakeModel($make, $model, $postTitle, $makeModelField, $postBody, $allHtml);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMake = $make;
                $bestModel = $model;
            }
        }

        return [$bestMake, $bestModel];
    }

    private function getRawMakeModel()
    {
        return $this->between('<span><b>', '</b>');
    }

    private function scoreMakeModel($make, $model, $postTitle, $makeModelField, $postBody, $allHtml)
    {
        $isModelGeneric = strlen(preg_replace('/[0-9]+/', '', $model)) <= 3;

        $makes = array_key_exists($make, self::ALT_CAR_MAKES) ? self::ALT_CAR_MAKES[$make] : [];
        $makes[] = $make;

        // F-250 -> F 250 or F250
        $models = [$model];
        if (strpos($model, '-')) {
            $models[] = str_replace('-', ' ', $model);
            $models[] = str_replace('-', '', $model);
        }
        if (strpos($model, ' ')) {
            $models[] = str_replace(' ', '', $model);
        }

        $scoreTitle = $scoreField = $scoreBody = $scoreHtml = 0;

        foreach ($makes as $make)
            foreach ($models as $model) {
                // Score title
                $tmp = stripos($postTitle, "$make $model");
                if ($tmp !== false) {
                    $scoreTitle = max($scoreTitle, 1 - .002 * $tmp + .02 * strlen($make . $model));
                } elseif (!$isModelGeneric) {
                    $tmp = stripos($postTitle, $make);
                    $tmp2 = stripos($postTitle, $model);
                    if ($tmp !== false && $tmp2 !== false) {
                        $scoreTitle = max(
                            $scoreTitle,
                            .5 - .001 * ($tmp + $tmp2) + .02 * min(strlen($make), strlen($model))
                        );
                    } elseif ($tmp2 !== false) {
                        $scoreTitle = .2;
                    }
                }

                // Score make/model field
                $tmp = stripos($makeModelField, "$make $model");
                if ($tmp !== false) {
                    $scoreField = max($scoreField, 1 - .002 * $tmp + .02 * strlen($make . $model));
                } elseif (!$isModelGeneric) {
                    $tmp = stripos($makeModelField, $make);
                    $tmp2 = stripos($makeModelField, $model);
                    if ($tmp !== false && $tmp2 !== false) {
                        $scoreField = max(
                            $scoreField,
                            .5 - .001 * ($tmp + $tmp2) + .02 * min(strlen($make), strlen($model))
                        );
                    } elseif ($tmp2 !== false) {
                        $scoreField = .2;
                    }
                }

                // Score body
                $tmp = stripos($postBody, "$make $model");
                if ($tmp !== false) {
                    $scoreBody = max($scoreBody, 1 - .0002 * $tmp + .02 * strlen($make . $model));
                } elseif (!$isModelGeneric) {
                    $tmp = stripos($postBody, $make);
                    $tmp2 = stripos($postBody, $model);
                    if ($tmp !== false && $tmp2 !== false) {
                        $scoreBody = max(
                            $scoreBody,
                            .5 - .0001 * ($tmp + $tmp2) + .02 * min(strlen($make), strlen($model))
                        );
                    }
                }

                // Score whole HTML
                if (!$isModelGeneric) {
                    $tmp = stripos($allHtml, $make);
                    $tmp2 = stripos($allHtml, $model);
                    if ($tmp !== false && $tmp2 !== false) {
                        $scoreHtml = max(
                            $scoreHtml,
                            .5 - .0001 * ($tmp + $tmp2) + .02 * min(strlen($make), strlen($model))
                        );
                    }
                }
            }

        return ($isModelGeneric ? .7 : 1) *
            (500 * $scoreField + 400 * $scoreTitle + 150 * $scoreBody + 100 * $scoreHtml);
    }

    public function getCraigslistLocation()
    {
        return $this->between('<link rel="canonical" href="https://', '.craigslist');
    }

    private function between($startText, $endText) {
        $a = strpos($this->html, $startText) + strlen($startText);
        if ($a <= strlen($startText)) return '';

        $b = strpos($this->html, $endText, $a + 2);
        if ($b <= 0) return '';

        return substr($this->html, $a, $b - $a);
    }
}
