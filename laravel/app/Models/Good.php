<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'whisper_id',
        'reply_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function whisper()
    {
        return $this->belongsTo('App\Models\Whisper');
    }
}
