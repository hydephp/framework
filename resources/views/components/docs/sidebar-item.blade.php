@php /** @var \Hyde\Framework\Features\Navigation\NavItem $item */ @endphp
@props(['grouped' => false])
<li @class(['sidebar-item -ml-4 pl-4', $grouped
        ? 'active -ml-8 pl-8 bg-black/5 dark:bg-black/10'
        : 'active bg-black/5 dark:bg-black/10' => $item->isCurrent()
    ]) role="listitem">
    @if($item->isCurrent())
        <a href="{{ $item->destination }}" aria-current="true" @class([$grouped
            ? '-ml-8 pl-4 py-1 px-2 block text-indigo-600 dark:text-indigo-400 dark:font-medium border-l-[0.325rem] border-indigo-500 transition-colors duration-300 ease-in-out hover:bg-black/10'
            : '-ml-4 p-2 block hover:bg-black/5 dark:hover:bg-black/10 text-indigo-600 dark:text-indigo-400 dark:font-medium border-l-[0.325rem] border-indigo-500 transition-colors duration-300 ease-in-out'
        ])>
            {{ $item->label }}
        </a>

        @if(config('docs.table_of_contents.enabled', true))
            <span class="sr-only">Table of contents</span>
            {!! ($page->getTableOfContents()) !!}
        @endif
    @else
        <a href="{{ $item->destination }}" @class([$grouped
            ? '-ml-8 pl-4 py-1 px-2 block border-l-[0.325rem] border-transparent transition-colors duration-300 ease-in-out hover:bg-black/10'
            : 'block -ml-4 p-2 border-l-[0.325rem] border-transparent hover:bg-black/5 dark:hover:bg-black/10'
        ])>
            {{ $item->label }}
        </a>
    @endif
</li>