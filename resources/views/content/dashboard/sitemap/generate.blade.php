@extends('layouts/layoutMaster')

@section('title', __('Sitemap Management'))

@section('content')
<div class="container">
    <h1>إدارة خريطة الموقع</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">توليد خريطة الموقع (Sitemap)</h5>
            <p class="card-text">اختر قاعدة البيانات ثم اضغط على الزر لتوليد خريطة الموقع وتحديثها.</p>
            
            <form action="{{ route('sitemap.generate') }}" method="POST" class="mb-3">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="database" class="form-label">اختر قاعدة البيانات</label>
                        <select class="form-select" id="database" name="database">
                            <option value="jo" {{ session('database') == 'jo' ? 'selected' : '' }}>الأردن</option>
                            <option value="sa" {{ session('database') == 'sa' ? 'selected' : '' }}>السعودية</option>
                            <option value="eg" {{ session('database') == 'eg' ? 'selected' : '' }}>مصر</option>
                            <option value="ps" {{ session('database') == 'ps' ? 'selected' : '' }}>فلسطين</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-refresh me-1"></i>توليد خريطة الموقع
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="alert alert-info mt-3">
                <div class="d-flex">
                    <i class="ti ti-info-circle me-2 fs-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">معلومات هامة</h6>
                        <p class="mb-0">سيتم توليد ثلاثة أنواع من خرائط الموقع:</p>
                        <ul class="mt-2 mb-0">
                            <li>خريطة الصفحات الثابتة (الرئيسية، الصفوف، الفئات)</li>
                            <li>خريطة المقالات</li>
                            <li>خريطة الأخبار</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">استعراض خرائط الموقع</h5>
            <p class="card-text">يمكنك استعراض خرائط الموقع المولدة من خلال الروابط التالية:</p>
            
            <div class="list-group mt-3">
                <a href="{{ route('sitemap.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="ti ti-list me-2"></i>
                    عرض جميع خرائط الموقع
                </a>
                <a href="{{ route('sitemap.manage') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="ti ti-settings me-2"></i>
                    إدارة محتوى خرائط الموقع
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
