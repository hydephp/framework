<ul id="sidebar-navigation-menu" role="list">
	@foreach (Hyde\Framework\Services\DocumentationSidebarService::getCategories() as $category)
	<li role="listitem">
		<h4>{{ Hyde::titleFromSlug($category) }}</h4>
		<ul role="list">
			@foreach (Hyde\Framework\Services\DocumentationSidebarService::getItemsInCategory($category) as $item)
			<li @class([ 'sidebar-navigation-item' , 'active'=> $item->destination === basename($currentPage)]) role="listitem">
				@if($item->destination === basename($currentPage))
				<a href="{{ $item->destination }}.html" aria-current="true">{{
					$item->label }}</a>
		
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