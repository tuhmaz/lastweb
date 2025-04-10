@extends('layouts/contentNavbarLayout')

@section('title', __('Messages'))

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/scss/pages/app-email.scss',
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss'
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
  .email-list .email-list-item.email-unread {
    background-color: rgba(67, 89, 113, 0.02);
  }
  .email-list .email-list-item.email-unread .email-list-item-username,
  .email-list .email-list-item.email-unread .email-list-item-subject {
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

  /* Modal Styles */
  .message-modal .modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  }
  .message-modal .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
  }
  .message-modal .modal-title {
    color: #566a7f;
    font-weight: 600;
    margin: 0;
  }
  .message-modal .modal-body {
    padding: 1.5rem;
  }
  .message-modal .message-info {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
  }
  .message-modal .sender-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-right: 1rem;
  }
  .message-modal .sender-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #566a7f;
    margin-bottom: 0.25rem;
  }
  .message-modal .message-timestamp {
    font-size: 0.875rem;
    color: #a1acb8;
  }
  .message-modal .message-content {
    background-color: #fff;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1rem;
    border: 1px solid #e9ecef;
  }
  .message-modal .modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
  }
  .message-modal .btn-reply {
    background-color: #696cff;
    border-color: #696cff;
    padding: 0.5rem 1.5rem;
  }
  .message-modal .btn-reply:hover {
    background-color: #5f65f4;
    border-color: #5f65f4;
  }
  .message-modal .message-reply {
    margin-top: 1.5rem;
    border-top: 1px solid #e9ecef;
    padding-top: 1.5rem;
  }
  .message-modal .message-reply-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
  }
  .message-modal .message-reply-toggle {
    color: #696cff;
    cursor: pointer;
    font-weight: 500;
  }
  .message-modal .message-reply-toggle:hover {
    text-decoration: underline;
  }
  .message-modal .message-reply-form {
    display: none;
  }
  .message-modal .message-reply-form.show {
    display: block;
  }
  .message-modal .message-reply-editor {
    height: 150px;
    margin-bottom: 1rem;
  }
  .message-modal .ql-toolbar.ql-snow,
  .message-modal .ql-container.ql-snow {
    border-color: #e9ecef;
  }
  .message-modal .ql-editor {
    min-height: 100px;
  }
  .message-modal .quick-reply-btn {
    background-color: #696cff;
    border-color: #696cff;
  }
  .message-modal .quick-reply-btn:hover {
    background-color: #5f65f4;
    border-color: #5f65f4;
  }
  .message-modal .quick-reply-btn:disabled {
    background-color: #b3b5ff;
    border-color: #b3b5ff;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-email.js'])
@endsection

@section('content')
<script>
  const sendMessageRoute = "{{ route('dashboard.messages.send') }}";
</script>

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
                  <input type="text" class="form-control email-search" placeholder="{{ __('Search mail') }}">
                </div>
              </div>
            </div>
            <hr class="my-0">

            <!-- Messages -->
            @forelse($messages as $message)
              <div class="email-list-item d-flex align-items-center {{ $message->read ? '' : 'email-unread' }}" 
                   data-bs-toggle="modal" 
                   data-bs-target="#messageModal{{ $message->id }}">
                <div class="email-list-item-content ms-2 ms-sm-4 me-2">
                  <span class="email-list-item-username me-2 h6">{{ $message->sender->name }}</span>
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
                </div>
              </div>
              
              <!-- Message Modal -->
              <div class="modal fade message-modal" id="messageModal{{ $message->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        <i class="ti ti-mail me-2"></i>
                        {{ $message->subject }}
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="message-info d-flex align-items-center">
                        <div class="d-flex align-items-center flex-grow-1">
                          <img src="{{ $message->sender->getAvatarUrl() }}" alt="{{ $message->sender->name }}" class="sender-avatar">
                          <div>
                            <h6 class="sender-name mb-0">{{ $message->sender->name }}</h6>
                            <div class="message-timestamp">
                              <i class="ti ti-calendar me-1"></i>
                              {{ $message->created_at->format('Y-m-d') }}
                              <i class="ti ti-clock ms-2 me-1"></i>
                              {{ $message->created_at->format('H:i') }}
                            </div>
                          </div>
                        </div>
                        @if($message->is_important)
                          <div class="ms-auto">
                            <span class="badge bg-warning">
                              <i class="ti ti-star me-1"></i>
                              {{ __('Important') }}
                            </span>
                          </div>
                        @endif
                      </div>
                      <div class="message-content">
                        {!! $message->body !!}
                      </div>

                      <!-- Quick Reply Section -->
                      <div class="message-reply">
                        <div class="message-reply-header">
                          <span class="message-reply-toggle">
                            <i class="ti ti-arrow-back-up me-1"></i>
                            {{ __('Quick Reply') }}
                          </span>
                        </div>
                        <div class="message-reply-form">
                          <form class="quick-reply-form" 
                                data-recipient="{{ $message->sender_id }}"
                                data-subject="{{ $message->subject }}">
                            <div id="editor{{ $message->id }}" class="message-reply-editor"></div>
                            <div class="text-end mt-3">
                              <button type="submit" class="btn btn-primary quick-reply-btn">
                                <i class="ti ti-send me-1"></i>
                                {{ __('Send Reply') }}
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        {{ __('Close') }}
                      </button>
                      <button type="button" class="btn btn-outline-danger btn-delete" data-message-id="{{ $message->id }}">
                        <i class="ti ti-trash me-1"></i>
                        {{ __('Delete') }}
                      </button>
                      <a href="{{ route('dashboard.messages.compose', ['reply_to' => $message->id]) }}" 
                         class="btn btn-primary btn-reply">
                        <i class="ti ti-edit me-1"></i>
                        {{ __('Full Reply') }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Delete Confirmation Modal -->
              <div class="modal fade" id="deleteModal{{ $message->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        <i class="ti ti-alert-triangle text-warning me-2"></i>
                        {{ __('Confirm Delete') }}
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p>{{ __('Are you sure you want to delete this message? This action cannot be undone.') }}</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        {{ __('Cancel') }}
                      </button>
                      <button type="button" class="btn btn-danger confirm-delete" data-message-id="{{ $message->id }}">
                        <i class="ti ti-trash me-1"></i>
                        {{ __('Delete Message') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="my-0">
            @empty
              <div class="text-center p-5">
                <i class="ti ti-inbox mb-2" style="font-size: 3rem;"></i>
                <h5>{{ __('Your inbox is empty') }}</h5>
                <p class="mb-0">{{ __('No messages found') }}</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection