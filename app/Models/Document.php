<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_id',
        'file_path',
        'file_name',
        'file_size',
        'user_id',
        'sharing_token',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($document) {
            $document->threads->each(function ($thread) {
                $thread->messages()->delete();
                $thread->delete();
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}