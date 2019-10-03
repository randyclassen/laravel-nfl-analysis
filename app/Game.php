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
        'home_team_win_pct',
        'away_team_win_pct',
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

    public function getHomeWinAttribute()
    {
        return (int) ($this->home_points > $this->away_points);
    }

    public function getHomeTeamWinPctAttribute()
    {
        $record = $this->homeRecord->where('year', $this->season)->first();

        return 100 * $record->wins / ($record->wins + $record->losses);
    }

    public function getAwayTeamWinPctAttribute()
    {
        $record = $this->awayRecord->where('year', $this->season)->first();

        return 100 * $record->wins / ($record->wins + $record->losses);
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
