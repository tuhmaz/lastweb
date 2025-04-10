@foreach($users as $user)
<tr>
    <td>
        <div class="d-flex justify-content-start align-items-center user-name">
            <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-3">
                    @if($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Avatar" class="rounded-circle">
                    @else
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    @endif
                    @if($user->isOnline())
                        <span class="avatar-status bg-success"></span>
                    @else
                        <span class="avatar-status bg-secondary"></span>
                    @endif
                </div>
            </div>
            <div class="d-flex flex-column">
                <a href="{{ route('dashboard.users.show', $user) }}" class="text-body text-truncate">
                    <span class="fw-semibold">{{ $user->name }}</span>
                </a>
                <small class="text-muted">{{ $user->email }}</small>
            </div>
        </div>
    </td>
    <td>
        @foreach($user->roles as $role)
            <span class="badge bg-label-primary">{{ $role->name }}</span>
        @endforeach
    </td>
    <td>
        @if($user->isOnline())
            <span class="badge bg-success">{{ __('Online') }}</span>
        @else
            <span class="badge bg-secondary">{{ __('Offline') }}</span>
            @if($user->last_activity)
                <small class="text-muted d-block">{{ __('Last seen') }}: {{ $user->last_activity->diffForHumans() }}</small>
            @endif
        @endif
    </td>
    <td>
        <div class="d-inline-block">
            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="ti ti-dots-vertical"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('dashboard.users.show', $user) }}">
                    <i class="ti ti-eye me-1"></i> {{ __('View') }}
                </a>
                <a class="dropdown-item" href="{{ route('dashboard.users.edit', $user) }}">
                    <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                </a>
                <a class="dropdown-item" href="{{ route('dashboard.users.permissions-roles', $user) }}">
                    <i class="ti ti-lock me-1"></i> {{ __('Permissions') }}
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger delete-record">
                        <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </td>
</tr>
@endforeach