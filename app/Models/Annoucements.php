<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annoucements extends Model
{
    use HasFactory;

    protected $table ='annoucements';

    protected $fillable = [
       'title',
       'type',
        'date',
        'description',
        'location',
        'required_skills', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}
