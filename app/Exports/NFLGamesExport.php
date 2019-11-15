<?php

namespace App\Exports;

use App\Game;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class NFLGamesExport implements FromCollection, WithStrictNullComparison, WithHeadings
{
    public function __construct(Collection $games)
    {
        $this->games = $games;
    }

    public function collection()
    {
        return $this->games;
    }

    public function headings() : array
    {
        return array_keys($this->games->first()->toArray());
    }
}
