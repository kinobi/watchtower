<?php

namespace App\Support;

enum UrlStatus: string
{
    case DRAFT = 'draft';
    case READING = 'reading';
    case NOT_ANNOTATED = 'not_annotated';
    case ANNOTATED = 'annotated';
}
