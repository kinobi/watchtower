<?php

namespace App\Support;

enum UrlStatus: string
{
    case DRAFT = 'draft';
    case READING = 'reading';
    case NOT_ON_KINDLE = 'not_on_kindle';
    case KINDLE = 'kindle';
    case NOT_ANNOTATED = 'not_annotated';
    case ANNOTATED = 'annotated';
    case SHARED = 'shared';
    case NOT_BOOKMARKED = 'not_bookmarked';
    case BOOKMARKED = 'bookmarked';
    case READ = 'read';
}
