<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gamer extends Model
{
    protected $fillable = [ 'name', 'lastname', 'photo', 'slug'];

    public function tournaments() {
        return $this->belongsToMany(Tournament::class)->withTimestamps();
    }

    public function couples() {
        return $this->belongsToMany(Couple::class)->withTimestamps();
    }
}
