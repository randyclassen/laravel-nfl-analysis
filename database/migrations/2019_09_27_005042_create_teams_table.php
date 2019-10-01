<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('stadium_id')->default(0);
            $table->string('full_name');
            $table->string('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
