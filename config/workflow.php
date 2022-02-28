<?php

use App\Models\Url;
use App\Support\UrlStatus;
use App\Support\UrlTransition;

return [
    'url_workflow' => [
        'type' => 'workflow',
        'marking_store' => [
            'property' => 'status',
        ],
        'supports' => [Url::class],
        'places' => [
            UrlStatus::DRAFT->value,
            UrlStatus::READING->value,
            UrlStatus::NOT_ANNOTATED->value,
            UrlStatus::ANNOTATED->value
        ],
        'initial_places' => [UrlStatus::DRAFT->value],
        'transitions' => [
            UrlTransition::READ->value => [
                'from' => UrlStatus::DRAFT->value,
                'to' => [UrlStatus::READING->value, UrlStatus::NOT_ANNOTATED->value],
            ],
            UrlTransition::ANNOTATE->value => [
                'from' => UrlStatus::NOT_ANNOTATED->value,
                'to' => UrlStatus::ANNOTATED->value,
            ],
        ],
    ],
];
