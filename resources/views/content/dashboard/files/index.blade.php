@extends('layouts.contentNavbarLayout')

@section('title', __('Files Management'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <!-- محدد الدولة والعنوان -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title mb-0">{{ __('Files Management') }}</h5>
            <small class="text-muted">{{ __('Manage your files and documents') }}</small>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <div class="country-selector">
              <select class="form-select form-select-sm" style="min-width: 150px;"
                onchange="window.location.href='{{ route('dashboard.files.index') }}?country=' + this.value">
                <option value="1" {{ $country == '1' ? 'selected' : '' }}>الأردن</option>
                <option value="2" {{ $country == '2' ? 'selected' : '' }}>السعودية</option>
                <option value="3" {{ $country == '3' ? 'selected' : '' }}>مصر</option>
                <option value="4" {{ $country == '4' ? 'selected' : '' }}>فلسطين</option>
              </select>
            </div>
            <a href="{{ route('dashboard.files.create', ['country' => $country]) }}" class="btn btn-primary">
              <i class="ti ti-plus me-1"></i>{{ __('Add File') }}
            </a>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="btn-group">
              <button type="button" class="btn btn-outline-primary {{ !request('category') ? 'active' : '' }}"
                      onclick="window.location.href='{{ route('dashboard.files.index', ['country' => $country]) }}'">
                {{ __('All') }}
              </button>
              <button type="button" class="btn btn-outline-success {{ request('category') === 'plans' ? 'active' : '' }}"
                      onclick="window.location.href='{{ route('dashboard.files.index', ['country' => $country, 'category' => 'plans']) }}'">
                {{ __('Plans') }}
              </button>
              <button type="button" class="btn btn-outline-info {{ request('category') === 'papers' ? 'active' : '' }}"
                      onclick="window.location.href='{{ route('dashboard.files.index', ['country' => $country, 'category' => 'papers']) }}'">
                {{ __('Papers') }}
              </button>
              <button type="button" class="btn btn-outline-warning {{ request('category') === 'tests' ? 'active' : '' }}"
                      onclick="window.location.href='{{ route('dashboard.files.index', ['country' => $country, 'category' => 'tests']) }}'">
                {{ __('Tests') }}
              </button>
              <button type="button" class="btn btn-outline-primary {{ request('category') === 'books' ? 'active' : '' }}"
                      onclick="window.location.href='{{ route('dashboard.files.index', ['country' => $country, 'category' => 'books']) }}'">
                {{ __('Books') }}
              </button>
            </div>
          </div>

          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center">
              <form action="{{ route('dashboard.files.index', ['country' => $country]) }}" method="GET" class="article-search-form flex-grow-1 me-3">
                <div class="input-group">
                  <span class="input-group-text"><i class="ti ti-search"></i></span>
                  <input type="text"
                         class="form-control"
                         name="search"
                         placeholder="{{ __('Search by article title...') }}"
                         value="{{ request('search') }}">
                  @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                  @endif
                  <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                </div>
              </form>
              <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#updateFileModal">
                <i class="ti ti-file-upload me-1"></i>{{ __('Update File') }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('File Name') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Article') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Uploaded Date') }}</th>
                <th>{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($files ?? [] as $file)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    @php
                      $iconClass = match($file->file_type) {
                        'pdf' => 'ti-file-text',
                        'doc', 'docx' => 'ti-file-description',
                        'xls', 'xlsx' => 'ti-file-spreadsheet',
                        'jpg', 'jpeg', 'png', 'gif' => 'ti-photo',
                        default => 'ti-file'
                      };
                    @endphp
                    <i class="ti {{ $iconClass }} me-2"></i>
                    <span>{{ $file->file_Name ?? $file->file_name }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-{{ match($file->file_category) {
                    'plans' => 'success',
                    'papers' => 'info',
                    'tests' => 'warning',
                    'books' => 'primary',
                    default => 'secondary'
                  } }}">
                    {{ match($file->file_category) {
                      'plans' => __('Plans'),
                      'papers' => __('Papers'),
                      'tests' => __('Tests'),
                      'books' => __('Books'),
                      default => __('Other')
                    } }}
                  </span>
                </td>
                <td>{{ $file->article->title ?? __('N/A') }}</td>
                <td>{{ strtoupper($file->file_type) }}</td>
                <td>{{ $file->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('dashboard.files.show', ['file' => $file->id, 'country' => $country]) }}"
                       class="btn btn-sm btn-icon btn-text-secondary"
                       title="{{ __('View') }}">
                      <i class="ti ti-eye"></i>
                    </a>
                    <form action="{{ route('dashboard.files.download', ['file' => $file->id, 'country' => $country]) }}"
                          method="GET"
                          class="d-inline">
                      <button type="submit"
                              class="btn btn-sm btn-icon btn-text-secondary"
                              title="{{ __('Download') }}">
                        <i class="ti ti-download"></i>
                      </button>
                    </form>
                    <form action="{{ route('dashboard.files.destroy', ['file' => $file->id, 'country' => $country]) }}"
                          method="POST"
                          class="d-inline delete-form"
                          data-file-name="{{ $file->file_Name }}">
                      @csrf
                      @method('DELETE')
                      <button type="button"
                              title="{{ __('Delete') }}"
                              class="btn btn-sm btn-icon btn-text-secondary delete-file-btn">
                        <i class="ti ti-trash text-danger"></i>
                      </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary update-file-btn" 
                            data-file-id="{{ $file->id }}" 
                            data-file-name="{{ $file->file_Name }}" 
                            data-file-category="{{ $file->file_category }}" 
                            data-article-id="{{ $file->article_id }}">
                      <i class="ti ti-edit"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <div class="text-center">
                    <i class="ti ti-file-off mb-2" style="font-size: 3rem;"></i>
                    <p class="mb-0">{{ __('No files found') }}</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($files instanceof \Illuminate\Pagination\LengthAwarePaginator)
          <div class="card-footer">
            {{ $files->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal Update File -->
<div class="modal fade" id="updateFileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Update File') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateFileForm" action="" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="_method" value="PUT">

          <!-- معلومات الملف الحالي -->
          <div class="alert alert-info mb-3">
            <div class="d-flex align-items-center mb-2">
              <i class="ti ti-info-circle me-2"></i>
              <strong>{{ __('Current File Information') }}</strong>
            </div>
            <p class="mb-0">{{ __('File Name') }}: <span id="currentFileName"></span></p>
            <p class="mb-0">{{ __('Category') }}: <span id="currentFileCategory"></span></p>
          </div>

          <div class="row g-3">
            <!-- اختيار المقالة -->
            <div class="col-md-12 mb-3">
              <label class="form-label">{{ __('Select Article') }}</label>
              <select class="form-select" name="article_id" id="article_id" required>
                <option value="">{{ __('Select Article') }}</option>
                @foreach($articles as $article)
                  <option value="{{ $article->id }}">{{ $article->title }}</option>
                @endforeach
              </select>
            </div>

            <!-- اختيار الفئة -->
            <div class="col-md-12 mb-3">
              <label class="form-label">{{ __('File Category') }}</label>
              <select class="form-select" name="file_category" id="file_category" required>
                <option value="">{{ __('Select Category') }}</option>
                <option value="plans">{{ __('Plans') }}</option>
                <option value="papers">{{ __('Papers') }}</option>
                <option value="tests">{{ __('Tests') }}</option>
                <option value="books">{{ __('Books') }}</option>
              </select>
            </div>

            <!-- منطقة تحميل الملف -->
            <div class="col-md-12">
              <label class="form-label">{{ __('Upload New File') }}</label>
              <div class="input-group">
                <input type="file"
                       class="form-control"
                       name="file"
                       id="fileInput"
                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
              </div>
              <div class="form-text">
                {{ __('Supported files') }}: JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX
                <br>
                {{ __('Maximum file size') }}: 10MB
              </div>
              <div id="selected-file-info" class="alert alert-primary d-none mt-2">
                <div class="d-flex align-items-center">
                  <i class="ti ti-file me-2"></i>
                  <div>{{ __('Selected file') }}: <span id="selected-filename"></span></div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" form="updateFileForm" class="btn btn-primary">
          <i class="ti ti-file-upload me-1"></i>{{ __('Update File') }}
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // عرض اسم الملف المحدد
  document.getElementById('fileInput').addEventListener('change', function() {
    const fileName = this.files[0]?.name;
    const fileInfo = document.getElementById('selected-file-info');
    const fileNameSpan = document.getElementById('selected-filename');

    if (fileName) {
      fileNameSpan.textContent = fileName;
      fileInfo.classList.remove('d-none');
    } else {
      fileInfo.classList.add('d-none');
    }
  });

  // معالجة زر تحديث الملف
  document.querySelectorAll('.update-file-btn').forEach(button => {
    button.addEventListener('click', function() {
      const fileId = this.dataset.fileId;
      const fileName = this.dataset.fileName;
      const fileCategory = this.dataset.fileCategory;
      const articleId = this.dataset.articleId;

      // تحديث معلومات الملف الحالي
      document.getElementById('currentFileName').textContent = fileName;
      document.getElementById('currentFileCategory').textContent = fileCategory;

      // تحديث القيم المحددة
      document.getElementById('article_id').value = articleId;
      document.getElementById('file_category').value = fileCategory;

      // تحديث عنوان URL النموذج
      const form = document.getElementById('updateFileForm');
      const updateUrl = `{{ route('dashboard.files.update', ['file' => ':id']) }}`;
      form.action = updateUrl.replace(':id', fileId);

      // إعادة تعيين حقل الملف
      document.getElementById('fileInput').value = '';
      document.getElementById('selected-file-info').classList.add('d-none');

      // فتح النافذة المنبثقة
      new bootstrap.Modal(document.getElementById('updateFileModal')).show();
    });
  });

  // معالجة تقديم النموذج
  document.getElementById('updateFileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // التحقق من اختيار المقالة والفئة
    const articleId = document.getElementById('article_id').value;
    const fileCategory = document.getElementById('file_category').value;

    if (!articleId || !fileCategory) {
      Swal.fire({
        icon: 'error',
        title: '{{ __("Validation Error") }}',
        text: '{{ __("Please select both article and category") }}',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
      return;
    }

    // التحقق من حجم الملف
    const fileInput = document.getElementById('fileInput');
    if (fileInput.files.length > 0) {
      const fileSize = fileInput.files[0].size / 1024 / 1024; // تحويل إلى ميجابايت
      if (fileSize > 10) {
        Swal.fire({
          icon: 'error',
          title: '{{ __("File Too Large") }}',
          text: '{{ __("Maximum file size is 10MB") }}',
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
        });
        return;
      }
    }

    // إظهار حالة التحميل
    Swal.fire({
      title: '{{ __("Updating File") }}',
      text: '{{ __("Please wait...") }}',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // إرسال النموذج
    const formData = new FormData(this);

    fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: '{{ __("Success") }}',
          text: data.message,
          showConfirmButton: false,
          timer: 1500
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.message || '{{ __("Error updating file") }}');
      }
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: '{{ __("Error") }}',
        text: error.message,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    });
  });

  // معالجة حذف الملف
  document.querySelectorAll('.delete-file-btn').forEach(button => {
    button.addEventListener('click', function() {
      const form = this.closest('.delete-form');
      const fileName = form.dataset.fileName;

      Swal.fire({
        title: '{{ __("Delete File") }}',
        text: `{{ __("Are you sure you want to delete") }} "${fileName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, delete it!") }}',
        cancelButtonText: '{{ __("Cancel") }}',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-secondary ms-3'
        },
        buttonsStyling: false
      }).then(function(result) {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush
