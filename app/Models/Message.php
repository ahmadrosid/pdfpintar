<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'role',
        'content',
        'conversation_id',
        'created_at',
        'updated_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
