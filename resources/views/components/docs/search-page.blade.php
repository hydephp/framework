<h1>Search the documentation</h1>

<div id="hyde-search">
    <noscript>
        The search feature requires JavaScript to be enabled in your browser.
    </noscript>
    <input type="search" name="search" id="search-input" placeholder="Search..." autocomplete="off">
</div>

<script src="https://cdn.jsdelivr.net/npm/hydesearch@0.2.1/dist/HydeSearch.min.js" defer></script>

<script>
    window.addEventListener('load', function() {
        const searchIndexLocation = 'search.json';
        const Search = new HydeSearch(searchIndexLocation);

        Search.init();
    });
</script>