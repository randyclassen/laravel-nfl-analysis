<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $timestamps = false;
    protected $dates = ['date_time'];
    protected $guarded = [];
    protected $hidden = [
        'id', 
        'url', 
        'roof', 
        'date_time', 
        'temperature',
        'home_team_id', 
        'away_team_id', 
        'home_pass_yards', 
        'away_pass_yards',
        'home_rush_yards', 
        'away_rush_yards',         
        'home_penalties',
        'away_penalties',
    ];
    protected $appends = [
        'point_diff',
        'home_win',
        'time',
        'exp_home_pts',
        'exp_away_pts',
        'home_avg_pt_mgn',
        'away_avg_pt_mgn',
        'home_pass_yards_diff',
        'away_pass_yards_diff',
        'home_rush_yards_diff',
        'away_rush_yards_diff',
        'home_pass_off',
        'away_pass_off',
        'home_rush_off',
        'away_rush_off',
        'avg_exp_home_pts',
        'avg_exp_away_pts',
        'avg_home_pass_off',
        'avg_away_pass_off',
        'avg_home_rush_off',
        'avg_away_rush_off',     
        'avg_home_win_pct',
        'avg_away_win_pct',
        'home_team_home_win_pct',
        'away_team_away_win_pct',
        'penalties_diff',
        'stadium_type',
        'lat_diff',
        'long_diff',
    ];

    public function homeTeam()
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->hasOne(Team::class, 'id', 'away_team_id');
    }    

    public function scopeRegularSeason($query, $year)
    {
        return $query->whereYear('date_time', $year)->where('week', 'like', 'Week%');
    }
    
    public function scopeRecent($query, $numGames, $gameId)
    {
        return $query->where('id', '<', $gameId)
            ->where('week', 'like', 'Week%')
            ->orderByDesc('id')
            ->take($numGames);
    }
    
    public function getWeekAttribute($value)
    {
        return preg_replace("/[^0-9]/", "", $value);
    }

    public function getTimeAttribute()
    {
        return $this->date_time->hour + ($this->date_time->minute / 60);
    }

    public function getPointDiffAttribute()
    {
        return $this->home_points - $this->away_points;
    }

    public function getExpHomePtsAttribute()
    {
        return $this->homeTeam->avgHomePts($this->season)
            + $this->awayTeam->avgAwayPtsAllowed($this->season);
    }

    public function getExpAwayPtsAttribute()
    {
        return $this->awayTeam->avgAwayPts($this->season)
            + $this->homeTeam->avgHomePtsAllowed($this->season);
    }

    public function getHomeAvgPtMgnAttribute()
    {
        return $this->homeTeam->avgPtMgnHome($this->season);
    }

    public function getAwayAvgPtMgnAttribute()
    {
        return $this->awayTeam->avgPtMgnAway($this->season);
    }

    public function getHomeWinAttribute()
    {
        return (int) ($this->home_points > $this->away_points);
    }

    public function getHomeTeamHomeWinPctAttribute()
    {
        return $this->homeTeam->homeWinPct($this->season);
    }

    public function getAwayTeamAwayWinPctAttribute()
    {
        return $this->awayTeam->awayWinPct($this->season);
    }

    public function getPenaltiesDiffAttribute()
    {
        return $this->home_penalties - $this->away_penalties;
    }

    public function getLatDiffAttribute()
    {
        return $this->homeTeam->stadium->latitude - $this->awayTeam->stadium->latitude;
    }

    public function getLongDiffAttribute()
    {
        return $this->homeTeam->stadium->longitude - $this->awayTeam->stadium->longitude;
    }

    public function getStadiumTypeAttribute()
    {
        return (int) ($this->roof === 'indoors');
    }

    public function getSeasonAttribute()
    {
        if ($this->date_time->month <= 2) {
            return $this->date_time->year - 1;
        }

        return $this->date_time->year;
    }

    public function getHomePassYardsDiffAttribute()
    {
        return $this->homeTeam->avgPassYardsHome($this->season)
            - $this->awayTeam->avgPassYardsAllowedAway($this->season);
    }

    public function getAwayPassYardsDiffAttribute()
    {
        return $this->awayTeam->avgPassYardsAway($this->season)
            - $this->homeTeam->avgPassYardsAllowedHome($this->season);
    }    

    public function getHomeRushYardsDiffAttribute()
    {
        return $this->homeTeam->avgRushYardsHome($this->season)
            - $this->awayTeam->avgRushYardsAllowedAway($this->season);
    }

    public function getAwayRushYardsDiffAttribute()
    {
        return $this->awayTeam->avgRushYardsAway($this->season)
            - $this->homeTeam->avgRushYardsAllowedHome($this->season);
    }

    public function getHomePassOffAttribute()
    {
        return $this->homeTeam->avgPassYardsHome($this->season)
            + $this->awayTeam->avgPassYardsAllowedAway($this->season);
    }

    public function getAwayPassOffAttribute()
    {
        return $this->awayTeam->avgPassYardsAway($this->season)
            + $this->homeTeam->avgPassYardsAllowedHome($this->season);
    }    

    public function getHomeRushOffAttribute()
    {
        return $this->homeTeam->avgRushYardsHome($this->season)
            + $this->awayTeam->avgRushYardsAllowedAway($this->season);
    }

    public function getAwayRushOffAttribute()
    {
        return $this->awayTeam->avgRushYardsAway($this->season)
            + $this->homeTeam->avgRushYardsAllowedHome($this->season);
    }

    // ---------------------------- Rolling averages ----------------------------

    public function getAvgHomeWinPctAttribute()
    {
        return $this->homeTeam->rollingHomeWinPct($this->id);
    }

    public function getAvgAwayWinPctAttribute()
    {
        return $this->awayTeam->rollingAwayWinPct($this->id);
    }

    public function getAvgExpHomePtsAttribute()
    {
        return $this->homeTeam->rollingAvgPtsHome($this->id)
            + $this->awayTeam->rollingAvgPtsAllowedAway($this->id);
    }

    public function getAvgExpAwayPtsAttribute()
    {
        return $this->awayTeam->rollingAvgPtsAway($this->id)
            + $this->homeTeam->rollingAvgPtsAllowedHome($this->id);
    }


    public function getAvgHomePassOffAttribute()
    {
        return $this->homeTeam->rollingAvgPassYardsHome($this->id)
            + $this->awayTeam->rollingAvgPassYardsAllowedAway($this->id);
    }

    public function getAvgAwayPassOffAttribute()
    {
        return $this->awayTeam->rollingAvgPassYardsAway($this->id)
            + $this->homeTeam->rollingAvgPassYardsAllowedHome($this->id);
    }    

    public function getAvgHomeRushOffAttribute()
    {
        return $this->homeTeam->rollingAvgRushYardsHome($this->id)
            + $this->awayTeam->rollingAvgRushYardsAllowedAway($this->id);
    }

    public function getAvgAwayRushOffAttribute()
    {
        return $this->awayTeam->rollingAvgRushYardsAway($this->id)
            + $this->homeTeam->rollingAvgRushYardsAllowedHome($this->id);
    }

}
