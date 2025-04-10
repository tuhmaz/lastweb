import 'summernote/dist/summernote-bs5.min.css';
import 'summernote/dist/summernote-bs5.min.js';
import 'summernote/dist/lang/summernote-ar-AR.js';
import axios from 'axios';
import Swal from 'sweetalert2';

$(document).ready(function () {
  $('#summernote').summernote({
    disableDragAndDrop: true,
    height: 300,
    lang: 'ar-AR',
    dialogsInBody: true,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'clear']],
      ['fontsize', ['fontsize']],
      ['height', ['height']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['insert', ['link', 'picture']],
      ['view', ['codeview', 'help']]
    ],
    callbacks: {
      onImageUpload: function (files) {
        const file = files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
          alert('عذراً، يمكنك رفع ملفات الصور فقط (JPG, PNG, GIF)');
          return;
        }

        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
          alert('عذراً، حجم الملف يجب أن لا يتجاوز 5 ميجابايت');
          return;
        }

        // Show image resize dialog
        Swal.fire({
          title: 'إعدادات الصورة',
          html: `
            <div class="mb-3">
              <label class="form-label">العرض (بالبكسل)</label>
              <input type="number" id="imageWidth" class="form-control" min="1" value="650">
            </div>
            <div class="mb-3">
              <label class="form-label">الارتفاع (بالبكسل)</label>
              <input type="number" id="imageHeight" class="form-control" min="1" value="450">
            </div>
            <div class="mb-3">
              <label class="form-label">جودة الصورة (%)</label>
              <input type="range" id="imageQuality" class="form-range" min="1" max="100" value="80">
              <div class="text-center" id="qualityValue">80%</div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'رفع',
          cancelButtonText: 'إلغاء',
          didOpen: () => {
            // Update quality value display
            $('#imageQuality').on('input', function () {
              $('#qualityValue').text($(this).val() + '%');
            });
          }
        }).then(result => {
          if (result.isConfirmed) {
            const width = $('#imageWidth').val();
            const height = $('#imageHeight').val();
            const quality = $('#imageQuality').val();

            // Show loading
            Swal.fire({
              title: 'جاري رفع الصورة...',
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            uploadImage(file, width, height, quality);
          }
        });
      },
      onPaste: function (e) {
        const clipboardData = e.originalEvent.clipboardData;
        const items = clipboardData.items;
        for (let i = 0; i < items.length; i++) {
          if (items[i].type.indexOf('image') !== -1) {
            e.preventDefault();
            alert('عذراً، لا يمكن لصق الصور مباشرة');
            return;
          }
        }
      }
    },
    popover: {
      image: [
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ],
      link: [['link', ['linkDialogShow', 'unlink']]],
      table: [
        ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
        ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
      ],
      air: [
        ['font', ['bold', 'underline', 'clear']],
        ['para', ['ul', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link']]
      ]
    }
  });
});

// وظيفة رفع الصورة
function uploadImage(file, width, height, quality) {
  let data = new FormData();
  data.append("file", file);
  
  // Add resize parameters if provided
  if (width) data.append("width", width);
  if (height) data.append("height", height);
  if (quality) data.append("quality", quality);

  // Set timeout to 30 seconds
  const TIMEOUT = 30000;
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), TIMEOUT);

  // Show initial loading state
  Swal.fire({
    title: 'جاري معالجة الصورة...',
    html: 'يتم تحضير الصورة للرفع',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  axios.post('/upload/image', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    withCredentials: true,
    signal: controller.signal,
    onUploadProgress: (progressEvent) => {
      const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      Swal.update({
        title: 'جاري رفع الصورة...',
        html: `تم رفع ${percentCompleted}%`
      });
    }
  })
  .then(response => {
    clearTimeout(timeoutId);
    Swal.close();
    
    if (response.status === 200 && response.data.url) {
      let fileName = file.name;
      // Insert the image with specific dimensions
      $('#summernote').summernote('insertImage', response.data.url, function($image) {
        $image.attr('alt', fileName);
        // Set the actual dimensions returned from the server
        if (response.data.width && response.data.height) {
          $image.css({
            width: response.data.width + 'px',
            height: response.data.height + 'px'
          });
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'فشل في رفع الصورة. يرجى المحاولة مرة أخرى.'
      });
    }
  })
  .catch(error => {
    clearTimeout(timeoutId);
    let errorMessage = 'حدث خطأ أثناء رفع الصورة. يرجى التحقق من اتصالك أو المحاولة مرة أخرى لاحقاً.';
    
    if (error.name === 'AbortError') {
      errorMessage = 'انتهت مهلة رفع الصورة. يرجى التحقق من حجم الصورة واتصال الإنترنت والمحاولة مرة أخرى.';
    }
    
    Swal.fire({
      icon: 'error',
      title: 'خطأ',
      text: errorMessage
    });
    console.error('Image upload failed:', error);
  });
}

// وظيفة رفع الملف
function uploadFile(file, context) {
  let data = new FormData();
  data.append("file", file);

  axios.post('/upload/file', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    withCredentials: true
  })
  .then(response => {
    if (response.status === 200 && response.data.url) {
      let fileName = file.name;
      let fileUrl = response.data.url;
      // إدراج رابط الملف في المحرر
      context.invoke('editor.createLink', {
        text: fileName,
        url: fileUrl,
        isNewWindow: true
      });
    } else {
      alert('Failed to upload the file. Please try again.');
    }
  })
  .catch(error => {
    console.error('File upload failed:', error);
    alert('An error occurred while uploading the file. Please check your connection or try again later.');
  });
}
