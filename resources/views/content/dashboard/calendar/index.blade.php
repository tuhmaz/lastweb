@extends('layouts/contentNavbarLayout')

@section('title', 'التقويم')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss'
  ])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/select2/select2.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/app-calendar-events.js',
    'resources/assets/js/app-calendar.js'
  ])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card app-calendar-wrapper">
    <div class="row g-0">
      <!-- Calendar Sidebar -->
      <div class="col-12 col-md-3 app-calendar-sidebar">
        <div class="border-bottom p-4 my-sm-0 mb-3">
          <div class="d-grid">
            <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas" data-bs-target="#addEventSidebar">
              <i class="ti ti-plus me-1"></i>
              <span class="align-middle">إضافة حدث جديد</span>
            </button>
          </div>
        </div>
        <div class="p-4">
          <!-- قائمة اختيار قاعدة البيانات -->
          <div class="mb-4">
            <small class="text-small text-muted text-uppercase align-middle">قاعدة البيانات</small>
            <select id="select-database" class="form-select mt-3" name="database">
              @foreach($databases as $db)
                <option value="{{ $db }}" {{ $currentDatabase === $db ? 'selected' : '' }}>
                  @switch($db)
                    @case('jo')
                      الأردن
                      @break
                    @case('sa')
                      السعودية
                      @break
                    @case('ae')
                      الإمارات
                      @break
                    @case('bh')
                      البحرين
                      @break
                    @case('kw')
                      الكويت
                      @break
                    @case('om')
                      عمان
                      @break
                    @case('qa')
                      قطر
                      @break
                    @case('eg')
                      مصر
                      @break
                    @case('ps')
                      فلسطين
                      @break
                    @default
                      {{ $db }}
                  @endswitch
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <!-- /Calendar Sidebar -->
      
      <!-- Calendar & Modal -->
      <div class="col app-calendar-content">
        <div class="card shadow-none border-0">
          <div class="card-body pb-0">
            <!-- FullCalendar -->
            <div id="calendar"></div>
          </div>
        </div>
        <div class="app-overlay"></div>
        <!-- FullCalendar Offcanvas -->
        <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar" aria-labelledby="addEventSidebarLabel">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title mb-2" id="addEventSidebarLabel">إضافة حدث</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
              <div class="mb-3">
                <label class="form-label" for="eventTitle">عنوان الحدث</label>
                <input type="text" class="form-control" id="eventTitle" name="eventTitle" placeholder="حدث" required />
              </div>
              <div class="mb-3">
                <label class="form-label" for="eventDescription">الوصف</label>
                <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label" for="eventStartDate">تاريخ الحدث</label>
                <input type="text" class="form-control" id="eventStartDate" name="eventStartDate" placeholder="YYYY-MM-DD HH:mm" required />
              </div>
              <div class="mb-3">
                <label class="form-label" for="eventDatabase">قاعدة البيانات</label>
                <select id="eventDatabase" name="eventDatabase" class="form-select" required>
                  @foreach($databases as $db)
                    <option value="{{ $db }}" {{ $currentDatabase === $db ? 'selected' : '' }}>
                      @switch($db)
                        @case('jo')
                          الأردن
                          @break
                        @case('sa')
                          السعودية
                          @break
                        @case('ae')
                          الإمارات
                          @break
                        @case('bh')
                          البحرين
                          @break
                        @case('kw')
                          الكويت
                          @break
                        @case('om')
                          عمان
                          @break
                        @case('qa')
                          قطر
                          @break
                        @case('eg')
                          مصر
                          @break
                        @case('ps')
                          فلسطين
                          @break
                        @default
                          {{ $db }}
                      @endswitch
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                <button type="submit" class="btn btn-primary btn-add-event me-sm-3 me-1">إضافة</button>
                <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1" data-bs-dismiss="offcanvas">إلغاء</button>
                <button class="btn btn-label-danger btn-delete-event d-none">حذف</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- /Calendar & Modal -->
    </div>
  </div>
</div>

<!-- Pass databases data to JavaScript -->
<script>
  window.appCalendar = {
    databases: @json($databases),
    currentDatabase: @json($currentDatabase)
  };
</script>
@endsection
