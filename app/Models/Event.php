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

        static::updating(function (Event $event) {
            $photosToDelete = array_diff($event->getOriginal('photo'), $event->photo);
            $videosToDelete = array_diff($event->getOriginal('video'), $event->video);
            $documentsToDelete = array_diff($event->getOriginal('document'), $event->document);

            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo);
            }
            foreach ($videosToDelete as $video) {
                Storage::disk('public')->delete($video);
            }
            foreach ($documentsToDelete as $document) {
                Storage::disk('public')->delete($document);
            }
        });
    }
}
