'use strict';

document.addEventListener('DOMContentLoaded', function() {
  // تهيئة Dropzone
  if (document.getElementById('dropzone-files')) {
    let myDropzone = new Dropzone('#dropzone-files', {
      url: '/dashboard/articles/upload-file',
      paramName: 'file',
      maxFilesize: 10, // MB
      addRemoveLinks: true,
      dictDefaultMessage: document.querySelector('#dropzone-files .dz-message').innerHTML,
      acceptedFiles: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      init: function() {
        this.on('sending', function(file, xhr, formData) {
          const fileCategory = document.getElementById('file_category').value;
          if (!fileCategory) {
            this.removeFile(file);
            showError('Please select a file category before uploading');
            return;
          }
          formData.append('file_category', fileCategory);
          formData.append('article_id', document.querySelector('form').dataset.articleId);
          formData.append('class_id', document.getElementById('class_id').value);
          formData.append('subject_id', document.getElementById('subject_id').value);
          formData.append('semester_id', document.getElementById('semester_id').value);
          formData.append('country', new URLSearchParams(window.location.search).get('country') || '1');
        });

        this.on('success', function(file, response) {
          const fileRow = document.createElement('tr');
          fileRow.dataset.fileId = response.file.id;
          
          fileRow.innerHTML = `
            <td>
              <i class="ti ti-file me-2"></i>
              ${response.file.name}
            </td>
            <td>
              <span class="badge bg-label-${getCategoryColor(response.file.category)}">
                ${response.file.category}
              </span>
            </td>
            <td>${formatFileSize(response.file.size)}</td>
            <td>
              <div class="d-flex gap-2">
                <a href="${response.file.path}" 
                   class="btn btn-sm btn-label-primary"
                   target="_blank"
                   title="${window.translations.Download || 'Download'}">
                  <i class="ti ti-download"></i>
                </a>
                <button type="button" 
                        class="btn btn-sm btn-label-danger delete-file" 
                        data-file-id="${response.file.id}"
                        title="${window.translations.Delete || 'Delete'}">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          `;
          
          document.getElementById('files-list').appendChild(fileRow);
          this.removeFile(file);
        });

        this.on('error', function(file, message) {
          showError(typeof message === 'string' ? message : message.error);
          this.removeFile(file);
        });
      }
    });

    // حذف الملفات
    document.addEventListener('click', function(e) {
      if (e.target.closest('.delete-file')) {
        const button = e.target.closest('.delete-file');
        const fileId = button.dataset.fileId;
        const row = document.querySelector(`tr[data-file-id="${fileId}"]`);
        
        if (confirm(window.translations.DeleteConfirm || 'Are you sure you want to delete this file?')) {
          fetch('/dashboard/articles/remove-file', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ file_id: fileId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              row.remove();
            } else {
              showError(data.message || window.translations.DeleteError || 'Failed to delete file');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showError(window.translations.DeleteError || 'Failed to delete file');
          });
        }
      }
    });
  }

  // Meta description character counter
  const metaDescription = document.getElementById('meta_description');
  if (metaDescription) {
    metaDescription.addEventListener('input', function() {
      const maxLength = this.getAttribute('maxlength');
      const currentLength = this.value.length;
      const remaining = maxLength - currentLength;
      document.getElementById('meta_chars').textContent = remaining;
    });

    // Trigger initial count
    metaDescription.dispatchEvent(new Event('input'));
  }

  // Meta description options
  const useTitleForMeta = document.getElementById('use_title_for_meta');
  const useKeywordsForMeta = document.getElementById('use_keywords_for_meta');
  const title = document.getElementById('title');
  const keywords = document.getElementById('keywords');

  if (useTitleForMeta && useKeywordsForMeta && metaDescription) {
    useTitleForMeta.addEventListener('change', function() {
      if (this.checked) {
        useKeywordsForMeta.checked = false;
        metaDescription.value = title.value;
        metaDescription.dispatchEvent(new Event('input'));
      }
    });

    useKeywordsForMeta.addEventListener('change', function() {
      if (this.checked) {
        useTitleForMeta.checked = false;
        metaDescription.value = keywords.value;
        metaDescription.dispatchEvent(new Event('input'));
      }
    });
  }
});

// Helper Functions
function showError(message) {
  Swal.fire({
    title: window.translations.Error || 'Error',
    text: message,
    icon: 'error',
    customClass: {
      confirmButton: 'btn btn-primary'
    },
    buttonsStyling: false
  });
}

function getCategoryColor(category) {
  const colors = {
    'plans': 'primary',
    'papers': 'info',
    'tests': 'warning',
    'books': 'success'
  };
  return colors[category] || 'primary';
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
