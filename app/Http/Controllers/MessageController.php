<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Repositories\UserRepository;
use App\Http\Repositories\MessageRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use App\Events\MessageSent;
use App\Notifications\MessageReceived;
use Illuminate\Support\Carbon;

class MessageController extends Controller
{
    protected $messageRepository;
    protected $userRepository;
    protected $nbrperPage;

    public function __construct(UserRepository $userRepository, MessageRepository $messageRepository)
    {
    	$this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
        $this->nbrperPage = 5;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        // $messages = $this->messageRepository->get();
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['errors' => ['user' => 'Make sure to be authenticated. User not found.']], 404);
        }

        $receiver = $this->userRepository->getById($id);

        $messages = $this->messageRepository->getMessagesFor($user->id, $receiver->id)/*->paginate($this->nbrperPage)*/;
        if($request->get('before'))
        {
        	$messages = $messages->where('created_at', '<', $request->get('before'));
        }
        // $messages =  $messages->reverse()->values()->all();

        // $unread = $this->messageRepository->unreadMessagesFor($user->id, $receiver->id);

        // if($unread)
        // {
        // 	$this->messageRepository->readAllFrom($user->id, $receiver->id);
        // }
        $_m = $messages;
        foreach ($_m->get() as $message) {
        	if($message->read_at == null && $message->receiver_id == $user->id)
        	{
        		$this->messageRepository->readAllFrom($user->id, $receiver->id);
        		break;
        	}
        }
        // $messages_sent = $user->sentMessages()->where('receiver_id', $receiver->id)->get();
        // $messages_received = $user->receivedMessages()->where('user_id', $user->id)->get();
        // $messages = $messages_sent->merge($messages_received);

        return response()->json(['data' => ['messages' => $messages->limit($this->nbrperPage)->get(), 'count' => $messages->count()]], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required|max:255',
        ]);

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['errors' => ['user' => 'Make sure to be authenticated. User not found.']], 404);
        }

        $text = $request->input('text');
        $receiver_id = $request->input('receiver_id');

        if($receiver = $this->userRepository->getById($receiver_id))
        {
	        $inputs = [
	            'text' => nl2br(e($text)),
	            'user_id' => $user->id,
	            'receiver_id' => $receiver->id,
	        ];

	        $message = $this->messageRepository->store($inputs);
	        $message->user()->associate($user);
	        // $message->receiver()->associate($receiver);
	        $message->save();

	        if($message)
	        {
	        	// $receiver->notify(new MessageReceived($message));
	            broadcast(new MessageSent($message))->toOthers();
	            return response()->json(['data' => $message], 201);
	        }
        }
        else
        {
        	return response()->json(['errors' => ['The contact you are trying to reach does not exist.']], 404);
        }


        /*
                    config/app.php uncomment App\Providers\BroadcastServiceProvider::class,
                    .env BROADCAST_DRIVER=pusher and add app_ id, key, secred and cluster
                    composer require pusher/pusher-php-server "~3.0"
                    npm install --save laravel-echo pusher-js
                    ressources/assets/js/bootstrap.js uncomment Echo and Pusher comments and add key and cluster
                    php artisan make:event EventClass add public properties and channel name
                    EventController event(new EventClass($properties, ...)); or broadcast(new EventClass($properties, ...))->toOthers();

                    JWT:

                    ressources/assets/js/bootstrap.js :
    
                    authEndpoint: '/broadcasting/auth?token=' + localStorage.getItem('token'),
                    or better
                    auth: {
                         headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        },
                    },
                    on the fly in the function of the vue file:
                    Echo.connector.pusher.config.auth.headers.Authorization = 'Bearer ' + localStorage.getItem('token');
                    
                    app\Providers\BroadcastServiceProvider.php Broadcast::routes( [ 'middleware' => [ 'api', 'auth.jwt' ] ] );

                */
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'text' => 'required|max:255',
        ]);

        $text = $request->input('text');

        $inputs = [
            'text' => $text,
        ];

        $message = $this->messageRepository->update($id, $inputs);

        if($message)
        {
            return response()->json(['data' => $message], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($message = $this->messageRepository->destroy($id))
        {
            return response()->json(['data' => $message], 201);
        }
    }

    
	public function conversations(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$conversations = $this->userRepository->getContacts($user->id);
		$unread = $this->messageRepository->unreadCount($user->id);

		foreach ($conversations as $key => $conversation) {
			if(isset($unread[$conversation->id]))
			{
				$conversation->unread = $unread[$conversation->id];
			}
			else
			{
				$conversation->unread = 0;
			}
		}

		return response()->json([
			'data' => ['users' => $conversations]
		], 200);
	}}
