<?php

namespace App\Console\Commands;

use App\Team;
use Goutte\Client;
use Illuminate\Console\Command;

class CrawlTeams extends Command
{
    protected $signature = 'crawl:nfl:teams';

    public function handle(Client $client)
    {
        $crawler = $client->request('GET', 'https://www.pro-football-reference.com/teams/');
        
        $crawler->filter('#teams_active > tbody')->filter('tr')->each(function ($tr, $i) {
            $yearMax = $tr->filter('td[data-stat="year_max"]')->text();

            if ($yearMax >= 2014) {
                $team = Team::firstOrNew(['full_name' => $tr->filter('th')->first()->text()]);
                $team->name = collect(explode(' ', $tr->filter('th')->first()->text()))->last();
                $team->save();
            }
        });

        dd(Team::get()->toArray());
    }

}
