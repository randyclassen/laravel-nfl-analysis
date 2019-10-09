<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $appends = [
        'point_difference',
        'home_win',
        'latitude_difference',
        'pass_yards_difference',
        'rush_yards_difference',
        'latitude difference',
        'penalties_difference',
        'home_team_win_pct',
        'away_team_win_pct',
        'home_team_home_win_pct',
        'away_team_away_win_pct',
    ];
    protected $dates = [
        'date_time',
    ];

    public function homeTeam()
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->hasOne(Team::class, 'id', 'away_team_id');
    }    

    public function homeRecord()
    {
        return $this->hasMany(Record::class, 'team_id', 'home_team_id');
    }

    public function awayRecord()
    {
        return $this->hasMany(Record::class, 'team_id', 'away_team_id');
    } 

    public function getPointDifferenceAttribute()
    {
        return $this->home_points - $this->away_points;
    }

    public function getRushYardsDifferenceAttribute()
    {
        return $this->home_rush_yards - $this->away_rush_yards;
    }

    public function getPassYardsDifferenceAttribute()
    {
        return $this->home_pass_yards - $this->away_pass_yards;
    }

    public function getPenaltiesDifferenceAttribute()
    {
        return $this->home_penalties - $this->away_penalties;
    }

    public function getHomeWinAttribute()
    {
        return (int) ($this->home_points > $this->away_points);
    }

    public function getHomeTeamWinPctAttribute()
    {
        return $this->homeTeam->winPct($this->season);
    }

    public function getAwayTeamWinPctAttribute()
    {
        return $this->awayTeam->winPct($this->season);
    }

    public function getHomeTeamHomeWinPctAttribute()
    {
        return $this->homeTeam->homeWinPct($this->season);
    }

    public function getAwayTeamAwayWinPctAttribute()
    {
        return $this->awayTeam->awayWinPct($this->season);
    }

    public function getLatitudeDifferenceAttribute()
    {
        return $this->homeTeam->stadium->latitude - $this->awayTeam->stadium->latitude;
    }

    public function getStadiumAttribute()
    {
        return $this->homeTeam->stadium;
    }

    public function getStadiumTypeAttribute()
    {
        return $this->stadium->type;   
    }

    public function getSeasonAttribute()
    {
        if ($this->date_time->month <= 2) {
            return $this->date_time->year - 1;
        }

        return $this->date_time->year;
    }
}
