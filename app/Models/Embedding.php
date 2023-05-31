<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Embedding extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'uuid';
    protected $table = 'embeddings';
    protected $fillable = [
        "uuid", "custom_id", "collection_id", "cmetadata", "document", "embedding"
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
            // This custom_id can be used to user defined identifier
            $model->custom_id = Uuid::uuid4()->toString();
        });
    }
}
