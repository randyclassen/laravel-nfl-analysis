<?php

namespace App\Services;

use Spatie\Crawler\CrawlObserver;

class Crawler extends CrawlObserver
{
    private $pages =[];


    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    )
    {

        $path = $url->getPath();
        $doc = new DOMDocument();
        @$doc->loadHTML($response->getBody());
        $title = $doc->getElementsByTagName("title")[0]->nodeValue;

        $this->pages[] = [
            'path'=>$path,
            'title'=> $title
        ];
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    )
    {
        echo 'failed';
    }

}