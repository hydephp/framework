@php
$links = Hyde\Framework\Actions\GeneratesNavigationMenu::getNavigationLinks($currentPage);
$homeRoute = ($links[array_search('Home', array_column($links, 'title'))])['route'] ?? 'index.html';
@endphp

<nav class="flex items-center justify-between flex-wrap p-4 shadow-lg sm:shadow-xl md:shadow-none">
	<div class="flex items-center flex-shrink-0 text-gray-700 mr-6">
		<a href="{{ $homeRoute }}" class="font-bold px-4">
			{{ config('hyde.name', 'HydePHP') }}
		</a>
	</div>
	<div class="block md:hidden">
		<button class="flex items-center px-3 py-1 hover:text-gray-700">
			<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><title>Menu</title><path d="M0 0h24v24H0z" fill="none"/><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
		</button>
	</div>
	<div class="w-full block flex-grow md:flex md:items-center md:w-auto px-6 -mx-4 border-t mt-3 pt-3 md:border-none md:mt-0 md:py-0">
		<ul class="md:flex-grow md:flex justify-end">
			@foreach ($links as $item)
			<li>
				@include('hyde::components.navigation.navigation-link')
			</li>
			@endforeach
		</ul>
	</div>
</nav>