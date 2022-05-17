{{-- Config Defined Meta Tags --}}
@foreach (config('hyde.meta', []) as $name => $content) 
<meta name="{{ $name }}" content="{{ $content }}">
@endforeach

@foreach (config('hyde.ogProperties', []) as $property => $content) 
<meta property="og:{{ $property }}" content="{{ $content }}">
@endforeach

{{-- Add any extra tags to include in the <head> section --}}
@stack('meta')

