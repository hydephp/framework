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

    {{-- Config Defined Tags --}}
    @foreach (config('hyde.meta', []) as $name => $content) 
    <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach

    @stack('meta')

    {{-- App Stylesheets --}}
    @include('hyde::layouts.styles')
  
    {{-- Include any extra tags to include in the <head> section --}}
    @include('hyde::layouts.meta') 

    @if(Features::hasDarkmode())
    {{-- Check the local storage for theme preference to avoid FOUC --}}
    <script>if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark') } </script>
    @endif
</head>
<body id="app" class="flex flex-col min-h-screen overflow-x-hidden dark:bg-gray-900 dark:text-white">
    <a href="#content" id="skip-to-content">Skip to content</a>
    @includeUnless($withoutNavigation ?? false, 'hyde::layouts.navigation') 

    <section>
        @yield('content') 
    </section>

    @includeUnless(config('hyde.footer.enabled', true) && ($withoutNavigation ?? false), 'hyde::layouts.footer') 

    {{-- App Scripts --}}
    @include('hyde::layouts.scripts') 

    {{-- Include any extra scripts to include before the closing <body> tag --}}
    @stack('scripts')
</body>
</html>
