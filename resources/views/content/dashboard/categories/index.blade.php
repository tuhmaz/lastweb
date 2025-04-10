@extends('layouts.contentNavbarLayout')

@section('title', __('Categories Management'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <h5 class="mb-0">{{ __('Categories') }}</h5>
          <select class="form-select country-select" onchange="window.location.href='{{ route('dashboard.categories.index') }}?country=' + this.value">
            @foreach($countries as $code => $name)
              <option value="{{ $code }}" {{ $country == $code ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
          </select>
        </div>
        <a href="{{ route('dashboard.categories.create', ['country' => $country]) }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i>{{ __('Add New Category') }}
        </a>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('News Count') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $category)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $category->name }}</td>
                  <td>{{ $category->slug }}</td>
                  <td>
                    <span class="badge bg-label-info">
                      {{ $category->news_count }} {{ __('News') }}
                    </span>
                  </td>
                  <td>
                    <div class="form-check form-switch">
                      <input 
                        type="checkbox" 
                        class="form-check-input toggle-status" 
                        data-id="{{ $category->id }}"
                        data-url="{{ route('dashboard.categories.toggle-status', ['category' => $category->id, 'country' => $country]) }}"
                        {{ $category->is_active ? 'checked' : '' }}
                      >
                    </div>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('dashboard.categories.edit', ['category' => $category->id, 'country' => $country]) }}">
                          <i class="ti ti-pencil me-1"></i>
                          {{ __('Edit') }}
                        </a>
                        <form action="{{ route('dashboard.categories.destroy', ['category' => $category->id, 'country' => $country]) }}" 
                              method="POST" 
                              class="d-inline delete-form"
                              data-name="{{ $category->name }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item">
                            <i class="ti ti-trash me-1"></i>
                            {{ __('Delete') }}
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">{{ __('No categories found') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
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

@push('page-script')
@vite(['resources/assets/vendor/js/categories.js'])
@endpush
