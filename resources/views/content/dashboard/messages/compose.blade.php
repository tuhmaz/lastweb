@extends('layouts/contentNavbarLayout')

@section('title', __('Compose Message'))

@section('vendor-style')
<style>
.email-compose-wrapper {
  position: relative;
  height: calc(100vh - 12rem);
}
.select2-container {
  width: 100% !important;
}
.is-invalid {
  border-color: #ff3e1d;
}
</style>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
  // Handle form submission
  document.getElementById('compose-form').addEventListener('submit', function (e) {
    const messageContent = document.querySelector('#message').value.trim();

    // Validate message content
    if (!messageContent) {
      e.preventDefault(); // Prevent form submission
      document.querySelector('.message-error').style.display = 'block'; // Show error message
    } else {
      document.querySelector('.message-error').style.display = 'none'; // Hide error message
    }
  });
});

</script>
@endsection

@section('content')
<div class="email-compose-wrapper container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('dashboard.messages.send') }}" method="POST" id="compose-form">
        @csrf

        <!-- رسائل الخطأ -->
        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- رسائل النجاح -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="row g-3">
          <!-- المستلم -->
          <div class="col-12">
            <label class="form-label" for="recipient">{{ __('To') }}</label>
            <select id="recipient" name="recipient" class="select2 form-select @error('recipient') is-invalid @enderror" required>
              <option value="">{{ __('Select recipient') }}</option>
              @foreach($users as $user)
                @if($user->id !== auth()->id())
                  <option value="{{ $user->id }}" {{ old('recipient') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                  </option>
                @endif
              @endforeach
            </select>
            @error('recipient')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- الموضوع -->
          <div class="col-12">
            <label class="form-label" for="subject">{{ __('Subject') }}</label>
            <input type="text"
                   id="subject"
                   name="subject"
                   class="form-control @error('subject') is-invalid @enderror"
                   placeholder="{{ __('Message subject') }}"
                   value="{{ old('subject') }}"
                   required>
            @error('subject')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- محتوى الرسالة -->
<div class="col-12">
  <label class="form-label">{{ __('Message') }}</label>
  <textarea id="message" name="message" class="form-control @error('message') is-invalid @enderror" placeholder="{{ __('Type your message here...') }}" rows="10" required>{{ old('message') }}</textarea>
  <div class="message-error invalid-feedback" style="display: none;">
    {{ __('The message field is required.') }}
  </div>
  @error('message')
    <div class="invalid-feedback d-block">{{ $message }}</div>
  @enderror
</div>

          <!-- أزرار التحكم -->
          <div class="col-12 d-flex justify-content-between mt-4">
            <div>
              <button type="submit" class="btn btn-primary me-sm-3 me-1">
                <i class="ti ti-send me-1"></i>{{ __('Send') }}
              </button>
              <button type="button" class="btn btn-label-secondary me-sm-3 me-1">
                <i class="ti ti-file me-1"></i>{{ __('Save Draft') }}
              </button>
            </div>
            <button type="button" class="btn btn-label-danger" onclick="window.history.back();">
              <i class="ti ti-trash me-1"></i>{{ __('Discard') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
