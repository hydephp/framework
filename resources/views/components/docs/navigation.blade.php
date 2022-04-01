<div class="flex flex-row items-center justify-between h-full px-4">
	<span class="font-bold">
		@if(Hyde::docsIndexPath() !== false)
		<a href="{{ basename(Hyde::docsIndexPath()) }}">
			{{ config('hyde.name') }} Docs
		</a>
		@else
			{{ config('hyde.name') }} Docs
		@endif
	</span>

	@include('hyde::components.docs.sidebar-toggle')
</div>