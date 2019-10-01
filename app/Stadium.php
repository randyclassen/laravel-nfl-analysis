<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    public $timestamps = false;
    protected $table = 'stadiums';
    protected $guarded = [];
}
