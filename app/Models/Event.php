<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event',
        'date',
        'responsible_person',
        'participants',
        'speaker',
        'photo',
        'video',
        'document',
    ];

    protected $casts = [
        'responsible_person' => 'array',
        'participants' => 'array',
        'speaker' => 'array',
        'photo' => 'array',
        'video' => 'array',
        'document' => 'array',
    ];
}
