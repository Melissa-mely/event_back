<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'description', 'image', 'location', 'date', 'max_participants', 'category_id', 'organizer_id'
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
    
}
