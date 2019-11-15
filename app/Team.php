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

    public function altHomeGames()
    {
        return $this->hasMany(Game::class, 'home_team_id', 'previous_id');
    }

    public function altAwayGames()
    {
        return $this->hasMany(Game::class, 'away_team_id', 'previous_id');
    }

    public function winPct($year, $team = null)
    {
        $homeGames = $this->homeGames()->regularSeason($year);
        $awayGames = $this->awayGames()->regularSeason($year);

        if ($team) {
            $homeGames->where('away_team_id', $team);
            $awayGames->where('home_team_id', $team);
        }

        $homeWins = $homeGames->get()->sum('home_win');
        $awayWins = $awayGames->get()->where('home_win', 0)->count();        
        $totalGames = $homeGames->count() + $awayGames->count();

        if (!$totalGames) {
            return;
        }

        return round(($homeWins + $awayWins) / $totalGames, 3);
    }

    public function homeWinPct($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year);

        $homeWins = $homeGames->get()->sum('home_win');
        $totalGames = $homeGames->count();

        if (!$totalGames) {
            return;
        }

        return round($homeWins / $totalGames, 3);
    }

    public function awayWinPct($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year);

        $awayWins = $awayGames->get()->where('home_win', 0)->count();
        $totalGames = $awayGames->count();

        if (!$totalGames) {
            return;
        }

        return round($awayWins / $totalGames, 3);
    }

    public function avgHomePts($year)
    {
        $games = $this->homeGames()->regularSeason($year)->get();

        return $games->sum('home_points') / $games->count();
    }

    public function avgAwayPts($year)
    {
        $games = $this->awayGames()->regularSeason($year)->get();

        return $games->sum('away_points') / $games->count();
    }

    public function avgHomePtsAllowed($year)
    {
        $games = $this->homeGames()->regularSeason($year)->get();

        return $games->sum('away_points') / $games->count();
    }

    public function avgAwayPtsAllowed($year)
    {
        $games = $this->awayGames()->regularSeason($year)->get();

        return $games->sum('home_points') / $games->count();
    }


    public function avgPtMgnHome($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year)->get();

        return ($homeGames->sum('home_points') - $homeGames->sum('away_points')) / $homeGames->count();
    }

    public function avgPtMgnAway($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year)->get();

        return ($awayGames->sum('away_points') - $awayGames->sum('home_points')) / $awayGames->count();
    }

    public function avgPassYardsHome($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year)->get();

        return $homeGames->sum('home_pass_yards') / $homeGames->count();
    }

    public function avgPassYardsAway($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year)->get();

        return $awayGames->sum('away_pass_yards') / $awayGames->count();
    }
    
    public function avgRushYardsHome($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year)->get();

        return $homeGames->sum('home_rush_yards') / $homeGames->count();
    }

    public function avgRushYardsAway($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year)->get();

        return $awayGames->sum('away_rush_yards') / $awayGames->count();
    }

    public function avgPassYardsAllowedHome($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year)->get();

        return $homeGames->sum('away_pass_yards') / $homeGames->count();
    }

    public function avgPassYardsAllowedAway($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year)->get();

        return $awayGames->sum('home_pass_yards') / $awayGames->count();
    }
    
    public function avgRushYardsAllowedHome($year)
    {
        $homeGames = $this->homeGames()->regularSeason($year)->get();

        return $homeGames->sum('away_rush_yards') / $homeGames->count();
    }

    public function avgRushYardsAllowedAway($year)
    {
        $awayGames = $this->awayGames()->regularSeason($year)->get();

        return $awayGames->sum('home_rush_yards') / $awayGames->count();
    }

    public function rollingHomeWinPct($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return round($games->sum('home_win') / $games->count(), 3);
    }

    public function rollingAwayWinPct($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return round($games->where('home_win', 0)->count() / $games->count(), 3);
    }

    public function rollingAvgPtMgnHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_points') - $games->avg('away_points');
    }

    public function rollingAvgPtMgnAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_points') - $games->avg('home_points');
    }

    public function rollingAvgPtsHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_points');
    }

    public function rollingAvgPtsAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_points');
    }

    public function rollingAvgPtsAllowedHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_points');
    }

    public function rollingAvgPtsAllowedAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_points');
    }



    public function rollingAvgPassYardsHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_pass_yards');
    }

    public function rollingAvgPassYardsAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_pass_yards');
    }
    
    public function rollingAvgRushYardsHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_rush_yards');
    }

    public function rollingAvgRushYardsAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_rush_yards');
    }

    public function rollingAvgPassYardsAllowedHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_pass_yards');
    }

    public function rollingAvgPassYardsAllowedAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_pass_yards');
    }
    
    public function rollingAvgRushYardsAllowedHome($beforeId)
    {
        $games = $this->homeGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altHomeGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('away_rush_yards');
    }

    public function rollingAvgRushYardsAllowedAway($beforeId)
    {
        $games = $this->awayGames()->recent(4, $beforeId)->get();

        if ($games->count() < 4 && $this->previous_id) {
            $prevGames = $this->altAwayGames()->recent(4 - $games->count(), $beforeId)->get();
            $games = $games->concat($prevGames);
        }

        return $games->avg('home_rush_yards');
    }    
}
