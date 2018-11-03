<?php

namespace App\Http\Repositories;

use App\Event;

class EventRepository extends ResourceRepository
{
	public function __construct(Event $event)
	{
		$this->model = $event;
	}

    public function getPaginate($n, $col, $order)
	{
		return $this->model/*->with('users')*/->orderBy($col, $order)->paginate($n);
	}

	
	public function getById($id)
	{
		return $this->model/*->with('users')*/->findOrFail($id);
	}
}