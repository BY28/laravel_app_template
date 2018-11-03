<?php

namespace App\Http\Repositories;

use App\Message;
use Illuminate\Support\Carbon;

class MessageRepository extends ResourceRepository
{
	public function __construct(Message $message)
	{
		$this->model = $message;
	}

    public function get()
	{
		return $this->model->with('user')->get();
	}

	public function getById($id)
	{
		return $this->model->with('user')->findOrFail($id);
	}

	public function getMessagesFor($user_id, $receiver_id)
	{
		return $this->model->with('user')->whereRaw( "( (user_id = $user_id AND receiver_id = $receiver_id) OR (user_id = $receiver_id AND receiver_id = $user_id) )" )/*where([['user_id', $user_id], ['receiver_id', $receiver_id]])->orWhere([['user_id', $receiver_id], ['receiver_id', $user_id]])*/->orderBy('created_at', 'DESC');
	}

	public function unreadCount($user_id)
	{
		return $this->model->where('receiver_id', $user_id)
							->groupBy('user_id')
							->selectRaw('user_id, COUNT(id) as count')
							->where('read_at', null)
							->get()
							->pluck('count', 'user_id');
	}


	public function unreadMessagesFor($user_id, $receiver_id)
	{
		return $this->model->where([['receiver_id', $user_id], ['user_id', $receiver_id]])
							->groupBy('user_id')
							->selectRaw('user_id, COUNT(id) as count')
							->where('read_at', null)
							->get()
							->pluck('count', 'user_id');
	}

	public function readAllFrom($user_id, $receiver_id)
	{
		$this->model->where([['receiver_id', $user_id], ['user_id', $receiver_id]])->update(['read_at' => Carbon::now()]);
	}
}