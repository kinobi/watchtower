<?php

namespace App\Services;

use App\Models\Url;

class UrlMessageFormatter
{
    public const URL_FORMAT_HTML = <<<EOF
<b><a href="%s">%s</a></b>

%s

📥 <i>%s</i>
EOF;

    public const ANNOTATION_FORMAT_HTML = <<<EOF
\n
🗒️ %s
EOF;

    /**
     * Format the HTML text payload for a message Url
     *
     * @param Url $url
     * @param string $resultText
     * @return string
     */
    public function formatHtmlMessage(Url $url, string $resultText): string
    {
        $text = sprintf(
            self::URL_FORMAT_HTML,
            $url->uri,
            $url->title,
            $resultText,
            $url->created_at->ago(),
        );

        if ($url->annotation?->note) {
            $text .= sprintf(self::ANNOTATION_FORMAT_HTML, $url->annotation->note);
        }

        return $text;
    }
}
