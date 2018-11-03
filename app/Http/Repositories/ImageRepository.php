<?php

namespace App\Http\Repositories;

use App\Image;

class ImageRepository extends ResourceRepository
{
	public function __construct(Image $image)
	{
		$this->model = $image;
	}

    public function getPaginate($n, $col, $order)
	{
		return $this->model->with('post')->orderBy($col, $order)->paginate($n);
	}
}