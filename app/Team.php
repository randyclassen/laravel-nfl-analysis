<?php

namespace App;

use App\Stadium;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function stadium()
    {
        return $this->hasOne(Stadium::class, 'id', 'stadium_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    public function homeGames()
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames()
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function winPct($year)
    {
        $homeGames = $this->homeGames->where('season', $year);
        $awayGames = $this->awayGames->where('season', $year);
        $homeWins = $homeGames->sum('home_win');
        $awayWins = $awayGames->where('home_win', 0)->count();        
        $totalGames = $homeGames->count() + $awayGames->count();

        return ($homeWins + $awayWins) / $totalGames;        
    }

    public function homeWinPct($year)
    {
        $homeGames = $this->homeGames->where('season', $year);
        $homeWins = $homeGames->sum('home_win');
        $totalGames = $homeGames->count();

        return $homeWins / $totalGames;
    }

    public function awayWinPct($year)
    {
        $awayGames = $this->awayGames->where('season', $year);
        $awayWins = $awayGames->where('home_win', 0)->count();
        $totalGames = $awayGames->count();

        return $awayWins / $totalGames;
    }    
}
