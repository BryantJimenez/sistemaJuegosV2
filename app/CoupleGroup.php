<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoupleGroup extends Model
{
    protected $table = 'couple_group';

    protected $fillable = ['couple_id', 'group_id'];
}