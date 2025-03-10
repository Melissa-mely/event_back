<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 
        'description', 
        'image', 
        'location', 
        'date', 
        'max_participants', 
        'organizer_id'
    ];

    // Relation avec la catégorie
    public function categories()
{
    return $this->belongsToMany(Category::class, 'category_event');
}

    // Relation avec l'organisateur (utilisateur)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
        
    }
    // Relation avec les participants (utilisateurs)
    public function participants()
{
    return $this->belongsToMany(User::class, 'event_participant');
}

 

}
