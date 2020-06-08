<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameWinner extends Model
{
    protected $table = 'game_winner';

    protected $fillable = ['game_id', 'couple_id', 'winner_id'];

    public function winner() {
        return $this->hasOne(Winner::class);
    }

    public function game() {
        return $this->hasOne(Game::class);
    }

    public function couple() {
        return $this->hasOne(Couple::class);
    }
}
