<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [ 'name', 'slug', 'groups', 'couples', 'start', 'type', 'state', 'end', 'club_id'];

    public function gamers() {
        return $this->belongsToMany(Gamer::class)->withTimestamps();
    }

    public function groups() {
        return $this->hasMany(Group::class);
    }

    public function winners() {
        return $this->belongsToMany(Winner::class)->withTimestamps();
    }

    public function winners_couples() {
        return $this->belongsToMany(Couple::class)->withTimestamps();
    }

    public function club() {
        return $this->belongsTo(Club::class);
    }
}
