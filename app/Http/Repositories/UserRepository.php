<?php

namespace App\Http\Repositories;

use App\User;

class UserRepository extends ResourceRepository
{
	public function __construct(User $user)
	{
		$this->model = $user;
	}

	public function getById($id)
	{
		return $this->model->findOrFail($id);
	}

	public function getContacts($user_id)
	{
		return $this->model->where('id', '!=', $user_id)->get();
	}
}