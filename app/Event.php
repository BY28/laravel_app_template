<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
    	'title', 'description', 'start', 'end',
    ];


    public function users()
    {
    	return $this->belongsToMany(User::class);
    }
}
