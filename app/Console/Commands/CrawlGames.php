<?php

namespace App\Console\Commands;

use App\Game;
use App\Team;
use App\Stadium;
use Carbon\Carbon;
use Goutte\Client;
use App\Services\Distances;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CrawlGames extends Command
{
    protected $signature = 'crawl:nfl:games';

    protected $client;
    protected $gameUrls;

    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client =  $client;
        $this->gameUrls = [];
    }

    public function handle()
    {
        $baseUrl = 'https://www.pro-football-reference.com';

        foreach ([2019] as $year) {
            $crawler = $this->client->request('GET', 'https://www.pro-football-reference.com/years/' . $year . '/games.htm');

            $crawler->filter('#games > tbody')->filter('tr')->each(function ($tr, $i) use ($baseUrl) {
                $anchor = $tr->filter('a[href^="/boxscores/"]');

                if ($anchor->count()) {
                    $this->gameUrls[] = $baseUrl . $anchor->attr('href'); 
                }
            });
        }

        foreach ($this->gameUrls as $url) { 
            $this->crawlGame($url);
            usleep(500000);
        }
    }

    protected function crawlGame($url)
    {
        $baseCrawler = $this->client->request('GET', $url);
        $pageHtml = str_replace(['<!--', '-->'], '', $baseCrawler->html());
        $crawler = new Crawler($pageHtml);
        $pageText = $crawler->text();

        $homeTeamName = trim($crawler->filter('a[itemprop="name"]')->first()->text());
        $awayTeamName = trim($crawler->filter('a[itemprop="name"]')->last()->text());

        $homeTeam = Team::with('stadium')->where('full_name', $homeTeamName)->first();
        $awayTeam = Team::with('stadium')->where('full_name', $awayTeamName)->first();

        $stadiumName = trim(Str::after(Str::before($baseCrawler->filter('.scorebox_meta')->text(), 'Attendance:'), 'Stadium:'));
        $stadium = Stadium::where('name', $stadiumName)->first();

        if ($homeTeam && $awayTeam && $stadium) {
            $date = $this->getDateFromHeader($baseCrawler->filter('h1')->text());
            $time = Str::after(Str::before($baseCrawler->filter('.scorebox_meta')->text(), 'Stadium:'), 'Start Time:');
            
            $roof = trim(Str::after(Str::before($pageText, 'Surface'), 'Roof'));

            $homeTotalYards = (int) $crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(6) > td:nth-child(3)')->text();
            $awayTotalYards = (int) $crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(6) > td:nth-child(2)')->text();
    
            $homePassYards = (int) $crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(5) > td:nth-child(3)')->text();
            $awayPassYards = (int) $crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(5) > td:nth-child(2)')->text();
    
            $awayLongitude = $awayTeam->stadium->longitude;
            $awayLatitude = $awayTeam->stadium->latitude;
            $homeLongitude = $homeTeam->stadium->longitude;
            $homeLatitude = $homeTeam->stadium->latitude;

            $game = Game::updateOrCreate([
                'date_time' => Carbon::parse($date . $time)->toDateTimeString(),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
            ], [
                'week' => $crawler->filter('div.game_summaries:nth-child(1) > h2:nth-child(1) > a:nth-child(1)')->text(),
                'travel_distance' => Distances::getTravelDistance($awayLongitude, $awayLatitude, $homeLongitude, $homeLatitude),
                'roof' => ($roof == 'outdoors') ? $roof : 'indoors',
                'temperature' => ($roof == 'outdoors') ? (int) trim(Str::after(Str::before($pageText, 'degrees'), 'Weather')) : null,
                'home_points' => (int) $baseCrawler->filter('.scorebox')->filter('.score')->first()->text(),
                'away_points' => (int) $baseCrawler->filter('.scorebox')->filter('.score')->last()->text(),
                'home_rush_yards' => $homeTotalYards - $homePassYards,
                'away_rush_yards' => $awayTotalYards - $awayPassYards,
                'home_pass_yards' => $homePassYards,
                'away_pass_yards' => $awayPassYards,
                'home_penalties' => Str::before($crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(9) > td:nth-child(3)')->text(), '-'),
                'away_penalties' => Str::before($crawler->filter('#team_stats > tbody:nth-child(4) > tr:nth-child(9) > td:nth-child(2)')->text(), '-'),
                'url' => $url,
            ]);
        }
    }

    protected function getDateFromHeader($headerText)
    {
        return collect(explode('-', $headerText))->last();
    }

}
