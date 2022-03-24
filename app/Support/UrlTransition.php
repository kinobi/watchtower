<?php

namespace App\Support;

use App\Jobs\CreateAnnotationJob;
use App\Jobs\WorkflowBookmarkUrlJob;
use App\Jobs\WorkflowReadingUrlJob;
use App\Jobs\WorkflowReadUrlJob;
use App\Jobs\WorkflowResetUrlToDraftJob;
use App\Jobs\WorkflowSendUrlToKindleJob;
use App\Jobs\WorkflowShareUrlJob;
use App\Jobs\WorkflowTrashNoteJob;
use App\Models\Url;

enum UrlTransition: string
{
    case TO_READING = 'to_reading';
    case TO_KINDLE = 'to_kindle';
    case TO_READ = 'to_read';
    case ANNOTATE = 'annotate';
    case TRASH_NOTE = 'trash_note';
    case BOOKMARK = 'bookmark';
    case SHARE = 'share';
    case RESET = 'reset';

    public function answerCallback(Url $url): string
    {
        match ($this) {
            self::TO_READING => WorkflowReadingUrlJob::dispatch($url),
            self::TO_READ => WorkflowReadUrlJob::dispatch($url),
            self::TO_KINDLE => WorkflowSendUrlToKindleJob::dispatch($url),
            self::BOOKMARK => WorkflowBookmarkUrlJob::dispatch($url),
            self::RESET => WorkflowResetUrlToDraftJob::dispatch($url),
            self::ANNOTATE => CreateAnnotationJob::dispatch($url),
            self::TRASH_NOTE => WorkflowTrashNoteJob::dispatch($url),
            self::SHARE => WorkflowShareUrlJob::dispatch($url),
        };

        return $this->getAnswerNotificationText();
    }

    public function icon(): string
    {
        return match ($this) {
            self::TO_READING => '📖',
            self::TO_READ => '📗',
            self::TO_KINDLE => '📲',
            self::ANNOTATE => '📝',
            self::TRASH_NOTE => '🗑️',
            self::BOOKMARK => '🔖',
            self::SHARE => '📰',
            self::RESET => '⏮️',
        };
    }

    private function getAnswerNotificationText(): string
    {
        return __('watchtower.url.transition.' . $this->value, ['icon' => $this->icon()]);
    }
}
