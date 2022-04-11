<article id="{{ Hyde::uriPath() ?? '' }}posts/{{ $post->slug }}" class="post-article" itemscope itemtype="https://schema.org/Article"
    @class(['mx-auto prose', 'torchlight-enabled' => Hyde\Framework\Features::hasTorchlight()])>
    <meta itemprop="identifier" content="{{ $post->slug }}">
    @if(Hyde::uriPath())
    <meta itemprop="url" content="{{ Hyde::uriPath('posts/' . $post->slug) }}">
    @endif
    
    <header role="doc-pageheader">
        <h1 itemprop="headline" class="mb-4">{{ $title ?? 'Blog Post' }}</h1>
		<div id="byline" aria-label="About the post" role="doc-introduction">
            @includeWhen($date, 'hyde::components.post.datePublished')
		    @includeWhen($author, 'hyde::components.post.author')
            @includeWhen($category, 'hyde::components.post.category')
            @includeWhen($post->image, 'hyde::components.post.image')
        </div>
    </header>
    <div itemprop="articleBody">
        {!! $markdown !!}
    </div>
</article>
