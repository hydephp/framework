<!-- Include any extra tags to include in the <head> section -->

<!-- Config Defined Tags -->
@foreach (config('hyde.meta', []) as $name => $content)
<meta name="{{ $name }}" content="{{ $content }}">
@endforeach