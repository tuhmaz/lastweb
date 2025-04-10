@extends('layouts.layoutFront')

@section('title', $category->name . ' - ' . __('News'))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('content.frontend.news.index', ['database' => $database]) }}">{{ __('News') }}</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </nav>

            <h2 class="mb-4">{{ __('News in') }} {{ $category->name }}</h2>
        </div>
    </div>

    <div class="row">
        @forelse($news as $item)
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}"
                             style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($item->excerpt, 150) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('content.frontend.news.show', ['database' => $database, 'id' => $item->id]) }}" 
                               class="btn btn-outline-primary">{{ __('Read More') }}</a>
                            <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    {{ __('No news available in this category.') }}
                </div>
            </div>
        @endforelse
    </div>

    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $news->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
