<?php

use App\Models\Annotation;
use App\Models\Url;
use App\Support\AnnotationStatus;
use App\Support\AnnotationTransition;
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
            UrlStatus::NOT_ON_KINDLE->value,
            UrlStatus::KINDLE->value,
            UrlStatus::NOT_ANNOTATED->value,
            UrlStatus::ANNOTATED->value,
            UrlStatus::SHARED->value,
            UrlStatus::BOOKMARKED->value,
            UrlStatus::NOT_BOOKMARKED->value,
            UrlStatus::READ->value,
        ],
        'initial_places' => [UrlStatus::DRAFT->value],
        'transitions' => [
            UrlTransition::TO_READING->value => [
                'from' => [UrlStatus::DRAFT->value],
                'to' => [UrlStatus::READING->value, UrlStatus::NOT_ANNOTATED->value, UrlStatus::NOT_ON_KINDLE->value],
            ],
            UrlTransition::ANNOTATE->value => [
                'from' => [UrlStatus::NOT_ANNOTATED->value],
                'to' => [UrlStatus::ANNOTATED->value],
            ],
            UrlTransition::TRASH_NOTE->value => [
                'from' => [UrlStatus::ANNOTATED->value],
                'to' => [UrlStatus::NOT_ANNOTATED->value],
            ],
            UrlTransition::TO_KINDLE->value => [
                'from' => [UrlStatus::NOT_ON_KINDLE->value],
                'to' => [UrlStatus::KINDLE->value],
            ],
            UrlTransition::TO_READ->value => [
                'from' => [UrlStatus::READING->value],
                'to' => [UrlStatus::READ->value, UrlStatus::NOT_BOOKMARKED->value],
            ],
            UrlTransition::SHARE->value => [
                'from' => [
                    [UrlStatus::READ->value, UrlStatus::ANNOTATED->value],
                ],
                'to' => [UrlStatus::SHARED->value],
            ],
            UrlTransition::BOOKMARK->value => [
                'from' => [UrlStatus::NOT_BOOKMARKED->value],
                'to' => [UrlStatus::BOOKMARKED->value],
            ],
            UrlTransition::RESET->value => [
                'from' => [
                    [
                        UrlStatus::READING->value,
                        UrlStatus::NOT_ANNOTATED->value,
                        UrlStatus::NOT_ON_KINDLE->value
                    ]
                ],
                'to' => [UrlStatus::DRAFT->value],
            ],
        ],
    ],
    'annotation_workflow' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
        ],
        'supports' => [Annotation::class],
        'places' => [
            AnnotationStatus::DRAFT->value,
            AnnotationStatus::CREATED->value,
        ],
        'initial_places' => [AnnotationStatus::DRAFT->value],
        'transitions' => [
            AnnotationTransition::WRITE->value => [
                'from' => [AnnotationStatus::DRAFT->value],
                'to' => [AnnotationStatus::CREATED->value],
            ],
        ],
    ],
];
