<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GamerTournament extends Model
{
	protected $table = 'gamer_tournament';

    protected $fillable = ['gamer_id', 'tournament_id', 'couple_id'];

    public function couple() {
		return $this->belongsTo(Couple::class);
	}
}
