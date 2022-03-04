<?php

namespace App\Support;

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
}
