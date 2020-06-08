<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TournamentWinner extends Model
{
    protected $table = 'tournament_winner';

    protected $fillable = ['couple_id', 'tournament_id', 'winner_id'];

    public function couple() {
        return $this->belongsTo(Couple::class);
    }

    public function winner() {
        return $this->belongsTo(Winner::class);
    }
}
