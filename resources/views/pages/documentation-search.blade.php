@php
    $page = Hyde\Framework\Models\Pages\DocumentationPage::make('search', ['title' => 'Search']);
    $title = 'Search';
    $currentPage = $page->getCurrentPagePath();
    $markdown = '';
@endphp

@extends('hyde::layouts.docs')
@section('content')
    <h1>Search the documentation site</h1>
    <style>#searchMenuButton, .edit-page-link {
            display: none !important;
        }

        #search-results {
            max-height: unset !important;1
        }</style>
    <x-hyde::docs.search-input class="max-w-xs border-b-4 border-indigo-400" />
@endsection
