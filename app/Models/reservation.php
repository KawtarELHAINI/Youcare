<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    use HasFactory;
    protected $fillable = [

        'annoucement_id',
        'statut',

    ];
    public function annoucement()
    {
        return $this->belongsTo(Annoucements::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'benevole_id');
    }
}



