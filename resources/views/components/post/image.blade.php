<figure aria-label="Cover image" role="doc-cover" itemprop="image" itemscope itemtype="https://schema.org/ImageObject"> 
    <img src="{{ $post->image->getSource() }}" alt="{{ $post->image->description ?? '' }}" title="{{ $post->image->title ?? '' }}" itemprop="image" class="mb-0"> 
    <figcaption aria-label="Image caption" itemprop="caption"> 
        {!! $post->image->getFluentAttribution() !!} 
    </figcaption> 
    @foreach ($post->image->getMetadataArray() as $name => $value) 
	<meta itemprop="{{ $name }}" content="{{ $value }}"> 
    @endforeach 
</figure> 
