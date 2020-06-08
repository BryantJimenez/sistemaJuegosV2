<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoupleGamer extends Model
{
    protected $table = 'couple_gamer';

    protected $fillable = ['couple_id', 'gamer_id'];
}
