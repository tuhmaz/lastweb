@extends('layouts/contentNavbarLayout')

@section('title', 'إدارة محتوى خريطة الموقع')

@push('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.css',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.css'
  ])
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">إدارة محتوى خريطة الموقع</h5>
          <form action="{{ route('dashboard.sitemap.set-database') }}" method="POST" class="d-flex align-items-center gap-2">
            @csrf
            <div class="form-floating" style="min-width: 200px;">
              <select name="database" id="database" class="form-select" onchange="this.form.submit()">
                @foreach($dbNames as $key => $name)
                  <option value="{{ $key }}" {{ $database == $key ? 'selected' : '' }}>
                    {{ $name }}
                  </option>
                @endforeach
              </select>
              <label for="database">اختر قاعدة البيانات</label>
            </div>
          </form>
        </div>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-articles" type="button">
              <i class="ti ti-article me-1"></i>
              المقالات
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-lessons" type="button">
              <i class="ti ti-book me-1"></i>
              الدروس
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-categories" type="button">
              <i class="ti ti-category me-1"></i>
              التصنيفات
            </button>
          </li>
        </ul>

        <div class="tab-content pt-4">
          <!-- المقالات -->
          <div class="tab-pane fade show active" id="tab-articles">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>العنوان</th>
                    <th>التصنيف</th>
                    <th>آخر تحديث</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($articles as $article)
                  <tr>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->subject->subject_name ?? __('Not Set') }}</td>
                    <td>{{ $article->updated_at->format('Y-m-d') }}</td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                          onchange="updateInclusion('article', {{ $article->id }}, this.checked)"
                          {{ !isset($statuses["article-{$article->id}"]) ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <a href="{{ route('dashboard.articles.show', ['article' => $article->id]) }}"
                         class="btn btn-sm btn-icon btn-secondary" target="_blank">
                        <i class="ti ti-external-link"></i>
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <!-- الدروس -->
          <div class="tab-pane fade" id="tab-lessons">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>العنوان</th>
                    <th>المستوى</th>
                    <th>آخر تحديث</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($classes as $class)
                  <tr>
                    <td>{{ $class->grade_name }}</td>
                    <td>{{ $class->grade_level }}</td>
                    <td>{{ $class->updated_at->format('Y-m-d') }}</td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                          onchange="updateInclusion('class', {{ $class->id }}, this.checked)"
                          {{ !isset($statuses["class-{$class->id}"]) ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <a href="{{ route('dashboard.school-classes.show', ['school_class' => $class->id, 'country' => request()->input('country', 'jo')]) }}"
                         class="btn btn-sm btn-icon btn-secondary" target="_blank">
                        <i class="ti ti-external-link"></i>
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <!-- التصنيفات -->
          <div class="tab-pane fade" id="tab-categories">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>الاسم</th>
                    <th>الرابط</th>
                    <th>عدد المقالات</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($categories as $category)
                  <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->articles_count ?? 0 }}</td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                          onchange="updateInclusion('category', {{ $category->id }}, this.checked)"
                          {{ !isset($statuses["category-{$category->id}"]) ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <a href="{{ route('dashboard.categories.show', ['category' => $category->slug]) }}"
                         class="btn btn-sm btn-icon btn-secondary" target="_blank">
                        <i class="ti ti-external-link"></i>
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endpush

@push('page-script')
<script>
function updateInclusion(type, id, included) {
  fetch('{{ route("dashboard.sitemap.update-inclusion") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      resource_type: type,
      resource_id: id,
      included: included
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'تم التحديث',
        text: 'تم تحديث حالة العنصر بنجاح',
        showConfirmButton: false,
        timer: 1500
      });
    } else {
      throw new Error(data.message || 'حدث خطأ أثناء تحديث الحالة');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'خطأ',
      text: error.message || 'حدث خطأ أثناء تحديث الحالة',
      confirmButtonText: 'حسناً'
    });
    // إعادة الزر إلى حالته السابقة
    event.target.checked = !included;
  });
}
</script>
@endpush
