<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //

    protected $fillable = [
    	'title', 'slug', 'body'
    ];

    public function tags()
    {
    	return $this->belongsToMany(Tag::class);
    }

    public function section()
    {
    	return $this->belongsTo(Section::class);
    }

    public function images()
    {
    	return $this->hasMany(Image::class);
    }
}
