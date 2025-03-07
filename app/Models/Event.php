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

}
