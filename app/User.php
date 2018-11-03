<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    
    
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }
    
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id');
    }

    

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(User::class, 'contact_user', 'user_id', 'contact_id');
    }

    public function addContact(User $user)
    {
        $this->contacts()->attach($user->id);
    }

    public function removeContact(User $user)
    {
        $this->contacts()->detach($user->id);
    }

}
