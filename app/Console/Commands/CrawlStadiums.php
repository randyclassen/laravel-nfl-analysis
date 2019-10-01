<?php

namespace App\Console\Commands;

use App\Team;
use App\Stadium;
use Goutte\Client;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class CrawlStadiums extends Command
{
    protected $signature = 'crawl:nfl:stadiums';

    public function handle(Client $client)
    {
        $crawler = $client->request('GET', 'https://www.pro-football-reference.com/stadiums/');
        
        $crawler->filter('#stadiums > tbody')->filter('tr')->each(function ($tr, $i) use ($client) {
            if ($i <= 60) {
                $recentlyUsedStadium = $tr->filter('td[data-stat="year_max"]')->text() >= 2014;
                $teamsArray = explode(',', trim($tr->filter('td[data-stat="teams"]')->text()));
                $teamsArray = array_map('trim', $teamsArray);

                if ($teamsArray) {
                    $teams = Team::whereIn('full_name', $teamsArray)->get();
                }

                if ($teamsArray && $teams->isNotEmpty() && $recentlyUsedStadium) {
                    $stadiumCrawler = $client->request('GET', $tr->filter('th')->filter('a[href^="/stadiums/"]')->attr('href'));
                    
                    $stadium = Stadium::firstOrNew([
                        'name' => trim($tr->filter('th')->text()),
                    ], [
                        'location' => $stadiumCrawler->filter('#meta > div:nth-child(1) > p:nth-child(2)')->text(),
                    ]);
                
                    list($latitude, $longitude) = \App\Services\Distances::getCoordinates($stadium->location);
                    
                    $stadium->fill([
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ]);

                    $stadium->save();

                    foreach($teams as $team) {
                        $team->stadium_id = $stadium->id;
                        $team->save();
                    }
                }
            }

            usleep(250000);
        });

        dd(Team::get()->toArray(), Stadium::get()->toArray());
    }

}
