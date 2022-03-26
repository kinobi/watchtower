<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaData extends Model
{
    use HasFactory;

    protected $casts = [
        'meta' => 'array',
    ];

    protected $fillable = [
        'meta',
        'provider',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }
}
