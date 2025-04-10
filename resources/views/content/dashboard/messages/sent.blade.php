@extends('layouts/contentNavbarLayout')

@section('title', __('Sent Messages'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/scss/pages/app-email.scss'
])
<style>
.email-list {
  position: relative;
  height: calc(100vh - 15rem);
}
.email-list .email-list-item {
  padding: 1rem;
  transition: all 0.2s ease-in-out;
  margin: 0.25rem 0;
  border-radius: 0.5rem;
}
.email-list .email-list-item:hover {
  background-color: rgba(67, 89, 113, 0.04);
  cursor: pointer;
}
.email-list .email-list-item .email-list-item-username {
  font-weight: 500;
}
.email-navigation-list .navigation-item {
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease-in-out;
  margin: 0.25rem 0;
}
.email-navigation-list .navigation-item:hover {
  background-color: rgba(67, 89, 113, 0.04);
}
.email-navigation-list .navigation-item.active {
  background-color: #696cff;
  color: #fff;
}
.email-navigation-list .navigation-item.active .badge {
  background-color: #fff !important;
  color: #696cff !important;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js'
])
@endsection



@section('content')
<div class="email-wrapper container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="row g-0">
      <!-- Navigation -->
      <div class="col-12 col-lg-3 email-navigation border-end">
        <div class="d-grid gap-2 p-4">
          <a href="{{ route('dashboard.messages.compose') }}" class="btn btn-primary btn-lg waves-effect waves-light">
            <i class="ti ti-plus me-1"></i>{{ __('Compose') }}
          </a>
        </div>
        <div class="email-navigation-list ps ps--active-y">
          <a href="{{ route('dashboard.messages.index') }}" class="d-flex align-items-center navigation-item {{ request()->routeIs('dashboard.messages.index') ? 'active' : '' }}">
            <i class="ti ti-mail me-2"></i>
            <span>{{ __('Inbox') }}</span>
            @if($unreadMessagesCount > 0)
              <span class="badge bg-label-primary rounded-pill ms-auto">{{ $unreadMessagesCount }}</span>
            @endif
          </a>
          <a href="{{ route('dashboard.messages.sent') }}" class="d-flex align-items-center navigation-item {{ request()->routeIs('dashboard.messages.sent') ? 'active' : '' }}">
            <i class="ti ti-send me-2"></i>
            <span>{{ __('Sent') }}</span>
            @if($sentMessagesCount > 0)
              <span class="badge bg-label-secondary rounded-pill ms-auto">{{ $sentMessagesCount }}</span>
            @endif
          </a>
        </div>
      </div>

      <!-- Messages List -->
      <div class="col-12 col-lg-9 email-list">
        <div class="card shadow-none border-0">
          <div class="card-body email-list-wrapper p-0">
            <!-- Search -->
            <div class="email-list-item d-flex align-items-center bg-lighter px-3 py-2">
              <div class="email-list-item-content ms-2 ms-sm-4 me-2 w-100">
                <div class="input-group input-group-merge">
                  <span class="input-group-text" id="basic-addon1"><i class="ti ti-search"></i></span>
                  <input type="text" class="form-control email-search" placeholder="{{ __('Search sent mail') }}">
                </div>
              </div>
            </div>
            <hr class="my-0">

            <!-- Messages -->
            @forelse($sentMessages as $message)
  <div class="email-list-item d-flex align-items-center">
    <div class="email-list-item-content ms-2 ms-sm-4 me-2">
      <span class="email-list-item-username me-2 h6">
        {{ $message->conversation->user1_id == auth()->id()
           ? $message->conversation->user2->name
           : $message->conversation->user1->name }}
      </span>
      <span class="email-list-item-subject d-xl-inline-block d-block">
        {{ $message->subject }}
      </span>
    </div>
    <div class="email-list-item-meta ms-auto d-flex align-items-center">
      <span class="email-list-item-time">{{ $message->created_at->format('M d') }}</span>
      <div class="ms-3">
        @if($message->is_important)
          <i class="ti ti-star text-warning"></i>
        @endif
      </div>
      <!-- زر الحذف -->
      <form action="{{ route('dashboard.messages.delete', $message->id) }}" method="POST" class="ms-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to delete this message?') }}')">
          <i class="ti ti-trash"></i>
        </button>
      </form>
    </div>
  </div>
  <hr class="my-0">
@empty
  <div class="text-center p-5">
    <i class="ti ti-send mb-2" style="font-size: 3rem;"></i>
    <h5>{{ __('No sent messages') }}</h5>
    <p class="mb-0">{{ __('Start sending messages to see them here') }}</p>
  </div>
@endforelse

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
