@php
use Illuminate\Support\Str;
$defaultAvatar = 'assets/img/avatars/default.png';
@endphp

<ul class="timeline timeline-center mt-3">
    @forelse($activities as $activity)
        <li class="timeline-item">
            <span class="timeline-indicator
                @if($activity['type'] === 'article')
                    bg-primary
                @elseif($activity['type'] === 'news')
                    bg-info
                @elseif($activity['type'] === 'comment')
                    bg-warning
                @else
                    bg-secondary
                @endif
            ">
                <i class="bx {{ $activity['icon'] }}"></i>
            </span>
            <div class="timeline-event">
                <div class="timeline-header">
                    <h6 class="mb-0">{{ $activity['action'] }}</h6>
                    <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                </div>
                <div class="timeline-body">
                    <p class="mb-2">{{ Str::limit($activity['description'] ?? '', 100) }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <img src="{{ $activity['user_avatar'] ?? asset($defaultAvatar) }}"
                         class="rounded-circle me-2"
                         width="24"
                         height="24"
                         alt="{{ $activity['user'] }}">
                    <small>{{ $activity['user'] }}</small>
                </div>
            </div>
        </li>
    @empty
        <li class="text-center py-4">
            <p class="mb-0">لا توجد نشاطات حديثة</p>
        </li>
    @endforelse
</ul>
