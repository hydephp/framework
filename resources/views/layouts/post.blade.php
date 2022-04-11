{{-- The Post Page Layout --}}
@extends('hyde::layouts.app')
@section('content')

@php
$title = $post->matter['title'] ?? false;
$description = $post->matter['description'] ?? false;
$category = $post->matter['category'] ?? false;
$author = $post->matter['author'] ?? false;
@endphp

@push('meta')
<!-- Blog Post Meta Tags -->
@foreach ($post->getMetadata() as $name => $content)
    <meta name="{{ $name }}" content="{{ $content }}">
@endforeach
@foreach ($post->getMetaProperties() as $name => $content)
    <meta property="{{ $name }}" content="{{ $content }}">
@endforeach
@endpush

<main class="mx-auto max-w-7xl py-16 px-8">
	@include('hyde::components.post.article')
</main>

@endsection