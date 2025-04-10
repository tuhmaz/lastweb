@extends('layouts.contentNavbarLayout')

@section('title', 'مراقبة الزوار النشطين')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
@endsection

@section('content')
<div class="row">
  <div class="col-md-4 mb-4">
    <div class="card bg-primary text-white h-100">
      <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
        <div class="mb-3">
          <i class="ti ti-users fs-1"></i>
        </div>
        <h2 class="mb-2 fw-semibold" id="visitors-count">0</h2>
        <h5 class="mb-3">زائر نشط</h5>
        <div class="small" id="last-update">آخر تحديث: --:--:--</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-8 mb-4">
    <div class="card h-100">
      <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0 text-white">
            <i class="ti ti-activity me-2"></i>نشاط الزوار
          </h5>
          <button class="btn btn-sm btn-light" onclick="updateVisitorsTable()">
            <i class="ti ti-refresh me-1"></i>تحديث
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="alert alert-info mb-3">
          <div class="d-flex">
            <span class="alert-icon text-info me-2">
              <i class="ti ti-info-circle"></i>
            </span>
            <div>
              يتم عرض الزوار النشطين في آخر 20 ثانية. يتم تحديث البيانات تلقائيًا كل 5 ثوانٍ.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light">
        <h5 class="mb-0">
          <i class="ti ti-list me-2"></i>قائمة الزوار النشطين
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped border" id="visitors-table">
            <thead class="table-light">
              <tr>
                <th class="text-center" width="5%">#</th>
                <th width="25%">الصفحة الحالية</th>
                <th width="15%">المصدر</th>
                <th width="15%">عنوان IP</th>
                <th width="10%">المتصفح</th>
                <th width="15%">أول ظهور</th>
                <th width="15%">آخر نشاط</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center py-5">
                  <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                  </div>
                  <p class="mb-0 text-muted">جاري تحميل بيانات الزوار...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  // للتأكد من أن الصفحة تحمل البيانات بشكل صحيح
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing active visitors page');
    updateVisitorsTable();
  });

  // استخراج معلومات المتصفح من user-agent
  function getBrowserInfo(userAgent) {
    if (!userAgent) return 'غير معروف';
    
    if (userAgent.includes('Chrome')) return 'Chrome';
    if (userAgent.includes('Firefox')) return 'Firefox';
    if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) return 'Safari';
    if (userAgent.includes('Edge')) return 'Edge';
    if (userAgent.includes('MSIE') || userAgent.includes('Trident/')) return 'Internet Explorer';
    
    return 'آخر';
  }

  // تحديث جدول الزوار
  function updateVisitorsTable() {
    console.log('جاري جلب بيانات الزوار...');
    
    // إظهار حالة التحميل
    document.querySelector('#visitors-table tbody').innerHTML = `
      <tr>
        <td colspan="7" class="text-center py-5">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">جاري التحميل...</span>
          </div>
          <p class="mb-0 text-muted">جاري تحميل بيانات الزوار...</p>
        </td>
      </tr>
    `;
    
    // استخدام المسار الصحيح مع CSRF token
    fetch('/monitoring/active-visitors/data', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
      .then(response => {
        console.log('حالة الاستجابة:', response.status);
        if (!response.ok) {
          throw new Error(`خطأ في الاتصال! الحالة: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('البيانات المستلمة:', data);
        if (!data.success) {
          throw new Error(data.message || 'حدث خطأ غير معروف');
        }
        updateVisitorsTableWithData(data);
      })
      .catch(error => {
        console.error('خطأ في تحديث جدول الزوار:', error);
        document.querySelector('#visitors-table tbody').innerHTML = `
          <tr>
            <td colspan="7" class="text-center py-5">
              <i class="ti ti-alert-triangle fs-1 text-danger d-block mb-3"></i>
              <p class="text-danger mb-3">خطأ في تحميل البيانات: ${error.message}</p>
              <button class="btn btn-sm btn-outline-primary" onclick="updateVisitorsTable()">
                <i class="ti ti-refresh me-1"></i>إعادة المحاولة
              </button>
            </td>
          </tr>
        `;
      });
  }
  
  // وظيفة منفصلة لتحديث الجدول بالبيانات
  function updateVisitorsTableWithData(data) {
    // تحديث عدد الزوار
    document.getElementById('visitors-count').textContent = data.count;
    
    // تحديث وقت آخر تحديث
    document.getElementById('last-update').textContent = `آخر تحديث: ${new Date().toLocaleTimeString()}`;
    
    const tbody = document.querySelector('#visitors-table tbody');
    
    // إذا لم يكن هناك زوار نشطين
    if (!data.visitors || data.visitors.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center py-5">
            <i class="ti ti-mood-empty fs-1 text-muted d-block mb-3"></i>
            <p class="mb-0 text-muted">لا يوجد زوار نشطين حاليًا</p>
          </td>
        </tr>
      `;
      return;
    }
    
    // تفريغ الجدول
    tbody.innerHTML = '';
    
    // إضافة صفوف لكل زائر
    data.visitors.forEach((visitor, index) => {
      const row = document.createElement('tr');
      
      // تحويل الطوابع الزمنية إلى كائنات Date
      let firstSeen, lastActivity;
      try {
        firstSeen = visitor.first_seen ? new Date(visitor.first_seen) : new Date();
        lastActivity = visitor.last_activity ? new Date(visitor.last_activity) : new Date();
      } catch (e) {
        console.warn('خطأ في تحليل التواريخ:', e);
        firstSeen = new Date();
        lastActivity = new Date();
      }
      
      // استخراج معلومات المتصفح
      const browser = getBrowserInfo(visitor.user_agent);
      
      // إنشاء محتوى الصف
      row.innerHTML = `
        <td class="text-center fw-bold">${index + 1}</td>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2 bg-label-primary">
              <span class="avatar-initial rounded-circle"><i class="ti ti-link"></i></span>
            </div>
            <span class="text-truncate" style="max-width: 200px;" title="${visitor.url}">
              ${visitor.url}
            </span>
          </div>
        </td>
        <td>
          <span class="badge bg-label-secondary">
            <i class="ti ti-arrow-back-up me-1"></i>${visitor.referrer || 'مباشر'}
          </span>
        </td>
        <td>
          <span class="badge bg-label-primary">
            <i class="ti ti-device-desktop me-1"></i>${visitor.ip}
          </span>
        </td>
        <td>
          <span class="badge bg-label-info">
            <i class="ti ti-browser me-1"></i>${browser}
          </span>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <span class="badge bg-label-secondary me-2">
              <i class="ti ti-clock"></i>
            </span>
            ${firstSeen.toLocaleTimeString()}
          </div>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <span class="badge bg-label-success me-2">
              <i class="ti ti-activity"></i>
            </span>
            ${lastActivity.toLocaleTimeString()}
          </div>
        </td>
      `;
      
      tbody.appendChild(row);
    });
  }

  // تحديث البيانات كل 5 ثوانٍ
  setInterval(updateVisitorsTable, 5000);
  
  // تتبع الزائر الحالي
  function trackCurrentVisitor() {
    const data = {
      url: window.location.href,
      referrer: document.referrer
    };
    
    fetch('/track-visitor', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => console.log('تم تسجيل الزائر:', data))
    .catch(error => console.error('خطأ في تسجيل الزائر:', error));
  }
  
  // تتبع الزائر عند تحميل الصفحة
  trackCurrentVisitor();
  
  // تحديث نشاط الزائر كل 30 ثانية
  setInterval(function() {
    const data = {
      url: window.location.href
    };
    
    fetch('/update-visitor-activity', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(data)
    })
    .catch(error => console.error('خطأ في تحديث نشاط الزائر:', error));
  }, 30000);
</script>
@endsection
