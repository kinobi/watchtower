<?php

namespace App\Support;

use App\Jobs\WorkflowReadingUrlJob;
use App\Jobs\WorkflowReadUrlJob;
use App\Jobs\WorkflowResetUrlToDraftJob;
use App\Jobs\WorkflowSendUrlToKindleJob;
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
        return match ($this) {
            self::TO_READING => $this->answerToReading($url),
            self::TO_READ => $this->answerToRead($url),
            self::TO_KINDLE => $this->answerToKindle($url),
            self::ANNOTATE => '📝',
            self::TRASH_NOTE => '🗑️',
            self::BOOKMARK => '🔖',
            self::SHARE => '📰',
            self::RESET => $this->answerReset($url),
        };
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

    private function answerToReading(Url $url): string
    {
        WorkflowReadingUrlJob::dispatch($url);
        return $this->icon();
    }

    private function answerToRead(Url $url): string
    {
        WorkflowReadUrlJob::dispatch($url);
        return $this->icon();
    }

    private function answerToKindle(Url $url): string
    {
        WorkflowSendUrlToKindleJob::dispatch($url);
        return $this->icon();
    }

    private function answerReset(Url $url): string
    {
        WorkflowResetUrlToDraftJob::dispatch($url);
        return $this->icon();
    }
}
