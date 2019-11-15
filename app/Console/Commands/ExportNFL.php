<?php

namespace App\Console\Commands;

use App\Game;
use App\Exports\NFLGamesExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExportNFL extends Command
{
    protected $signature = 'export:nfl';

    public function handle()
    {
        $yearFrom = 2017;
        $yearTo = 2018;
        $games = Game::where('week', 'like', 'Week%')
            ->whereYear('date_time', '>=', $yearFrom)
            ->whereYear('date_time', '<=', $yearTo)
            ->get()
            ->whereBetween('season', [$yearFrom, $yearTo]);

        Excel::store(new NFLGamesExport($games), 'games.xlsx');
        
        $test = $games->random(75);
        $train = $games->diff($test);

        Excel::store(new NFLGamesExport($test), 'test-games.xlsx');
        Excel::store(new NFLGamesExport($train), 'train-games.xlsx');

        dump($test->count() . ' testing rows exported.');
        dump($train->count() . ' training rows exported.');
        dd($games->count() . ' rows exported.');
    }

}
