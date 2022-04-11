<!DOCTYPE html>
<html lang="{{ config('hyde.language', 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:site_name" content="{{ config('hyde.name', 'HydePHP') }}">
    <title>{{ isset($title) ? config('hyde.name', 'HydePHP') . ' - ' . $title : config('hyde.name', 'HydePHP') }}</title>

    @if (file_exists(Hyde::path('_media/favicon.ico'))) 
    <link rel="shortcut icon" href="{{ Hyde::relativePath('media/favicon.ico', $currentPage) }}" type="image/x-icon">
    @endif

    <!-- Config Defined Tags -->
    @foreach (config('hyde.meta', []) as $name => $content) 
    <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach

    @stack('meta')
  
    <!-- The compiled Tailwind styles -->
    <link rel="stylesheet" href="{{ Hyde::relativePath('media/app.css', $currentPage) }}">
    
    <!-- The core Hyde stylesheet -->
    <link rel="stylesheet" href="{{ Hyde::relativePath('media/hyde.css', $currentPage) }}">
  
    <!-- Include any extra tags to include in the <head> section -->
    @include('hyde::layouts.meta') 
</head>
<body id="app" class="flex flex-col min-h-screen overflow-x-hidden">
    <a href="#content" id="skip-to-content">Skip to content</a>
    @includeUnless($withoutNavigation ?? false, 'hyde::layouts.navigation') 

    <section id="content">
        @yield('content') 
    </section>

    @includeUnless(config('hyde.footer.enabled', true) && ($withoutNavigation ?? false), 'hyde::layouts.footer') 

    <!-- The core Hyde scripts -->
    <script defer src="{{ Hyde::relativePath('media/hyde.js', $currentPage) }}"></script>

    @stack('scripts')

    <!-- Include any extra scripts to include in before the closing <body> tag -->
    @include('hyde::layouts.scripts') 
</body>
</html>
