<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $fillable = ['nom', 'fichier', 'cours_id'];

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }
}
