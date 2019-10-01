<?php

namespace App\Console\Commands;

use App\Team;
use App\Record;
use Goutte\Client;
use Illuminate\Console\Command;

class CrawlRecords extends Command
{
    protected $signature = 'crawl:nfl:records';

    public function handle(Client $client)
    {
        foreach (range(2014, 2018) as $year) {
            $crawler = $client->request('GET', 'https://www.pro-football-reference.com/years/' . $year);

            foreach (['#AFC > tbody', '#NFC > tbody'] as $selector) {
                $crawler->filter($selector)->filter('tr')->each(function ($tr, $i) use ($year) {
                    $name = $tr->filter('th[data-stat="team"]')->text('');
    
                    if ($name) {
                        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
                        $teamName = collect(explode(' ', $name))->last();
                        
                        $team = Team::where('name', $teamName)->first();

                        $record = Record::firstOrNew([
                            'team_id' => $team->id,
                            'year' => $year,
                        ], [
                            'wins' => $tr->filter('td[data-stat="wins"]')->text(),
                            'losses' => $tr->filter('td[data-stat="losses"]')->text(),
                        ]);

                        $record->save();
                    }
                });
            }
        }
    }
}
