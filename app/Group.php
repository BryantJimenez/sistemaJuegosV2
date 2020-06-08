<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [ 'name', 'slug', 'phase_id', 'tournament_id'];

    public function tournament() {
		return $this->belongsTo(Tournament::class);
	}

	public function phase() {
		return $this->belongsTo(Phase::class);
	}

	public function couples() {
        return $this->belongsToMany(Couple::class)->withTimestamps();
    }

    public function games() {
    	return $this->hasMany(Game::class);
    }
}
