<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
        // Relation avec les favoris (utilisateurs)

        public function favorites()
        {
            return $this->belongsToMany(Event::class, 'favorites');


        }
        // Relation avec les événements auxquels l'utilisateur a participé
        
        public function participatedEvents()
        {
            return $this->belongsToMany(Event::class, 'event_participant');
        }


        
        //utilisateur  peut avoir plusieurs événements
        public function events()
{
    return $this->hasMany(Event::class, 'organizer_id');
}


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
