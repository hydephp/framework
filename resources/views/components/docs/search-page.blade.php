<h1>Search the documentation</h1>

<style>
	#search-input {
		font-size: 16px;
		line-height: 1.5;
		padding: 6px 10px;
		background-color: #eee;
		color: #000
	}
	.dark #search-input {
		background-color: #333;
		color: #fff;
	}
</style>

<div id="hyde-search">
    <noscript>
        The search feature requires JavaScript to be enabled in your browser.
    </noscript>
    <input type="search" name="search" id="search-input" placeholder="Search..." autocomplete="off" autofocus>
</div>

<script src="https://cdn.jsdelivr.net/npm/hydesearch@0.2.1/dist/HydeSearch.min.js" defer></script>

<script>
    window.addEventListener('load', function() {
        const searchIndexLocation = 'search.json';
        const Search = new HydeSearch(searchIndexLocation);

        Search.init();
    });
</script>