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
	#searchMenuCloseButton {
		position: absolute;
		top: 1rem;
		right: 1rem;
		z-index: 150;
		opacity: 0.75;
	}
	#searchMenuCloseButton:hover {
		opacity: 1;
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

	// Remove the close button
	const closeButton = document.getElementById('searchMenuCloseButton');
	closeButton.remove();
}

function openSearchMenu() {
	searchMenu.setAttribute('open', '');

	createBackdrop();
	createCloseButton();

	function createBackdrop() {
		const backdrop = document.createElement('div');
		backdrop.id = 'searchMenuBackdrop';
		backdrop.classList.add('backdrop', 'active');
		backdrop.addEventListener('click', () => {
			closeSearchMenu();
		});
		document.body.appendChild(backdrop);
	}

	function createCloseButton() {
		const closeButton = document.createElement('button');
		closeButton.id = 'searchMenuCloseButton';
		closeButton.setAttribute('aria-label', 'Close search menu');
		closeButton.addEventListener('click', () => {
			closeSearchMenu();
		});
		closeButton.innerHTML = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
				<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
			</svg>`;

		document.body.appendChild(closeButton);
	}
}

document.addEventListener('keydown', (e) => {
	if (e.key === 'Escape' && searchMenu.hasAttribute('open')) {
		closeSearchMenu();
	}
});
</script>
@endpush