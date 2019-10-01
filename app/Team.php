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
}
