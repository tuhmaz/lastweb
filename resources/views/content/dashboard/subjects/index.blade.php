@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('Subjects'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('page-style')
<style>
    .accordion-button:not(.collapsed) {
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
    }
    .subject-item {
        transition: all 0.2s ease;
    }
    .subject-item:hover {
        background-color: var(--bs-gray-100);
    }
    .subject-count {
        min-width: 24px;
        height: 24px;
        display: flex;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
    window.translations = {
        'View Details': '{{ __("View Details") }}',
        'Edit': '{{ __("Edit") }}',
        'Delete': '{{ __("Delete") }}',
        'Are you sure?': '{{ __("Are you sure?") }}',
        'You will not be able to recover this!': '{{ __("You will not be able to recover this!") }}',
        'Yes, delete it!': '{{ __("Yes, delete it!") }}',
        'No, cancel!': '{{ __("No, cancel!") }}',
        'Deleted!': '{{ __("Deleted!") }}',
        'Record has been deleted.': '{{ __("Record has been deleted.") }}',
        'Error!': '{{ __("Error!") }}',
        'Something went wrong!': '{{ __("Something went wrong!") }}'
    };
</script>
@vite(['resources/js/subjects.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('School') }} /</span> {{ __('Subjects') }}
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="d-flex gap-3 flex-wrap align-items-center">
                    <div style="min-width: 250px;">
                        <form method="GET" action="{{ route('dashboard.subjects.index') }}" id="filterForm">
                            <select name="country_id" class="select2 form-select" data-placeholder="{{ __('All Countries') }}" id="countrySelect">
                                <option value="">{{ __('All Countries') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['id'] }}" {{ $selectedCountry == $country['id'] ? 'selected' : '' }}>
                                        {{ $country['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="text-muted">
                        {{ __('Total Subjects') }}: <span class="fw-semibold">{{ $totalSubjects }}</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('dashboard.subjects.create', ['country_id' => $selectedCountry]) }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('Add New Subject') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($hasError && $errorMessage)
                <div class="alert alert-warning" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ $errorMessage }}
                </div>
            @endif

            @if($groupedSubjects->isNotEmpty())
                <div class="accordion" id="accordionGrades">
                    @foreach($groupedSubjects as $group)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#grade_{{ $group['grade_level'] }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                        aria-controls="grade_{{ $group['grade_level'] }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <i class="ti ti-school me-2"></i>
                                            {{ $group['grade_name'] }}
                                        </div>
                                        <span class="badge bg-primary rounded-pill subject-count">
                                            {{ $group['subjects']->count() }}
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="grade_{{ $group['grade_level'] }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 data-bs-parent="#accordionGrades">
                                <div class="accordion-body p-0">
                                    <div class="list-group list-group-flush">
                                        @foreach($group['subjects'] as $subject)
                                            <div class="list-group-item subject-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">
                                                                <i class="ti ti-book me-2 text-primary"></i>
                                                                {{ $subject->subject_name }}
                                                            </h6>
                                                            <small class="text-muted">
                                                                <i class="ti ti-calendar-event me-1"></i>
                                                                {{ $subject->created_at->format('Y-m-d') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('dashboard.subjects.show', ['subject' => $subject->id, 'country_id' => $selectedCountry]) }}"
                                                           class="btn btn-icon btn-label-primary btn-sm"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('View Details') }}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <a href="{{ route('dashboard.subjects.edit', ['subject' => $subject->id, 'country_id' => $selectedCountry]) }}"
                                                           class="btn btn-icon btn-label-warning btn-sm"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('Edit') }}">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <form action="{{ route('dashboard.subjects.destroy', ['subject' => $subject->id, 'country_id' => $selectedCountry]) }}"
                                                              method="POST"
                                                              class="d-inline-block delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                    class="btn btn-icon btn-label-danger btn-sm delete-record"
                                                                    data-bs-toggle="tooltip"
                                                                    title="{{ __('Delete') }}"
                                                                    data-url="{{ route('dashboard.subjects.destroy', ['subject' => $subject->id, 'country_id' => $selectedCountry]) }}">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-5">
                    <img src="{{ asset('assets/img/illustrations/empty.svg') }}" alt="No Subjects" class="mb-3" style="max-width: 200px;">
                    <h4>{{ __('No Subjects Found') }}</h4>
                    <p class="mb-3">{{ __('There are no subjects available for the selected filters.') }}</p>
                    <a href="{{ route('dashboard.subjects.create', ['country_id' => $selectedCountry]) }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('Add New Subject') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
