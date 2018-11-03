<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{

	protected $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}
    
	public function signup(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
			'email' => 'required|max:255|email|unique:users',
			'password' => 'required|min:6|max:255|confirmed'
		]);

		$user = new User([
			'name' => $request->input('name'),
			'email' => $request->input('email'),
			'password' => bcrypt($request->input('password'))
		]);

		$user->save();

		return response()->json([
			'message' => 'Account Created'
		], 201);

	}

	public function signin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|max:255|email|exists:users,email',
			'password' => 'required|min:6|max:255'
		]);

		$credentials = $request->only('email', 'password');

		try {
			
			if(!$token = JWTAuth::attempt($credentials))
			{
				return response()->json([
					'error' => 'Invalid credentials.',
				], 401);
			}

		} catch (JWTException $e) {
			return response()->json([
				'error' => 'Error'
			], 500);
		}

		return response()->json([
			'user' => JWTAuth::authenticate($token),
			'token' => $token,
		], 200);
	}

	public function signout(Request $request) {
        
        // $this->validate($request, ['token' => 'required']); // Add middleware
        
        $token = JWTAuth::getToken();

		try {

			$token = JWTAuth::refresh($token); // might fail
			JWTAuth::setToken($token);    
			$user = JWTAuth::authenticate($token);

		} catch(TokenExpiredException $e) {
			//token cannot be refreshed, user needs to login again
			return response()->json(['token_expired'], $e->getStatusCode());
		}

        try {
            JWTAuth::invalidate($token);
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function getAuthenticatedUser()
	{
		try {

		// $user = JWTAuth::parseToken()->toUser(); // Get user
			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			$this->refreshToken();
			// return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		// the token is valid and we have found the user via the sub claim
		return response()->json([
			'user' => $user
		], 200);
	}

	public function refreshToken()
	{

		$token = JWTAuth::getToken();

		try {

			$token = JWTAuth::refresh($token); // might fail
			JWTAuth::setToken($token);    
			$user = JWTAuth::authenticate($token);

			return response()->json([
			'user' => $user,
			'token' => $token
		], 200);

		} catch(TokenExpiredException $e) {
			//token cannot be refreshed, user needs to login again
			return response()->json(['token_expired'], $e->getStatusCode());
		}

	}

	public function contacts(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();

		$users = $this->userRepository->getContacts($user->id);

		return response()->json([
			'data' => $users
		], 200);
	}

}
