<?php

namespace App\Http\Repositories;

use App\Tag;
use Illuminate\Support\Str;

class TagRepository extends ResourceRepository
{
	public function __construct(Tag $tag)
	{
		$this->model = $tag;
	}

	public function store($tags_array)
	{

        $persisted_tags = Tag::whereIn('name', $tags_array)->get();

        $tags_array = array_diff($tags_array, $persisted_tags->pluck('name')->all());

		$tags_array = array_map(function($tag){
            return ['name' => Str::lower($tag), 'slug' => Str::slug($tag)];
        }, $tags_array);

        $tags = collect([]);

        foreach ($persisted_tags as $key => $tag) {
            $tags->push($tag);
        }

        foreach ($tags_array as $key => $tag) {
            $tags->push($this->model->create($tag));
        }
        
        return $tags;
	}

	public function search($query, $n)
	{
		return $this->model->where('name', 'LIKE', '%'.$query.'%')->paginate($n);
	}


	public function isUniqueSlug($slug)
	{
		return !$this->model->where('slug', $slug)->exists();
	}

	public function getBySlug($slug)
	{
		return $this->model->where('slug', $slug)->first();
	}
}