<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hive extends Model
{
    protected $fillable = ['user_id', 'type', 'name'];
	protected $hidden 	= [];
}
