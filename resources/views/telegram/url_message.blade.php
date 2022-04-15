<b><a href="{{ $url->uri }}">{{ $url->title }}</a></b>

📥 <i>{{ $url->created_at->ago() }}</i>
@isset($text)

{{ $text }}

@endisset
@isset($url->annotation?->note)

🗒️ {{ $url->annotation->note }}
@endisset
