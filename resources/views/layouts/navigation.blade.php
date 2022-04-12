@php
$links = Hyde\Framework\Actions\GeneratesNavigationMenu::getNavigationLinks($currentPage);
$homeRoute = ($links[array_search('Home', array_column($links, 'title'))])['route'] ?? 'index.html';
@endphp

<nav aria-label="Main navigation" id="main-navigation" class="flex items-center justify-between flex-wrap p-4 shadow-lg sm:shadow-xl md:shadow-none">
	<div class="flex items-center flex-shrink-0 text-gray-700 mr-6">
		<a aria-label="Home page" href="{{ $homeRoute }}" class="font-bold px-4">
			{{ config('hyde.name', 'HydePHP') }}
		</a>
	</div>
	<div class="block md:hidden">
		<button class="flex items-center px-3 py-1 hover:text-gray-700" onclick="toggleNavigation()">
			<svg style="display: block;" id="open-main-navigation-menu-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><title>Open Menu</title><path d="M0 0h24v24H0z" fill="none"/><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
			<svg style="display: none;" id="close-main-navigation-menu-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><title>Close Menu</title> <path d="M0 0h24v24H0z" fill="none"></path> <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path> </svg>
		</button>
	</div>
	<div id="main-navigation-links" class="w-full hidden md:flex flex-grow md:items-center md:w-auto px-6 -mx-4 border-t mt-3 pt-3 md:border-none md:mt-0 md:py-0">
		<ul aria-label="Navigation links" class="md:flex-grow md:flex justify-end">
			@foreach ($links as $item)
			<li>
				@include('hyde::components.navigation.navigation-link')
			</li>
			@endforeach
			<li>
				<button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
					<svg id="theme-toggle-dark-icon" class="hidden" style="width:1.25rem;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
					<svg id="theme-toggle-light-icon" class="hidden" style="width:1.25rem;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
				</button>
			</li>
		</ul>
	</div>
</nav>
