# laravel-nfl-analysis

1. Collects game and other data from pro-football-reference.com.

* [Games](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Console/Commands/CrawlGames.php)
* [Teams](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Console/Commands/CrawlTeams.php)
* [Records](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Console/Commands/CrawlRecords.php)
* [Stadiums](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Console/Commands/CrawlStadiums.php)

2. Calculates a few specific averages and stats.

* Model attributes / scopes: [Game](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Game.php), [Team](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Team.php)
* [Google Maps API](https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Services/Distances.php)

3. Exports to Excel.
* https://github.com/randyclassen/laravel-nfl-analysis/blob/master/app/Console/Commands/ExportNFL.php
