@extends('layouts/contentNavbarLayout')

@section('title', __('Roles Management'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Roles') }} /</span> {{ __('Roles List') }}
</h4>

<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-3">{{ __('Roles List') }}</h5>
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
    </div>
    <div class="card-datatable table-responsive">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="row mx-2">
                <div class="col-md-2">
                    <div class="me-3">
                        <div class="dataTables_length" id="DataTables_Table_0_length">
                            <label>{{ __('Show') }}
                                <select name="DataTables_Table_0_length" class="form-select">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                        <div class="dt-buttons">
                            <a href="{{ route('dashboard.roles.create') }}" class="dt-button add-new btn btn-primary">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">{{ __('Add New Role') }}</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <table class="datatables-users table border-top dataTable no-footer dtr-column">
                <thead>
                    <tr>
                        <th>{{ __('Role Name') }}</th>
                        <th>{{ __('Users Count') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>{{ $role->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-inline-block">
                                <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-sm btn-icon">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form action="{{ route('dashboard.roles.destroy', $role) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon delete-record">
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
