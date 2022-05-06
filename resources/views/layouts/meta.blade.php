{{-- Config Defined Tags --}}
@foreach (config('hyde.meta', []) as $name => $content) 
<meta name="{{ $name }}" content="{{ $content }}">
@endforeach

{{-- Add any extra tags to include in the <head> section --}}
<meta property="og:site_name" content="{{ config('hyde.name', 'HydePHP') }}">

{{-- Add any extra meta tags to include after the others --}}
@stack('meta')
