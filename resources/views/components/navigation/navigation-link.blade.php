<a href="{{ $item['route'] }}" {{ $item['current'] ? 'aria-current="page"' : '' }} 
	@class(['block my-2 md:my-0 md:inline-block text-gray-700 hover:text-gray-900 md:mx-2 py-1', 'border-l-4 border-indigo-500 md:border-none font-medium -ml-6 pl-5 md:ml-0 md:pl-0' => $item['current']])>
	{{ $item['title'] }}
</a>