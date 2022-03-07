<?php

use App\Support\UrlTransition;

return [
    'fallback' => '🏳️ Not yet implemented',
    'txtpaper' => [
        'success' => '📔 WatchTower sending to the Kindle',
        'failed' => '⛔ WatchTower failed to send to the Kindle',
    ],
    'raindrop' => [
        'success' =>  '🔖 WatchTower has bookmarked !',
        'failed' => '⛔ WatchTower failed to bookmark',
        'tags' => ['watchtower']
    ],
    'url' => [
        'created' => '📥 Watchtower received a new entry !',
        'duplicated' => '⚠ Watchtower already get this one !',
        'reading' => '📖 Enjoy reading !',
        'read' => '📗 Done reading !',
        'reset' => '⏮️ Sent back in draft !',
        'unknown' => '⚠ Unknown Url !',
        'transition' => [
            UrlTransition::TO_READING->value => ':icon going to reading...',
            UrlTransition::TO_READ->value => ':icon going to read...',
            UrlTransition::TO_KINDLE->value => ':icon sending to Kindle...',
            UrlTransition::BOOKMARK->value => ':icon creating bookmark...',
            UrlTransition::RESET->value => ':icon resetting...',
        ]
    ],
];
