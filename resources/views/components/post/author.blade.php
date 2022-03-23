@php
	$authorObject = Hyde\Framework\Services\AuthorService::find($author);
@endphp
by author
<address itemprop="author publisher" itemscope itemtype="https://schema.org/Person"> 
	@if($authorObject && $authorObject->website)
	<a href="{{ $authorObject->website }}" rel="author" itemprop="url">
	@endif
	<span itemprop="name" {{ $authorObject && $authorObject->username ? 'title=@'.$authorObject->username.'' : '' }}>{{ $authorObject->name ?? $author }}</span> 
	@if($authorObject && $authorObject->website)
	</a>
	@endif
</address> 
