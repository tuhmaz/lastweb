@extends('layouts.contentNavbarLayout')

@section('title', __('Semesters'))

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">{{ __('Semesters') }} /</span> {{ __('List') }}
</h4>

<div class="card">
  <div class="card-header">
    <div class="d-flex justify-content-between align-items-center row">
      <div class="col-md-4">
        <h5 class="mb-0">{{ __('Semesters') }} ({{ __(ucfirst($country)) }})</h5>
      </div>
      <div class="col-md-4">
        <select class="form-select" id="countryFilter" onchange="window.location.href=this.value">
          <option value="{{ route('dashboard.semesters.index', ['country' => 'jordan']) }}" 
                  {{ $country == 'jordan' ? 'selected' : '' }}>
            {{ __('Jordan') }}
          </option>
          <option value="{{ route('dashboard.semesters.index', ['country' => 'saudi']) }}"
                  {{ $country == 'saudi' ? 'selected' : '' }}>
            {{ __('Saudi Arabia') }}
          </option>
          <option value="{{ route('dashboard.semesters.index', ['country' => 'egypt']) }}"
                  {{ $country == 'egypt' ? 'selected' : '' }}>
            {{ __('Egypt') }}
          </option>
          <option value="{{ route('dashboard.semesters.index', ['country' => 'palestine']) }}"
                  {{ $country == 'palestine' ? 'selected' : '' }}>
            {{ __('Palestine') }}
          </option>
        </select>
      </div>
      <div class="col-md-4 text-end">
        <a href="{{ route('dashboard.semesters.create', ['country' => $country]) }}" class="btn btn-primary">
          <i class="ti ti-plus me-1"></i>
          {{ __('Add New Semester') }}
        </a>
      </div>
    </div>
  </div>
  
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible mb-3" role="alert">
        {{ __(session('success')) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @forelse($groupedSemesters as $gradeName => $semesters)
      <div class="mb-4">
        <h6 class="fw-bold mb-3">{{ $gradeName }}</h6>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>{{ __('Semester Name') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($semesters as $semester)
                <tr>
                  <td>{{ $semester->semester_name }}</td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="{{ route('dashboard.semesters.show', ['semester' => $semester->id, 'country' => $country]) }}" 
                         class="btn btn-info btn-sm">
                        <i class="ti ti-eye"></i>
                      </a>
                      <a href="{{ route('dashboard.semesters.edit', ['semester' => $semester->id, 'country' => $country]) }}" 
                         class="btn btn-warning btn-sm">
                        <i class="ti ti-pencil"></i>
                      </a>
                      <form action="{{ route('dashboard.semesters.destroy', ['semester' => $semester->id, 'country' => $country]) }}" 
                            method="POST" 
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this semester?') }}');"
                            style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                          <i class="ti ti-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @empty
      <div class="text-center py-5">
        <h6 class="text-muted">{{ __('No semesters found') }}</h6>
      </div>
    @endforelse
  </div>
</div>

@push('page-script')
<script>
  // Add animation when changing country
  document.getElementById('countryFilter').addEventListener('change', function() {
    document.querySelector('.card-body').style.opacity = '0.5';
  });
</script>
@endpush

@endsection
