<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
    	'text', 'user_id', 'receiver_id', 'read_at'
    ];

    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function receiver()
    {
    	return $this->belongsTo(User::class, 'receiver_id');
    }
}
