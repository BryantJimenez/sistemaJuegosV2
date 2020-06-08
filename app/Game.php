<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['slug', 'type', 'state', 'rond', 'group_id'];

    public function couples() {
        return $this->belongsToMany(Couple::class);
    }

    public function game_winner() {
        return $this->hasOne(GameWinner::class);
    }

    public function couple_game() {
        return $this->hasMany(CoupleGame::class);
    }
}
