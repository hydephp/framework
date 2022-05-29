<noscript><style>#searchMenuButton{display:none;}</style></noscript>

<button id="searchMenuButton" onclick="toggleSearchMenu()">Search</button>
@push('scripts')
	
<style>
	#searchMenu {
		padding: 16px;
		max-width: 90vw;
		width: 70ch;
		min-height: 300px;
		margin-top: 10vh;
		border-radius: 8px;
		z-index: 250;
		max-height: 75vh;
		overflow-y: hidden;
	}
	#search-results-list {
		max-height: 60vh;
		overflow-y: auto;
	}
	#searchMenu input {
		width: 100%;
		padding: 8px 12px;
		border-radius: 4px;
		font-size: 1rem;
		line-height: 1.5;
		background-color: #fff;
	}
	.dark #searchMenu input {
		background-color: #374151;
		color: #fff;
	}
	#searchMenuBackdrop {
		background-color: rgba(0,0,0,0.5);
		z-index: 100;
	}
</style>

<dialog id="searchMenu" class="prose dark:prose-invert bg-gray-100 dark:bg-gray-800">
	@include('hyde::components.docs.search-component')
</dialog>

<script>
const searchMenu = document.getElementById('searchMenu');

function toggleSearchMenu() {
	if (searchMenu.hasAttribute('open')) {
		closeSearchMenu();
	} else {
		openSearchMenu();
	}
}

function closeSearchMenu() {
	searchMenu.removeAttribute('open');

	// Remove the backdrop
	const backdrop = document.getElementById('searchMenuBackdrop');
	backdrop.remove();
}

function openSearchMenu() {
	searchMenu.setAttribute('open', '');

	// Create a backdrop
	const backdrop = document.createElement('div');
	backdrop.id = 'searchMenuBackdrop';
	backdrop.classList.add('backdrop', 'active');
	backdrop.addEventListener('click', () => {
		closeSearchMenu();
	});
	document.body.appendChild(backdrop);
}
</script>
@endpush