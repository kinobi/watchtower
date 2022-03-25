<?php

use App\Support\UrlTransition;

return [
    'fallback' => '🏳️ Not yet implemented',
    'txtpaper' => [
        'success' => '📔 WatchTower sending to the Kindle',
        'failed' => '⛔ WatchTower failed to send to the Kindle',
    ],
    'raindrop' => [
        'success' => '🔖 WatchTower has bookmarked !',
        'failed' => '⛔ WatchTower failed to bookmark',
        'tags' => ['watchtower']
    ],
    'bitly' => [
        'tags' => ['watchtower']
    ],
    'url' => [
        'created' => '📥 Watchtower received a new entry !',
        'duplicated' => '⚠ Watchtower already get this one !',
        'reading' => '📖 Enjoy reading !',
        'read' => '📗 Done reading !',
        'reset' => '⏮️ Sent back in draft !',
        'annotated' => '📝 Annotated !',
        'shared' => '📰 Shared !',
        'note_trashed' => [
            'success' => '🗑️ Note trashed !',
            'failed' => '⚠ Failed to trash the note !',
        ],
        'unknown' => '⚠ Unknown Url !',
        'stats' => [
            'queue' => '📊 :count_draft links waiting, currently reading :count_reading and :count_read read',
        ],
        'transition' => [
            UrlTransition::TO_READING->value => ':icon going to reading...',
            UrlTransition::TO_READ->value => ':icon going to read...',
            UrlTransition::TO_KINDLE->value => ':icon sending to Kindle...',
            UrlTransition::BOOKMARK->value => ':icon creating bookmark...',
            UrlTransition::RESET->value => ':icon resetting...',
            UrlTransition::ANNOTATE->value => ':icon creating note...',
            UrlTransition::TRASH_NOTE->value => ':icon deleting note...',
            UrlTransition::SHARE->value => ':icon sharing...',
        ],
    ],
    'annotation' => [
        'create' => UrlTransition::ANNOTATE->icon() . ' Write a note for ":title"',
    ],
];
