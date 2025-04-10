document.addEventListener('DOMContentLoaded', function() {
  // تهيئة المتغيرات
  const publishBtn = document.querySelector('.publish-btn');
  const unpublishBtn = document.querySelector('.unpublish-btn');
  const deleteForm = document.querySelector('.delete-form');
  const attachmentLinks = document.querySelectorAll('.attachment-download');
  const printBtn = document.querySelector('.print-article');
  const shareBtn = document.querySelector('.share-article');
  const keywordBadges = document.querySelectorAll('.keyword-badge');

  // معالجة نشر/إلغاء نشر المقال
  function handlePublishStatus(button, action) {
    if (!button) return;

    button.addEventListener('click', function() {
      const articleId = this.dataset.id;
      const url = window.routes.articles[action].replace(':id', articleId);

      Swal.fire({
        title: window.translations[`${action}Confirmation`],
        text: window.translations[`${action}Message`],
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.no,
        customClass: {
          confirmButton: `btn btn-${action === 'publish' ? 'success' : 'warning'} me-3`,
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          // إرسال طلب تغيير الحالة
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
                title: window.translations.success,
                text: data.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                },
                buttonsStyling: false
              }).then(() => {
                window.location.reload();
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: window.translations.error,
              text: window.translations.generalError,
              customClass: {
                confirmButton: 'btn btn-danger'
              },
              buttonsStyling: false
            });
          });
        }
      });
    });
  }

  // معالجة حذف المقال
  if (deleteForm) {
    deleteForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const confirmMessage = this.dataset.confirm;

      Swal.fire({
        title: window.translations.deleteConfirmation,
        text: confirmMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.no,
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  }

  // تتبع تحميل المرفقات
  attachmentLinks.forEach(link => {
    link.addEventListener('click', function() {
      const fileId = this.dataset.fileId;
      const downloadUrl = window.routes.files.download.replace(':id', fileId);

      // تحديث عداد التحميل
      fetch(downloadUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });
    });
  });

  // طباعة المقال
  if (printBtn) {
    printBtn.addEventListener('click', function() {
      window.print();
    });
  }

  // مشاركة المقال
  if (shareBtn) {
    shareBtn.addEventListener('click', function() {
      if (navigator.share) {
        navigator.share({
          title: document.title,
          url: window.location.href
        })
        .catch(console.error);
      } else {
        // نسخ الرابط إلى الحافظة
        navigator.clipboard.writeText(window.location.href).then(() => {
          Swal.fire({
            icon: 'success',
            title: window.translations.linkCopied,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            customClass: {
              popup: 'colored-toast'
            }
          });
        });
      }
    });
  }

  // معالجة الكلمات المفتاحية
  keywordBadges.forEach(badge => {
    badge.addEventListener('click', function() {
      const keyword = this.textContent.trim();
      window.location.href = window.routes.articles.search + '?q=' + encodeURIComponent(keyword);
    });
  });

  // تهيئة الصور القابلة للتكبير
  const articleImages = document.querySelectorAll('.ql-content img');
  articleImages.forEach(img => {
    img.addEventListener('click', function() {
      Swal.fire({
        imageUrl: this.src,
        imageAlt: this.alt,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
          popup: 'image-popup'
        }
      });
    });
  });

  // تفعيل النشر/إلغاء النشر
  handlePublishStatus(publishBtn, 'publish');
  handlePublishStatus(unpublishBtn, 'unpublish');
});
