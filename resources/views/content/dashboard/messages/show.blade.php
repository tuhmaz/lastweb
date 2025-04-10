@extends('layouts/contentNavbarLayout')

@section('title', __('View Message'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/scss/pages/app-email.scss'
])
<style>
.message-content {
  background: #fff;
  border-radius: 0.5rem;
  padding: 2rem;
  margin-bottom: 1rem;
}
.message-header {
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}
.message-body {
  font-size: 1rem;
  line-height: 1.6;
}
.message-meta {
  color: #6c757d;
  font-size: 0.875rem;
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-body">
      <div class="message-content">
        <div class="message-header">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">{{ $message->subject }}</h5>
            <a href="{{ route('dashboard.messages.index') }}" class="btn btn-label-secondary">
              <i class="ti ti-arrow-left me-1"></i>{{ __('Back to Inbox') }}
            </a>
          </div>
          <div class="message-meta">
            <span class="me-3">
              <strong>{{ __('From') }}:</strong> {{ $message->sender->name }}
            </span>
            <span class="me-3">
              <strong>{{ __('Date') }}:</strong> {{ $message->created_at->format('M d, Y H:i') }}
            </span>
          </div>
        </div>
        
        <div class="message-body">
          {!! $message->body !!}
        </div>
      </div>

      <div class="mt-4">
        <button type="button" class="btn btn-primary me-2" onclick="window.location.href='{{ route('dashboard.messages.compose') }}?reply_to={{ $message->id }}'">
          <i class="ti ti-reply me-1"></i>{{ __('Reply') }}
        </button>
        <button type="button" class="btn btn-label-danger" onclick="deleteMessage({{ $message->id }})">
          <i class="ti ti-trash me-1"></i>{{ __('Delete') }}
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
function deleteMessage(id) {
  if (confirm('{{ __("Are you sure you want to delete this message?") }}')) {
    // يمكن إضافة منطق الحذف هنا
  }
}
</script>
@endsection
