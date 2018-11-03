<?php

namespace App\Http\Repositories;

use App\Section;

class SectionRepository extends ResourceRepository
{
	public function __construct(Section $section)
	{
		$this->model = $section;
	}
}