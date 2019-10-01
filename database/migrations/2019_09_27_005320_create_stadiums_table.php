<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStadiumsTable extends Migration
{

    public function up()
    {
        Schema::create('stadiums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stadiums');
    }
}
