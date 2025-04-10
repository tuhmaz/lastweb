@extends('layouts/layoutMaster')

@section('title', __('Notifications'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/spinkit/spinkit.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Dashboard') }} /</span> {{ __('Notifications') }}
    </h4>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                <div class="col-md-4 notification_status">
                    <select class="form-select" id="filterNotification">
                        <option value="all">{{ __('All') }}</option>
                        <option value="unread">{{ __('Unread') }}</option>
                        <option value="read">{{ __('Read') }}</option>
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-primary me-2 mark-all-read" href="{{ route('dashboard.notifications.mark-all-as-read') }}">
                        <i class="ti ti-mail-opened me-1"></i> {{ __('Mark all as read') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="deleteSelected">
                        <i class="ti ti-trash me-1"></i> {{ __('Delete Selected') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="notificationsForm" action="{{ route('dashboard.notifications.handle-actions') }}" method="POST">
                @csrf
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        <div class="notification-item d-flex mb-3 pb-2 border-bottom {{ $notification->read_at ? '' : 'unread' }}">
                            <div class="flex-shrink-0 me-3">
                                <div class="form-check">
                                    <input class="form-check-input notification-check" type="checkbox" name="selected_notifications[]" value="{{ $notification->id }}">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 me-auto">{{ $notification->data['title'] ?? __('Notification') }}</h6>
                                    <small class="text-muted ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                <div class="d-flex align-items-center">
                                    @if(!$notification->read_at)
                                        <a href="{{ route('dashboard.notifications.mark-as-read', $notification->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                            {{ __('Mark as read') }}
                                        </a>
                                    @endif
                                    @if(isset($notification->data['action_url']))
                                        <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary me-2">
                                            {{ $notification->data['action_text'] ?? __('View') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('dashboard.notifications.delete', $notification->id) }}" class="btn btn-sm btn-outline-danger delete-notification">
                                        {{ __('Delete') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="ti ti-bell-off ti-3x text-muted"></i>
                            </div>
                            <h6 class="mb-0">{{ __('No notifications found') }}</h6>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة SweetAlert2 مع التصميم المخصص
    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000,
        customClass: {
            popup: 'animated fadeInDown'
        }
    });

    // تهيئة التأكيد المخصص
    const CustomSwal = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false,
        reverseButtons: true
    });

    // تحديد الكل كمقروء
    document.querySelector('.mark-all-read').addEventListener('click', function(e) {
        e.preventDefault();
        CustomSwal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Are you sure you want to mark all notifications as read?") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, mark all!") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            padding: '2em'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = this.getAttribute('href');
            }
        });
    });

    // حذف الإشعارات المحددة
    document.querySelector('#deleteSelected').addEventListener('click', function(e) {
        e.preventDefault();
        const selectedCount = document.querySelectorAll('input[name="selected_notifications[]"]:checked').length;
        
        if (selectedCount === 0) {
            Toast.fire({
                icon: 'warning',
                title: '{{ __("Please select at least one notification") }}',
                padding: '1em'
            });
            return;
        }

        CustomSwal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("Are you sure you want to delete selected notifications?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete!") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            padding: '2em'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('#notificationsForm').action = "{{ route('dashboard.notifications.delete-selected') }}";
                document.querySelector('#notificationsForm').submit();
            }
        });
    });

    // حذف إشعار واحد
    document.querySelectorAll('.delete-notification').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            CustomSwal.fire({
                title: '{{ __("Are you sure?") }}',
                text: '{{ __("Are you sure you want to delete this notification?") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __("Yes, delete!") }}',
                cancelButtonText: '{{ __("Cancel") }}',
                padding: '2em'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.getAttribute('href');
                }
            });
        });
    });

    // عرض رسائل النجاح
    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            padding: '1em'
        });
    @endif

    // عرض رسائل الخطأ
    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            padding: '1em'
        });
    @endif
});
</script>
@endsection
