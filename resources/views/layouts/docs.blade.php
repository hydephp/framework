<!doctype html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{ isset($title) ? config('hyde.name', 'HydePHP') . ' - ' . $title : config('hyde.name', 'HydePHP') }}
	</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/caendesilva/lagrafo@v0.1.0-beta/dist/lagrafo.min.css">
</head>

<body id="lagrafo-app">
	<script>
		document.body.classList.add('js-enabled');
	</script>

	<nav id="mobile-navigation">
		<strong>
			@if(Hyde::docsIndexPath() !== false)
			<a href="{{ basename(Hyde::docsIndexPath()) }}">
				{{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}
			</a>
			@else
			{{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}
			@endif
		</strong>
		<button id="sidebar-toggle" title="Toggle sidebar" aria-label="Toggle sidebar navigation menu">
			<span class="icon-bar" role="presentation"></span>
			<span class="icon-bar" role="presentation"></span>
			<span class="icon-bar" role="presentation"></span>
			<span class="icon-bar" role="presentation"></span>
		</button>
	</nav>
	<aside id="sidebar">
		<header id="sidebar-header">
			<div id="sidebar-brand">
				<strong>
					@if(Hyde::docsIndexPath() !== false)
					<a href="{{ basename(Hyde::docsIndexPath()) }}">
						{{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}
					</a>
					@else
					{{ config('hyde.docsSidebarHeaderTitle', 'Documentation') }}
					@endif
				</strong>
			</div>
		</header>
		<nav id="sidebar-navigation">
			<ul id="sidebar-navigation-menu" role="list">
				@foreach (Hyde\Framework\Actions\GeneratesDocumentationSidebar::get($currentPage) as $item)
				<li @class([ 'sidebar-navigation-item' , 'active'=> $item['active']
					])>
					@if($item['active'])
					<a href="{{ $item['slug'] }}.html" aria-current="true">{{
						$item['title'] }}</a>

					@if(isset($docs->tableOfContents))
					{!! ($docs->tableOfContents) !!}
					@endif
					@else
					<a href="{{ $item['slug'] }}.html">{{ $item['title'] }}</a>
					@endif
				</li>
				@endforeach
			</ul>
		</nav>
		<footer id="sidebar-footer">
			<p>
				<a href="{{ Hyde::relativePath('index.html', $currentPage) }}">Back to home page</a>
			</p>
		</footer>
	</aside>
	<main id="content">
		<article id="document" itemscope itemtype="https://schema.org/Article" @class(['mx-auto prose dark:prose-invert
			max-w-3xl', 'torchlight-enabled'=> Hyde\Framework\Features::hasTorchlight()])>
			<section id="document-main-content" itemprop="articleBody">
				{!! $markdown !!}
			</section>
			<footer id="document-footer">
				<nav id="pagination">
					{{-- NYI --}}
				</nav>
	
				<a href="#">Edit this page</a>
			</footer>
		</article>
	</main>
	<script defer="" src="https://cdn.jsdelivr.net/gh/caendesilva/lagrafo@v0.1.0-beta/dist/lagrafo.min.js"></script>
</body>

</html>