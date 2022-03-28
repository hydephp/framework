{{-- The Documentation Page Layout based on Laradocgen --}}
@extends('hyde::layouts.app')
@section('content')
@php($withoutNavigation = true)
<nav id="documentation-navigation" class="md:hidden fixed top-0 w-screen h-16 p-4 shadow-lg sm:shadow-xl overflow-hidden bg-white z-30">
	@include('hyde::components.docs.navigation')
</nav>
<aside id="documentation-sidebar" class="w-64 h-screen hidden md:flex flex-col fixed top-0 left-0 shadow-md overflow-hidden bg-white z-20">
	@include('hyde::components.docs.sidebar')
</aside>
<main id="documentation-content" class="mx-auto max-w-7xl py-16 px-8 mt-8 md:mt-0 md:absolute md:left-72 xl:left-80">
	@include('hyde::components.docs.content')
</main>

<div id="sidebar-backdrop" class="hidden" title="Click to close sidebar" onClick="hideSidebar()"></div>
@endsection
