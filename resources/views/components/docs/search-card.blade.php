<style>
	body {
		background-color: #f2f2f2;
		font-family: 'Raleway', sans-serif;
		font-size: 16px;
		line-height: 1.5;
		color: #333;
		padding: 24px 16px;
	}
	input#search-input {
		background-color: #fff;
	}
	main {
		width: 65ch;
		max-width: 100vw;
	}
	.hyde-search-context {
		margin-bottom: 8px;
		white-space: pre-line;
	}
	.search-term-count {
		font-size: 0.9em;
		font-style: italic;
		color: #555;
	}
	.search-status small {
		opacity: 0.75;
	}
</style>

<main>
@include('hyde::components.docs.search-component')
</main>