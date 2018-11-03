<?php

namespace App\Http\Repositories;

use App\Product;

class ProductRepository extends ResourceRepository
{
	public function __construct(Product $product)
	{
		$this->model = $product;
	}
}