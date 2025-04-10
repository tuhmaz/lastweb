document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[id^="messageModal"]').forEach(modal => {
    const messageId = modal.id.replace('messageModal', '');

    // Initialize Quill editor
    const quillEditor = new Quill(`#editor${messageId}`, {
      modules: {
        toolbar: [
          [{ 'font': [] }, { 'size': [] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'color': [] }, { 'background': [] }],
          [{ 'align': [] }],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link'],
          ['clean']
        ]
      },
      placeholder: 'Type your reply here...',
      theme: 'snow'
    });

    // Handle quick reply form submission
    const quickReplyForm = modal.querySelector('.quick-reply-form');
    if (quickReplyForm) {
      quickReplyForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const editorContent = quillEditor.root.innerHTML;
        if (editorContent.trim() === '') {
          alert('Please enter a reply message.');
          return;
        }

        const quickReplyBtn = quickReplyForm.querySelector('.quick-reply-btn');
        quickReplyBtn.disabled = true;
        quickReplyBtn.innerHTML = '<i class="ti ti-send me-1"></i> Sending...';

        const formData = new FormData();
        formData.append('recipient', this.dataset.recipient);
        formData.append('subject', 'Re: ' + this.dataset.subject);
        formData.append('message', editorContent);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(sendMessageRoute, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Close modal
            const bsModal = bootstrap.Modal.getInstance(modal);
            bsModal.hide();

            // Show success toast
            const toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-white bg-success border-0';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
              <div class="d-flex">
                <div class="toast-body">
                  ${data.message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            `;
            document.body.appendChild(toastEl);

            const toast = new bootstrap.Toast(toastEl, {
              animation: true,
              autohide: true,
              delay: 3000
            });
            toast.show();

            // Reset form
            quillEditor.setText('');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          // Show error toast
          const toastEl = document.createElement('div');
          toastEl.className = 'toast align-items-center text-white bg-danger border-0';
          toastEl.setAttribute('role', 'alert');
          toastEl.setAttribute('aria-live', 'assertive');
          toastEl.setAttribute('aria-atomic', 'true');
          toastEl.innerHTML = `
            <div class="d-flex">
              <div class="toast-body">
                An error occurred while sending the message.
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          `;
          document.body.appendChild(toastEl);

          const toast = new bootstrap.Toast(toastEl, {
            animation: true,
            autohide: true,
            delay: 3000
          });
          toast.show();
        })
        .finally(() => {
          quickReplyBtn.disabled = false;
          quickReplyBtn.innerHTML = '<i class="ti ti-send me-1"></i> Send Reply';
        });
      });
    }

    // Toggle quick reply form
    const replyToggle = modal.querySelector('.message-reply-toggle');
    const replyForm = modal.querySelector('.message-reply-form');

    if (replyToggle && replyForm) {
      replyToggle.addEventListener('click', function () {
        replyForm.classList.toggle('show');
        if (replyForm.classList.contains('show')) {
          quillEditor.focus();
        }
      });
    }
  });
});