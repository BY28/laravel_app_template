<?php

namespace App\Http\Repositories;

use App\Post;

class PostRepository extends ResourceRepository
{
	public function __construct(Post $post)
	{
		$this->model = $post;
	}

    public function getPaginate($n, $col, $order)
	{
		return $this->model->with('section', 'tags', 'images')->orderBy($col, $order)->paginate($n);
	}

	
	public function getById($id)
	{
		return $this->model->with('section', 'tags', 'images')->findOrFail($id);
	}

	public function isUniqueSlug($slug)
	{
		return !$this->model->where('slug', $slug)->exists();
	}

	public function getBySlug($slug)
	{
		return $this->model->where('slug', $slug)->with('section', 'tags', 'images')->first();
	}
}