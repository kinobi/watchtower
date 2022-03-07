<?php

namespace App\Support\Job;

use App\Models\Url;

trait WithUniqueUrl
{
    public function uniqueId(): string
    {
        return md5(Url::class . $this->url->getKey());
    }
}
