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

class ExportNFL extends Command
{
    protected $signature = 'export:nfl';

    public function handle()
    {
dd(Game::pluck('roof')->unique());
        $responses = [
            'home_win',
            'point_difference',
        ];

        $predictors = [
            'date_time',
            'home_pass_yards',
            'away_pass_yards',
            'pass_yards_difference',
            'home_rush_yards',
            'away_rush_yards',
            'rush_yards_difference',
            'travel_distance',
            'roof',
            'temperature',
            'latitude difference',
            'penalties_difference',
            'home_team_win_pct',
            'away_team_win_pct',
            'home_team_home_win_pct',
            'away_team_away_win_pct',
        ];

        $gamesPlayoffs = Game::select($responses + $predictors)
            ->whereYear('date_time', '>=', '2014')
            ->whereMonth('date_time', '01')
            ->get();
dd($gamesPlayoffs);
        $gamesRegular = Game::select($responses + $predictors)
            ->whereYear('date_time', '2018')
            ->whereMonth('date_time', '>', '07')
            ->get();
dd($gamesRegular);
    }

}
