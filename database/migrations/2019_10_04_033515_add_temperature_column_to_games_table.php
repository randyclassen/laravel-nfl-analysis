<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemperatureColumnToGamesTable extends Migration
{

    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->integer('temperature')->after('travel_distance')->nullable();
            $table->string('url')->default('');
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('temperature');
            $table->dropColumn('url');
        });
    }
}
