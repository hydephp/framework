@php /** @var \Hyde\Pages\MarkdownPost $post */ @endphp
<article class="mt-4 mb-8" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="identifier" content="{{ $post->identifier }}">
    @if(Hyde::hasSiteUrl())
        <meta itemprop="url" content="{{ Hyde::url('posts/' . $post->identifier) }}">
    @endif

    <header>
        <a href="{{ $post->getRoute() }}" class="block w-fit">
            <h2 class="text-2xl font-bold text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white transition-colors duration-75">
                {{ $post->data('title') ?? $post->title }}
            </h2>
        </a>
    </header>

    <footer>
        @isset($post->date)
            <span class="opacity-75">
            <span itemprop="dateCreated datePublished">
                {{ $post->date->short }}</span>{{ isset($post->author) ? ',' : '' }}
        </span>
        @endisset
        @isset($post->author)
            <span itemprop="author" itemscope itemtype="https://schema.org/Person">
            <span class="opacity-75">by</span>
            <span itemprop="name">
                {{ $post->author->name ?? $post->author->username }}
            </span>
        </span>
        @endisset
    </footer>

    @if($post->data('description') !== null)
        <section role="doc-abstract" aria-label="Excerpt">
            <p class="leading-relaxed my-1">
                {{ $post->data('description') }}
            </p>
        </section>
    @endisset

    <footer>
        <a href="{{ $post->getRoute() }}"
           class="text-indigo-500 hover:underline font-medium">
            Read post</a>
    </footer>
</article>