Search results for: <b>{{ $search }}</b>

@forelse($results as $url)
    ➡ {{ $url->title }} /get_{{ $url->id }}

@empty
    No result
@endforelse
