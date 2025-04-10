'use strict';

$(function() {
    // Handle publish/unpublish buttons
    $('.publish-btn, .unpublish-btn').on('click', function() {
        const button = $(this);
        const articleId = button.data('id');
        const action = button.data('action');
        const isPublish = action === 'publish';
        
        Swal.fire({
            title: isPublish ? "{{ __('Are you sure you want to publish this article?') }}" : "{{ __('Are you sure you want to unpublish this article?') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: isPublish ? "{{ __('Publish') }}" : "{{ __('Unpublish') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            customClass: {
                confirmButton: `btn btn-${isPublish ? 'success' : 'warning'} btn-lg`,
                cancelButton: 'btn btn-outline-secondary btn-lg'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                button.prop('disabled', true);
                const originalHtml = button.html();
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

                // Send request
                $.ajax({
                    url: `${window.location.pathname}/${action}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        // Reset button state
                        button.prop('disabled', false);
                        button.html(originalHtml);

                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'An error occurred',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // Handle delete form
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        
        Swal.fire({
            title: form.data('confirm'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('Delete') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            customClass: {
                confirmButton: 'btn btn-danger btn-lg',
                cancelButton: 'btn btn-outline-secondary btn-lg'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.off('submit').submit();
            }
        });
    });
});
