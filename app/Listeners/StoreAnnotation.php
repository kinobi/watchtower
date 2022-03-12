<?php

namespace App\Listeners;

use App\Events\ReplyReceived;
use App\Jobs\CleanChatJob;
use App\Jobs\WorkflowWriteAnnotationJob;
use App\Models\Annotation;
use App\Support\AnnotationStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class StoreAnnotation implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReplyReceived $event): void
    {
        $telegramUpdate = $event->telegramUpdate;

        /** @var Annotation $annotation */
        $annotation = Annotation::where([
            'status' => AnnotationStatus::DRAFT->value,
            'chat_id' => $telegramUpdate->data('message.reply_to_message.chat.id'),
            'message_id' => $telegramUpdate->data('message.reply_to_message.message_id'),
        ])->sole();

        $jobs = [
            new WorkflowWriteAnnotationJob($annotation, $telegramUpdate),
            new CleanChatJob(
                $annotation->getTelegramMessageReference(),
                $telegramUpdate->getTelegramMessageReference(),
            ),
        ];

        Bus::chain($jobs)->dispatch();
    }
}
