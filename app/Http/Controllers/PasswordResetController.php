<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|max:255|email|exists:users,email',
            'password' => 'required|min:6|max:255|confirmed',
            'token' => 'required',
        ]);


        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        if(DB::table('password_resets')->where(['email' => $email, 'token' => $token])->first())
        {
            $user = User::where('email', $email)->first();
            $user->update([
                'password' => bcrypt($password)
            ]);
            DB::table('password_resets')->where(['email' => $email, 'token' => $token])->delete();
        }
        else
        {
            return response()->json(['error' => 'Invalid token'], 422);
        }

        return response()->json('Password Changed', 201);
    }

    public function email(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email'
        ]);

        $email = $request->email;
        $token = str_random(60);

        if(DB::table('password_resets')->where('email', $email)->first())
        {
            DB::table('password_resets')->where('email', $email)->update([
                                                                'token' => $token,
                                                                'created_at' => Carbon::now(),
                                                            ]);
        }
        else
        {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);   
        }

        Mail::to($email)->send(new PasswordResetMail($token));
        
        return response()->json('Check your mail inbox.', 200);
    }
}
