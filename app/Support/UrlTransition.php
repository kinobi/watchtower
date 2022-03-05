<?php

namespace App\Support;

use App\Jobs\ReadingUrlJob;
use App\Jobs\ResetUrlToDraftJob;
use App\Jobs\SendUrlToKindleJob;
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
            self::TO_READ => '📗',
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
        ReadingUrlJob::dispatch($url);
        return $this->icon();
    }

    private function answerToKindle(Url $url): string
    {
        SendUrlToKindleJob::dispatch($url);
        return $this->icon();
    }

    private function answerReset(Url $url): string
    {
        ResetUrlToDraftJob::dispatch($url);
        return $this->icon();
    }
}
