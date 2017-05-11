<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $fillable = ['type','name'];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
