<?php

namespace App\Models;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

class Annotation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WorkflowTrait;

    protected $fillable = [
        'chat_id',
        'message_id',
        'note',
    ];

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    public function getTelegramMessageReference(): MessageReference
    {
        return new MessageReference($this->chat_id, $this->message_id);
    }
}
