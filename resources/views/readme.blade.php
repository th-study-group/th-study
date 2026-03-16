@extends('layouts.app')

@section('title', 'README.md')

@section('style')
    <style>
        .readme-wrap {
            line-height: 1.7;
            word-break: break-word;
        }
        .readme-wrap img {
            max-width: 100%;
            height: auto;
        }
        .readme-wrap pre {
            overflow: auto;
            padding: 12px;
            border-radius: 8px;
            background: #111827;
            color: #e5e7eb;
        }
        .readme-wrap table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    @php
        $path = base_path('README.md');
        $markdown = file_exists($path) ? file_get_contents($path) : '# README.md not found';
        $html = \Illuminate\Support\Str::markdown($markdown);
    @endphp

    <section class="col-12 col-lg-8 mx-auto">
        <div class="readme-wrap bg-white rounded-3 p-3 p-lg-4 shadow-sm">
            {!! $html !!}
        </div>
    </section>
@endsection
