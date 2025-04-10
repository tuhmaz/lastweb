'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // تفعيل/تعطيل الفئة
  document.querySelectorAll('.toggle-status').forEach(toggle => {
    toggle.addEventListener('change', function() {
      const categoryId = this.dataset.id;
      const url = this.dataset.url;
      
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'نجاح',
            text: data.message,
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          throw new Error(data.message);
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'خطأ',
          text: error.message,
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          buttonsStyling: false
        });
        this.checked = !this.checked;
      });
    });
  });

  // تأكيد الحذف
  document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const categoryName = this.dataset.name;

      Swal.fire({
        title: 'حذف الفئة',
        text: `هل أنت متأكد من حذف "${categoryName}"؟`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذفه!',
        cancelButtonText: 'إلغاء',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  });
});
