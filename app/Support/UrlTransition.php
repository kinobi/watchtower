<?php

namespace App\Support;

enum UrlTransition: string
{
    case READ = 'read';
    case ANNOTATE = 'annotate';

    public function icon(): string
    {
        return match ($this) {
            self::READ => '📖',
            self::ANNOTATE => '✍',
        };
    }
}
