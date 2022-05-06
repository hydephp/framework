<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ isset($title) ? config('hyde.name', 'HydePHP') . ' - ' . $title : config('hyde.name', 'HydePHP') }}</title>

@if (file_exists(Hyde::path('_media/favicon.ico'))) 
<link rel="shortcut icon" href="{{ Hyde::relativePath('media/favicon.ico', $currentPage) }}" type="image/x-icon">
@endif

{{-- App Meta Tags --}}
@include('hyde::layouts.meta') 

{{-- App Stylesheets --}}
@include('hyde::layouts.styles')

@if(Hyde::features('darkmode'))
{{-- Check the local storage for theme preference to avoid FOUC --}}
<script>if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark') } </script>
@endif