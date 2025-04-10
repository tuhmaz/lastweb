@extends('layouts/contentNavbarLayout')

@section('title', 'سجلات تقييد معدل الطلبات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">سجلات تقييد معدل الطلبات</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#blockIpModal">
                            <i class="fas fa-ban"></i> حظر عنوان IP
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('dashboard.security.rate-limit-logs.index') }}" method="GET" class="form-inline">
                                <div class="input-group mr-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ $search }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <select name="filter" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>جميع السجلات</option>
                                    <option value="blocked" {{ $filter == 'blocked' ? 'selected' : '' }}>محظور حاليًا</option>
                                    <option value="expired" {{ $filter == 'expired' ? 'selected' : '' }}>انتهى الحظر</option>
                                </select>
                                <select name="per_page" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 سجل</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30 سجل</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 سجل</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 سجل</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <form action="{{ route('dashboard.security.rate-limit-logs.destroy-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات المحددة؟')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="filter" value="{{ $filter }}">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> حذف جميع السجلات المحددة
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'id', 'sort_order' => $sortBy == 'id' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            #
                                            @if ($sortBy == 'id')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'ip_address', 'sort_order' => $sortBy == 'ip_address' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            عنوان IP
                                            @if ($sortBy == 'ip_address')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>المستخدم</th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'route', 'sort_order' => $sortBy == 'route' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            المسار
                                            @if ($sortBy == 'route')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'method', 'sort_order' => $sortBy == 'method' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            الطريقة
                                            @if ($sortBy == 'method')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'attempts', 'sort_order' => $sortBy == 'attempts' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            المحاولات
                                            @if ($sortBy == 'attempts')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'limit', 'sort_order' => $sortBy == 'limit' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            الحد
                                            @if ($sortBy == 'limit')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'blocked_until', 'sort_order' => $sortBy == 'blocked_until' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            محظور حتى
                                            @if ($sortBy == 'blocked_until')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('dashboard.security.rate-limit-logs.index', ['sort_by' => 'created_at', 'sort_order' => $sortBy == 'created_at' && $sortOrder == 'asc' ? 'desc' : 'asc', 'filter' => $filter, 'search' => $search, 'per_page' => $perPage]) }}">
                                            تاريخ التسجيل
                                            @if ($sortBy == 'created_at')
                                                <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                <tr class="{{ $log->blocked_until && $log->blocked_until > now() ? 'table-danger' : '' }}">
                                    <td>{{ $log->id }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        @if ($log->user)
                                            <a href="{{ route('dashboard.users.edit', $log->user) }}">{{ $log->user->name }}</a>
                                        @else
                                            <span class="text-muted">زائر</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->route }}</td>
                                    <td>{{ $log->method }}</td>
                                    <td>{{ $log->attempts }}</td>
                                    <td>{{ $log->limit }}</td>
                                    <td>
                                        @if ($log->blocked_until)
                                            <span class="{{ $log->blocked_until > now() ? 'text-danger' : 'text-success' }}">
                                                {{ $log->blocked_until->format('Y-m-d H:i:s') }}
                                                @if ($log->blocked_until > now())
                                                    <br><small>(متبقي: {{ now()->diffForHumans($log->blocked_until, true) }})</small>
                                                @else
                                                    <br><small>(انتهى منذ: {{ $log->blocked_until->diffForHumans() }})</small>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <form action="{{ route('dashboard.security.rate-limit-logs.destroy', $log) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">لا توجد سجلات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $logs->appends(['filter' => $filter, 'search' => $search, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لحظر عنوان IP -->
<div class="modal fade" id="blockIpModal" tabindex="-1" role="dialog" aria-labelledby="blockIpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dashboard.security.rate-limit-logs.block-ip') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="blockIpModalLabel">حظر عنوان IP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="ip_address">عنوان IP</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" required placeholder="مثال: 192.168.1.1">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="duration">مدة الحظر</label>
                            <input type="number" class="form-control" id="duration" name="duration" required min="1" value="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="duration_unit">وحدة المدة</label>
                            <select class="form-control" id="duration_unit" name="duration_unit" required>
                                <option value="minutes">دقائق</option>
                                <option value="hours" selected>ساعات</option>
                                <option value="days">أيام</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حظر</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
