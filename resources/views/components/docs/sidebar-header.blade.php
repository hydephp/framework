<header class="h-16 p-4 border-b flex items-center">
    <h2 class="font-bold opacity-75 hover:opacity-100 w-fit">
        @if(Hyde::docsIndexPath() !== false)
        <a href="../{{ Hyde::docsIndexPath() }}">
            {{ config('hyde.name') }} Docs
        </a>
        @else
            {{ config('hyde.name') }} Docs
        @endif
    </h2>
</header>
