<div class="d-inline-block">
    <a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <a href="{{ route('dashboard.news.edit', ['news' => $news->id]) }}" class="dropdown-item">
            <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
        </a>
        <a href="javascript:;" class="dropdown-item delete-record" data-id="{{ $news->id }}" data-country="{{ $news->country }}">
            <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
        </a>
    </div>
</div>
