<article class="mt-4 mb-8" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="identifier" content="{{ $post->slug }}">
	@if(Hyde::uriPath())
    <meta itemprop="url" content="{{ Hyde::uriPath('posts/' . $post->slug) }}">
    @endif
    
	<header>
		<a href="posts/{{ $post->matter['slug'] }}.html" class="block w-fit">
			<h2 class="text-2xl font-bold text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white transition-colors duration-75">
				{{ $post->matter['title'] ?? $post->title }}
			</h2>
		</a>
	</header>
	<footer>
		@isset($post->matter['date'])
		<span class="opacity-75">
			<span itemprop="dateCreated datePublished">
				{{ date('M jS, Y', strtotime($post->matter['date'])) }}</span>{{ isset($post->matter['author']) ? ',' : '' }}
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
		@isset($post->matter['description'])
		<section role="doc-abstract" aria-label="Excerpt">
			<p class="leading-relaxed my-1">
				{{ $post->matter['description'] }}
			</p>
		</section>
		@endisset
			
		<a href="posts/{{ $post->matter['slug'] }}.html" class="text-indigo-500 hover:underline font-medium">Read post</a>
	</div>	
</article>