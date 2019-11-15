<?php

namespace App\Console\Commands;

use App\Game;
use App\Exports\NFLGamesExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExportNFLRolling extends Command
{
    protected $signature = 'export:nfl-rolling';

    public function handle()
    {
        $yearFrom = 2019;
        $games = Game::where('week', 'like', 'Week%')
            ->whereYear('date_time', $yearFrom)
            ->get()
            ->where('season', $yearFrom);

        Excel::store(new NFLGamesExport($games), 'games-2019.xlsx');

        dd($games->count() . ' rows exported.');
    }

}
