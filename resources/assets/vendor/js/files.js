'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // تهيئة Dropzone
  const myDropzone = new Dropzone("#dropzone-basic", {
    url: uploadUrl,
    paramName: "file",
    maxFilesize: 10,
    maxFiles: 10,
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx",
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    dictDefaultMessage: "قم بإسقاط الملفات هنا للتحميل",
    dictFallbackMessage: "المتصفح الخاص بك لا يدعم السحب والإفلات للملفات.",
    dictFileTooBig: "الملف كبير جداً ({{filesize}}ميجابايت). الحد الأقصى: {{maxFilesize}}ميجابايت.",
    dictInvalidFileType: "لا يمكنك تحميل ملفات من هذا النوع.",
    dictResponseError: "الخادم استجاب مع {{statusCode}}.",
    dictCancelUpload: "إلغاء التحميل",
    dictUploadCanceled: "تم إلغاء التحميل.",
    dictCancelUploadConfirmation: "هل أنت متأكد من إلغاء التحميل؟",
    dictRemoveFile: "حذف الملف",
    dictMaxFilesExceeded: "لا يمكنك تحميل المزيد من الملفات.",
    init: function() {
      this.on("success", function(file, response) {
        if (response.success) {
          // تحديث الجدول
          const tableBody = document.querySelector('#filesTable tbody');
          const newRow = createFileRow(response.file);
          tableBody.insertBefore(newRow, tableBody.firstChild);
          
          // إظهار رسالة نجاح
          showAlert('success', 'تم تحميل الملف بنجاح');
        }
      });

      this.on("error", function(file, errorMessage) {
        showAlert('error', typeof errorMessage === 'string' ? errorMessage : 'حدث خطأ أثناء تحميل الملف');
      });
    }
  });

  // حذف الملف
  document.addEventListener('click', function(e) {
    if (e.target.closest('.delete-file')) {
      e.preventDefault();
      const button = e.target.closest('.delete-file');
      const fileId = button.dataset.id;
      const url = button.dataset.url;

      Swal.fire({
        title: 'حذف الملف',
        text: 'هل أنت متأكد من حذف هذا الملف؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(url, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const row = button.closest('tr');
              row.remove();
              showAlert('success', 'تم حذف الملف بنجاح');
            } else {
              throw new Error(data.message);
            }
          })
          .catch(error => {
            showAlert('error', error.message || 'حدث خطأ أثناء حذف الملف');
          });
        }
      });
    }
  });

  // تغيير البلد
  const countrySelect = document.querySelector('#countrySelect');
  if (countrySelect) {
    countrySelect.addEventListener('change', function() {
      window.location.href = `${indexUrl}?country=${this.value}`;
    });
  }
});

// إنشاء صف جديد في الجدول
function createFileRow(file) {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <div class="d-flex align-items-center">
        <i class="ti ti-${getFileIcon(file.extension)} text-primary me-2" style="font-size: 1.5rem;"></i>
        <div>
          <h6 class="mb-0">${file.original_name}</h6>
          <small class="text-muted">${formatBytes(file.size)}</small>
        </div>
      </div>
    </td>
    <td>
      <span class="badge bg-label-primary">${file.extension.toUpperCase()}</span>
    </td>
    <td>
      <span class="text-muted"><i class="ti ti-calendar me-1"></i>${formatDate(file.created_at)}</span>
    </td>
    <td>
      <div class="d-flex gap-2">
        <a href="${file.url}" class="btn btn-icon btn-label-primary btn-sm" download>
          <i class="ti ti-download"></i>
        </a>
        <button type="button" class="btn btn-icon btn-label-danger btn-sm delete-file"
                data-id="${file.id}" data-url="${deleteUrl.replace(':id', file.id)}">
          <i class="ti ti-trash"></i>
        </button>
      </div>
    </td>
  `;
  return tr;
}

// الحصول على أيقونة الملف
function getFileIcon(extension) {
  const icons = {
    'pdf': 'file-type-pdf',
    'doc': 'file-type-doc',
    'docx': 'file-type-doc',
    'xls': 'file-type-xls',
    'xlsx': 'file-type-xls',
    'jpg': 'photo',
    'jpeg': 'photo',
    'png': 'photo',
    'gif': 'photo'
  };
  return icons[extension.toLowerCase()] || 'file';
}

// تنسيق حجم الملف
function formatBytes(bytes, decimals = 2) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// تنسيق التاريخ
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

// إظهار تنبيه
function showAlert(type, message) {
  Swal.fire({
    icon: type,
    title: type === 'success' ? 'نجاح' : 'خطأ',
    text: message,
    showConfirmButton: false,
    timer: 3000,
    customClass: {
      popup: 'animated fadeInDown'
    }
  });
}
