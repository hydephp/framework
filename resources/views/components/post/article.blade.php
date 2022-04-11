<article id="{{ Hyde::uriPath() ?? '' }}posts/{{ $post->slug }}" itemscope itemtype="https://schema.org/Article"
    @class(['post-article mx-auto prose', 'torchlight-enabled' => Hyde\Framework\Features::hasTorchlight()])>
    <meta itemprop="identifier" content="{{ $post->slug }}">
    @if(Hyde::uriPath())
    <meta itemprop="url" content="{{ Hyde::uriPath('posts/' . $post->slug) }}">
    @endif
    
    <header role="doc-pageheader">
        <h1 itemprop="headline" class="mb-4">{{ $title ?? 'Blog Post' }}</h1>
		<div id="byline" aria-label="About the post" role="doc-introduction">
            @includeWhen($post->date, 'hyde::components.post.date')
		    @includeWhen($author, 'hyde::components.post.author')
            @includeWhen($category, 'hyde::components.post.category')
        </div>
    </header>
    @includeWhen(isset($post->image), 'hyde::components.post.image')
    <div itemprop="articleBody">
        {!! $markdown !!}
    </div>
</article>
