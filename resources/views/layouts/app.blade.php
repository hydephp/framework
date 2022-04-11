<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:site_name" content="{{ config('hyde.name', 'HydePHP') }}">

    @if (file_exists(Hyde::path('_media/favicon.ico')))
        <link rel="shortcut icon" href="{{ Hyde::relativePath('media/favicon.ico', $currentPage) }}" type="image/x-icon">
    @endif

    @stack('meta')
    
    @include('hyde::layouts.meta') 

    <title>{{ isset($title) ? config('hyde.name', 'HydePHP') . ' - ' . $title : config('hyde.name', 'HydePHP') }}</title>

    <!-- The compiled Tailwind styles -->
    <link rel="stylesheet" href="{{ Hyde::relativePath('media/app.css', $currentPage) }}">
    
    <!-- The core Hyde stylesheet -->
    <link rel="stylesheet" href="{{ Hyde::relativePath('media/hyde.css', $currentPage) }}">

</head>
<body id="app" class="flex flex-col min-h-screen overflow-x-hidden">
    @includeUnless($withoutNavigation ?? false, 'hyde::layouts.navigation') 

    <section id="content">
        @yield('content') 
    </section>

    @includeUnless(config('hyde.footer.enabled', true) && ($withoutNavigation ?? false), 'hyde::layouts.footer') 

    <!-- The core Hyde scripts -->
    <script defer src="{{ Hyde::relativePath('media/hyde.js', $currentPage) }}"></script>
</body>
</html>
