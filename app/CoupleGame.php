<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoupleGame extends Model
{
    protected $table = 'couple_game';

    protected $fillable = ['couple_id', 'game_id', 'points'];
}
