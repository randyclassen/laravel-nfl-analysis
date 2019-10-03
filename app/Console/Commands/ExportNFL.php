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
        $gamesPlayoffs = Game::whereYear('date_time', '>=', '2014')
            ->whereMonth('date_time', '01')
            ->get();
        
        /*
            Predictors:
            - passing yards
            - rushing yards
            - travel distance
            - team ending record (or home team home win pct and away team away win pct)
            - latitude difference
            - game date
            - stadium type
            
            Responses:
            - point difference
            - home win

        */
        
    }

}
