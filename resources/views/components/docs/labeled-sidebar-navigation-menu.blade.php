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

<style>
	/* Style the sidebar layout for sidebars with categories */
	/* Inline styles until PR is ready, @todo merge into HydeFront */
	.sidebar-category {
		margin-top: 1rem;
		margin-bottom: 1rem;
	}
	.sidebar-category-heading {
		font-size: 1rem;
		font-weight: 600;
		margin-bottom: 0.5rem;
		margin-left: -0.25rem;
	}
	.sidebar-category-list {
		margin-left: 1rem;
	}
	
	#lagrafo-app #sidebar #sidebar-navigation .sidebar-category-list .sidebar-navigation-item.active {
		margin-left: -2rem;
    	padding-left: 2rem;
	}
	#lagrafo-app #sidebar #sidebar-navigation .sidebar-category-list .sidebar-navigation-item > a {
		margin-left: -2rem;
    	padding-left: 1rem;
		/* Decrease vertical spacing for a more compact layout */
		padding-top: 0.2rem;
		padding-bottom: 0.2rem;
		margin-top: 0.2rem;
		margin-bottom: 0.2rem;
	}
</style>