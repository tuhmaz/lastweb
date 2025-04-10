@php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-3">
      <div>
        <span class="footer-text">
          © <script>document.write(new Date().getFullYear())</script>
          {{ config('settings.site_name', 'My Website') }}
        </span>
        <span class="footer-version ms-3 text-muted">
          <i class="ti ti-info-circle me-1"></i>
          {{ config('settings.version', 'v1.0.0') }}
        </span>
      </div>

      <div class="d-none d-sm-inline-block">
        <div class="footer-utils">
          <a href="{{ config('settings.documentation') }}" class="footer-link me-4" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="التوثيق">
            <i class="ti ti-book me-1"></i>
            <span class="d-none d-md-inline">التوثيق</span>
          </a>
          <a href="{{ config('settings.support') }}" class="footer-link me-4" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="الدعم الفني">
            <i class="ti ti-help me-1"></i>
            <span class="d-none d-md-inline">الدعم الفني</span>
          </a>
          <a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#shortcutsModal">
            <i class="ti ti-keyboard me-1"></i>
            <span class="d-none d-md-inline">اختصارات لوحة المفاتيح</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Shortcuts Modal -->
<div class="modal fade" id="shortcutsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title">اختصارات لوحة المفاتيح</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>البحث السريع</span>
              <kbd>Ctrl + K</kbd>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>لوحة التحكم</span>
              <kbd>Ctrl + D</kbd>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span>المساعدة</span>
              <kbd>Ctrl + H</kbd>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>الإشعارات</span>
              <kbd>Ctrl + N</kbd>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span>الإعدادات</span>
              <kbd>Ctrl + ,</kbd>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span>تسجيل الخروج</span>
              <kbd>Ctrl + L</kbd>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.footer {
  background-color: var(--bs-body-bg) !important;
  border-top: 1px solid var(--bs-border-color);
}

.footer-container {
  min-height: 50px;
}

.footer-text {
  color: var(--bs-body-color);
}

.footer-version {
  font-size: 0.875rem;
}

.footer-link {
  color: var(--bs-body-color);
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.2s ease-in-out;
}

.footer-link:hover {
  color: var(--bs-primary);
}

.footer-utils {
  display: flex;
  align-items: center;
}

kbd {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  line-height: 1;
  color: var(--bs-body-color);
  background-color: var(--bs-body-bg);
  border: 1px solid var(--bs-border-color);
  border-radius: 0.25rem;
  box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.12);
}

@media (max-width: 767.98px) {
  .footer-container {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .footer-utils {
    margin-top: 1rem;
  }
}
</style>

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // تفعيل tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@endpush
