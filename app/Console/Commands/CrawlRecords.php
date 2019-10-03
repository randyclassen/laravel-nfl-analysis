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
                        $name = str_replace(['*', '+'], '', $name);

                        $team = Team::where('full_name', $name)->first();

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
