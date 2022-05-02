<header class="flex flex-grow h-16 pl-4 max-h-16 items-center">
    <h2 class="font-bold text-gray-700 hover:text-gray-900 dark:text-gray-200 w-fit">
        @if(Hyde::docsIndexPath() !== false)
        <a href="{{ basename(Hyde::docsIndexPath()) }}">
            {{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}

        </a>
        @else
            {{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}
        @endif
    </h2>
    <div class="ml-auto">
        @include('hyde::components.navigation.theme-toggle-button')
    </div>
</header>
