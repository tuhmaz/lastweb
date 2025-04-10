@extends('layouts/contentNavbarLayout')

@section('title', __('Sitemap Management'))

@push('vendor-style')
@vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-sitemap me-2"></i>{{ __('Sitemap Management') }}
                    </h5>
                </div>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="alert alert-info bg-info bg-opacity-10 border-0">
                    <div class="d-flex">
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                        <div>
                            {{ __('You can generate a new sitemap to update search engines with the latest changes to your website.') }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('dashboard.sitemap.generate') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-label-primary bg-opacity-10 shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ti ti-database fs-3 me-2"></i>
                                        <h5 class="mb-0">{{ __('Select Database for Sitemap Generation') }}</h5>
                                    </div>
                                    <p class="text-muted">{{ __('Choose the database you want to generate a sitemap for. This will create XML sitemaps for articles, news, and static pages.') }}</p>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <select name="database" id="database" class="form-select form-select-lg">
                                                    <option value="" disabled selected>{{ __('Select a database...') }}</option>
                                                    <option value="jo" {{ $database == 'jo' ? 'selected' : '' }}>
                                                        <i class="flag-icon flag-icon-jo"></i> {{ __('Jordan') }}
                                                    </option>
                                                    <option value="sa" {{ $database == 'sa' ? 'selected' : '' }}>
                                                        <i class="flag-icon flag-icon-sa"></i> {{ __('Saudi Arabia') }}
                                                    </option>
                                                    <option value="eg" {{ $database == 'eg' ? 'selected' : '' }}>
                                                        <i class="flag-icon flag-icon-eg"></i> {{ __('Egypt') }}
                                                    </option>
                                                    <option value="ps" {{ $database == 'ps' ? 'selected' : '' }}>
                                                        <i class="flag-icon flag-icon-ps"></i> {{ __('Palestine') }}
                                                    </option>
                                                </select>
                                                <label for="database">{{ __('Select Database') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary btn-lg w-100 h-100">
                                                <i class="ti ti-refresh me-2"></i>{{ __('Generate Sitemap') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <h5 class="mb-3">
                    <i class="ti ti-files me-2"></i>{{ __('Available Sitemap Links') }}
                </h5>

                @php
                    $databases = ['jo', 'sa', 'eg', 'ps'];
                    $dbNames = [
                        'jo' => 'Jordan',
                        'sa' => 'Saudi Arabia',
                        'eg' => 'Egypt',
                        'ps' => 'Palestine'
                    ];
                @endphp

                <div class="accordion" id="sitemapAccordion">
                    @foreach($databases as $db)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $db }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $db }}">
                                    <i class="ti ti-world me-2"></i>{{ __('Sitemap for') }} {{ $dbNames[$db] }}
                                </button>
                            </h2>
                            <div id="collapse{{ $db }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 data-bs-parent="#sitemapAccordion">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>{{ __('Sitemap Type') }}</th>
                                                    <th>{{ __('Last Updated') }}</th>
                                                    <th class="text-end">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset('storage/sitemaps/sitemap_articles_' . $db . '.xml') }}" 
                                                           class="d-flex align-items-center text-body" target="_blank">
                                                            <i class="ti ti-article me-2"></i>
                                                            <span>{{ __('Articles Sitemap') }}</span>
                                                            <i class="ti ti-external-link ms-2 text-muted"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if(isset($sitemapData['articles']) && $sitemapData['articles']['exists'])
                                                            <span class="text-muted">
                                                                <i class="ti ti-clock me-1"></i>
                                                                {{ \Carbon\Carbon::createFromTimestamp($sitemapData['articles']['last_modified'])->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-warning">{{ __('Not Generated') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('dashboard.sitemap.delete', ['type' => 'articles', 'database' => $db]) }}" 
                                                              method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('{{ __('Are you sure you want to delete this sitemap?') }}')">
                                                                <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset('storage/sitemaps/sitemap_news_' . $db . '.xml') }}" 
                                                           class="d-flex align-items-center text-body" target="_blank">
                                                            <i class="ti ti-news me-2"></i>
                                                            <span>{{ __('News Sitemap') }}</span>
                                                            <i class="ti ti-external-link ms-2 text-muted"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if(isset($sitemapData['news']) && $sitemapData['news']['exists'])
                                                            <span class="text-muted">
                                                                <i class="ti ti-clock me-1"></i>
                                                                {{ \Carbon\Carbon::createFromTimestamp($sitemapData['news']['last_modified'])->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-warning">{{ __('Not Generated') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('dashboard.sitemap.delete', ['type' => 'news', 'database' => $db]) }}" 
                                                              method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('{{ __('Are you sure you want to delete this sitemap?') }}')">
                                                                <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset('storage/sitemaps/sitemap_static_' . $db . '.xml') }}" 
                                                           class="d-flex align-items-center text-body" target="_blank">
                                                            <i class="ti ti-file me-2"></i>
                                                            <span>{{ __('Static Pages Sitemap') }}</span>
                                                            <i class="ti ti-external-link ms-2 text-muted"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if(isset($sitemapData['static']) && $sitemapData['static']['exists'])
                                                            <span class="text-muted">
                                                                <i class="ti ti-clock me-1"></i>
                                                                {{ \Carbon\Carbon::createFromTimestamp($sitemapData['static']['last_modified'])->diffForHumans() }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-warning">{{ __('Not Generated') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('dashboard.sitemap.delete', ['type' => 'static', 'database' => $db]) }}" 
                                                              method="POST" class="d-inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('{{ __('Are you sure you want to delete this sitemap?') }}')">
                                                                <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('vendor-script')
@vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endpush
