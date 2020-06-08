<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
	protected $fillable = ['club_id'];

    public function gamers() {
		return $this->belongsToMany(Gamer::class)->withTimestamps();
	}

	public function club() {
		return $this->belongsTo(Club::class);
	}

	public function winners_tournaments() {
        return $this->belongsToMany(Tournament::class)->withTimestamps();
    }

    public function games() {
        return $this->belongsToMany(Game::class)->withTimestamps();
    }

	public function groups() {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    public function gamer_tournaments() {
        return $this->hasMany(GamerTournament::class);
    }
}
