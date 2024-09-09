<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = [
        'openai_thread_id',
        'assistant_id',
        'document_id',
        'title',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
