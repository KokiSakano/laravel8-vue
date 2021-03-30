<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whisper extends Model
{
    use HasFactory;
    protected $fillable = [
        'whisp',
        'user_id',
        'good',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
