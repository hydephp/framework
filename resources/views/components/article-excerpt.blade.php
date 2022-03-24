<article class="mt-4 mb-8" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="identifier" content="{{ $post->slug }}">
	@if(Hyde::uriPath())
    <meta itemprop="url" content="{{ Hyde::uriPath($post->slug) }}">
    @endif
    
	<header>
		<a href="posts/{{ $post->matter['slug'] }}.html">
			<h2 class="text-2xl font-bold opacity-75 hover:opacity-100">{{ $post->matter['title'] }}</h2>
		</a>
	</header>
	<footer>
		@isset($post->matter['date'])
		<span class="opacity-75">
			<span itemprop="dateCreated datePublished">
				{{ date('M jS, Y', strtotime($post->matter['date'])) }}</span>,
		</span>
		@endisset
		@isset($post->matter['author'])
		<span itemprop="author" itemscope itemtype="https://schema.org/Person">
			<span class="opacity-75">by</span>
			<span itemprop="name">
				{{ $post->matter['author'] }}
			</span>
		</span>
		@endisset
	</footer>
	
	<div>
		<p class="leading-relaxed my-1">
			@isset($post->matter['description'])
			{{ $post->matter['description'] }}
			@endisset
		</p>
		<a href="posts/{{ $post->matter['slug'] }}.html" class="text-indigo-500 hover:underline font-medium">Read post</a>
	</div>	
</article>