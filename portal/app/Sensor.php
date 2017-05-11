<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    public $fillable = ['type','name','key'];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
