<ul id="sidebar-navigation-menu" role="list">
	@foreach ($sidebar->getCategories() as $category)
	<li class="sidebar-category" role="listitem">
		<h4 class="sidebar-category-heading">{{ Hyde::titleFromSlug($category) }}</h4>
		<ul class="sidebar-category-list" role="list">
			@foreach ($sidebar->getItemsInCategory($category) as $item)
			<li @class([ 'sidebar-navigation-item' , 'active'=> $item->destination === basename($currentPage)]) role="listitem">
				@if($item->destination === basename($currentPage))
				<a href="{{ $item->destination }}.html" aria-current="true">{{ $item->label }}</a>
				@if(isset($docs->tableOfContents))
				<span class="sr-only">Table of contents</span>
				{!! ($docs->tableOfContents) !!}
				@endif
				@else
				<a href="{{ $item->destination }}.html">{{ $item->label }}</a>
				@endif
			</li role="listitem">
			@endforeach
		</ul>
	</li>
	@endforeach
</ul>
