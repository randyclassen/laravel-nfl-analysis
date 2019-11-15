<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeekColumnToGamesTable extends Migration
{

    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('week')->after('date_time')->nullable();
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('week');
        });
    }
}
