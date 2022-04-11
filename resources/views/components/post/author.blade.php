@php
	$authorObject = Hyde\Framework\Services\AuthorService::find($author);
@endphp
by author
<address itemprop="author" itemscope itemtype="https://schema.org/Person" aria-label="The post author" style="display: inline;"> 
	@if($authorObject && $authorObject->website)
	<a href="{{ $authorObject->website }}" rel="author" itemprop="url" aria-label="The author's website">
	@endif
	<span itemprop="name" aria-label="The author's name" {{ $authorObject && $authorObject->username ? 'title=@'.$authorObject->username.'' : '' }}>{{ $authorObject->name ?? $author }}</span> 
	@if($authorObject && $authorObject->website)
	</a>
	@endif
</address> 
