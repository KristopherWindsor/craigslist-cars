<?php

class CraigslistSites
{
    private $data = [];
    private $urlToStateMap = [];

    public function __construct()
    {
        $x = json_decode(file_get_contents(__DIR__ . '/craigslistSites.json'), true);
        $this->data = array_map(
            function ($i) {return array_combine($i, $i);},
            $x
        );

        foreach ($x as $state => $list)
            foreach ($list as $item)
                $this->urlToStateMap[$item] = $state;
    }

    public function getAllStates()
    {
        foreach ($this->data as $state => $list)
            yield $state;
    }

    public function convertShortLocationToUrl($location)
    {
        return "https://$location.craigslist.org/";
    }

    public function convertUrlToShortLocation($url)
    {
        return str_replace(['https://', '.craigslist.org/'], '', $url);
    }

    public function getAllSiteUrls()
    {
        foreach ($this->data as $state => $list)
            foreach ($list as $url)
                yield $url;
    }

    public function getStateForUrl($url)
    {
        return $this->urlToStateMap[$url];
    }
}
