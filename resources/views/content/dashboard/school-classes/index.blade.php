@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', __('School Classes'))

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/css/pages/school-classes.css'])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/select2/select2.js'
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
@vite(['resources/js/school-classes.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('School') }} /</span> {{ __('Classes') }}
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="d-flex gap-3 flex-wrap align-items-center">
                    <div style="min-width: 250px;">
                        <form method="GET" action="{{ route('dashboard.school-classes.index') }}" id="filterForm">
                            <select name="country_id" class="select2 form-select" data-placeholder="{{ __('All Countries') }}" id="countrySelect">
                                <option value="">{{ __('All Countries') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ $selectedCountry == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="text-muted">
                        {{ __('Total Classes') }}: <span class="fw-semibold">{{ $classes->count() }}</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('dashboard.school-classes.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        {{ __('Add New Class') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="datatables-classes table border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Grade Name') }}</th>
                        <th>{{ __('Grade Level') }}</th>
                        <th>{{ __('Country') }}</th>
                        <th>{{ __('Subjects') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $index => $class)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $class->grade_name }}</td>
                        <td><span class="badge bg-label-primary">{{ $class->grade_level }}</span></td>
                        <td>{{ $class->country ? $class->country->name : __('N/A') }}</td>
                        <td><span class="badge bg-label-info">{{ $class->subjects->count() }}</span></td>
                        <td>{{ $class->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex">
                                <a href="{{ route('dashboard.school-classes.show', ['school_class' => $class->id, 'country_id' => $selectedCountry]) }}"
                                   class="btn btn-sm btn-icon btn-label-primary me-1"
                                   data-bs-toggle="tooltip"
                                   title="{{ __('View Details') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('dashboard.school-classes.edit', ['school_class' => $class->id, 'country_id' => $selectedCountry]) }}"
                                   class="btn btn-sm btn-icon btn-label-warning me-1"
                                   data-bs-toggle="tooltip"
                                   title="{{ __('Edit') }}">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('dashboard.school-classes.destroy', ['school_class' => $class->id, 'country_id' => $selectedCountry]) }}"
                                      method="POST"
                                      class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-icon btn-label-danger delete-record"
                                            data-bs-toggle="tooltip"
                                            title="{{ __('Delete') }}">
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
</div>
@endsection
