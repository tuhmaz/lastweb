@extends('layouts/contentNavbarLayout')

@section('title', __('OneSignal Settings'))

@section('content')
<div class="container mt-4">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Settings') }} /</span> {{ __('OneSignal Configuration') }}
    </h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('OneSignal Settings') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.settings.updateOneSignal') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="onesignal_app_id" class="form-label">{{ __('OneSignal App ID') }}</label>
                        <input type="text" 
                               class="form-control @error('onesignal_app_id') is-invalid @enderror" 
                               id="onesignal_app_id" 
                               name="onesignal_app_id" 
                               placeholder="{{ __('Enter OneSignal App ID') }}"
                               value="{{ old('onesignal_app_id', $settings->onesignal_app_id ?? '') }}">
                        @error('onesignal_app_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('You can find this in your OneSignal Dashboard') }}</small>
                    </div>

                    <div class="col-12 mb-4">
                        <label for="onesignal_rest_api_key" class="form-label">{{ __('OneSignal REST API Key') }}</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('onesignal_rest_api_key') is-invalid @enderror" 
                                   id="onesignal_rest_api_key" 
                                   name="onesignal_rest_api_key" 
                                   placeholder="{{ __('Enter OneSignal REST API Key') }}"
                                   value="{{ old('onesignal_rest_api_key', $settings->onesignal_rest_api_key ?? '') }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('onesignal_rest_api_key')">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
                        @error('onesignal_rest_api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('Found in App Settings > Keys & IDs') }}</small>
                    </div>

                    <div class="col-12 mb-4">
                        <label for="onesignal_user_auth_key" class="form-label">{{ __('OneSignal User Auth Key') }}</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('onesignal_user_auth_key') is-invalid @enderror" 
                                   id="onesignal_user_auth_key" 
                                   name="onesignal_user_auth_key" 
                                   placeholder="{{ __('Enter OneSignal User Auth Key') }}"
                                   value="{{ old('onesignal_user_auth_key', $settings->onesignal_user_auth_key ?? '') }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('onesignal_user_auth_key')">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
                        @error('onesignal_user_auth_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('Found in Account Settings > Keys & IDs') }}</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
    
    // تغيير أيقونة العين
    const icon = event.currentTarget.querySelector('i');
    icon.classList.toggle('bx-show');
    icon.classList.toggle('bx-hide');
}
</script>
@endsection
