<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date_time');
            $table->integer('travel_distance');
            $table->string('roof');
            $table->integer('home_team_id');
            $table->integer('away_team_id');
            $table->integer('away_points');
            $table->integer('home_points');
            $table->integer('home_rush_yards');
            $table->integer('away_rush_yards');
            $table->integer('home_pass_yards');
            $table->integer('away_pass_yards');
            $table->integer('home_penalties');
            $table->integer('away_penalties');
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
}
