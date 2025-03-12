<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\error;

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
        'responsible_person' => 'json',
        'participants' => 'json',
        'speaker' => 'array',
        'photo' => 'json',
        'video' => 'json',
        'document' => 'json',
    ];

    protected static function booted(): void
    {
        self::deleted(function (Event $event) {
            Storage::disk('public')->delete($event->photo);
            Storage::disk('public')->delete($event->video);
            Storage::disk('public')->delete($event->document);
        });
        self::updating(function (Event $event) {
            if ($event->isDirty('photo')) {
                Storage::disk('public')->delete($event->getOriginal('photo'));
            }
            if ($event->isDirty('video')) {
                Storage::disk('public')->delete($event->getOriginal('video'));
            }
            if ($event->isDirty('document')) {
                Storage::disk('public')->delete($event->getOriginal('document'));
            }
        });
    }
}
