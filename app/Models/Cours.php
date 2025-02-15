<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;
    protected $fillable = ['titre', 'description', 'image'];
    public function audios()
    {
        return $this->hasMany(Audio::class);
    }
    //
}
