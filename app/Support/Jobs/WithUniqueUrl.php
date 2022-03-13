<?php

namespace App\Support\Jobs;

use App\Models\Url;

trait WithUniqueUrl
{
    public function uniqueId(): string
    {
        return md5(Url::class . $this->url->getKey());
    }
}
