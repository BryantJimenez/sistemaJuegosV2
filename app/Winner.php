<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
	protected $fillable = ['type', 'position'];

	public function tournaments() {
		return $this->belongsToMany(Tournament::class)->withTimestamps();
	}

	public function game_winner() {
        return $this->hasOne(GameWinner::class);
    }
}
