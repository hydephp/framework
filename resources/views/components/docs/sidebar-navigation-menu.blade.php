<ul id="sidebar-navigation-menu" role="list">
	@foreach (Hyde\Framework\Services\DocumentationSidebarService::get() as $item)
	<li @class([ 'sidebar-navigation-item' , 'active'=> $item->destination === basename($currentPage)])>
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
	</li>
	@endforeach
</ul>