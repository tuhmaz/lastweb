@extends('layouts/contentNavbarLayout')

@section('title', __('Permissions Management'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Permissions') }} /</span> {{ __('Permissions List') }}
</h4>

<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-3">{{ __('Permissions List') }}</h5>
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
                            <button type="button" class="dt-button add-new btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1"></i>
                                    <span class="d-none d-sm-inline-block">{{ __('Add New Permission') }}</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="datatables-permissions table border-top dataTable no-footer dtr-column">
                <thead>
                    <tr>
                        <th>{{ __('Permission Name') }}</th>
                        <th>{{ __('Guard Name') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->guard_name }}</td>
                        <td>{{ $permission->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-inline-block">
                                <button type="button" class="btn btn-sm btn-icon edit-record"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPermissionModal"
                                        data-id="{{ $permission->id }}"
                                        data-name="{{ $permission->name }}">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <form action="{{ route('dashboard.permissions.destroy', $permission) }}" method="POST" class="d-inline-block">
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

<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Permission') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dashboard.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- رسائل الخطأ -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="permission-name">{{ __('Permission Name') }}</label>
                            <input type="text"
                                   id="permission-name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="{{ __('Enter permission name') }}"
                                   value="{{ old('name') }}"
                                   required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label" for="guard-name">{{ __('Guard Name') }}</label>
                            <select id="guard-name"
                                    name="guard_name"
                                    class="form-select @error('guard_name') is-invalid @enderror"
                                    required>
                                <option value="sanctum" @selected(old('guard_name') == 'sanctum')>Sanctum</option>
                                <option value="web" @selected(old('guard_name') == 'web')>Web</option>
                                <option value="api" @selected(old('guard_name') == 'api')>API</option>
                            </select>
                            @error('guard_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edit Permission') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="edit-permission-name">{{ __('Permission Name') }}</label>
                            <input type="text" id="edit-permission-name" name="name" class="form-control" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
const editPermissionModal = document.getElementById('editPermissionModal');
if (editPermissionModal) {
    editPermissionModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const permissionId = button.getAttribute('data-id');
        const permissionName = button.getAttribute('data-name');

        const form = this.querySelector('#editPermissionForm');
        const nameInput = this.querySelector('#edit-permission-name');

        form.action = `/dashboard/permissions/${permissionId}`;
        nameInput.value = permissionName;
    });
}

    // Delete confirmation
    document.querySelectorAll('.delete-record').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("You won\'t be able to revert this!") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("Yes, delete it!") }}',
                cancelButtonText: '{{ __("Cancel") }}',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
