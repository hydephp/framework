by author
<address itemprop="author" itemscope itemtype="https://schema.org/Person" aria-label="The post author" style="display: inline;"> 
	@if($post->author->website)
	<a href="{{ $post->author->website }}" rel="author" itemprop="url" aria-label="The author's website">
	@endif
	<span itemprop="name" aria-label="The author's name" {{ $post->author->username ? 'title=@'.$post->author->username.'' : '' }}>{{ $post->author->name ?? $post->author->username }}</span> 
	@if($post->author->website)
	</a>
	@endif
</address> 
