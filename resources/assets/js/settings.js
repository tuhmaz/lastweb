/**
 * Settings Management
 */

'use strict';

// DOM Elements
const settingsForm = document.getElementById('settingsForm');
const logoInput = document.querySelector('input[name="site_logo"]');
const logoPreview = document.querySelector('img[src*="site_logo"]');
const faviconInput = document.querySelector('input[name="site_favicon"]');
const faviconPreview = document.querySelector('img[src*="site_favicon"]');

document.addEventListener('DOMContentLoaded', function () {
    // Initialize forms
    const forms = document.querySelectorAll('.settings-form');
    forms.forEach(form => {
        setupFormSubmission(form);
    });

    // Initialize Select2
    if (typeof jQuery !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2();
    }

    // Initialize color pickers
    if (typeof $.fn.colorpicker !== 'undefined') {
        $('.colorpicker').each(function() {
            $(this).colorpicker({
                format: 'hex'
            }).on('colorpickerChange', function(e) {
                $(this).closest('.input-group').find('.color-preview').css('background-color', e.color.toString());
            });
        });
    }

    // Setup image previews for all file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        const previewElement = input.parentElement.querySelector('img');
        if (previewElement) {
            setupImagePreview(input, previewElement);
        }
    });

    // Test SMTP Connection
    testSMTPConnection();

    // Send Test Email
    sendTestEmail();
});

/**
 * Handle image preview functionality
 */
function setupImagePreview(input, previewElement) {
    if (!input || !previewElement) return;

    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
}

/**
 * Handle form submission
 */
function setupFormSubmission(form) {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Saving...';
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showSuccessMessage(data.message || 'Settings updated successfully');
                if (data.reload) {
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                throw new Error(data.message || 'Failed to update settings');
            }
        } catch (error) {
            console.error('Settings update error:', error);
            showErrorMessage(error.message || 'An error occurred while saving settings');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Changes';
            }
        }
    });
}

/**
 * Test SMTP Connection
 */
function testSMTPConnection() {
    const testBtn = document.querySelector('#test-smtp-btn');
    if (!testBtn) return;

    testBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Testing...';

        try {
            const response = await fetch(testBtn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showSuccessMessage(data.message);
            } else {
                throw new Error(data.message || 'SMTP test failed');
            }
        } catch (error) {
            console.error('SMTP test error:', error);
            showErrorMessage(error.message || 'Failed to test SMTP connection');
        } finally {
            testBtn.disabled = false;
            testBtn.innerHTML = 'Test SMTP Connection';
        }
    });
}

/**
 * Send Test Email
 */
function sendTestEmail() {
    const testEmailModal = document.querySelector('#testEmailModal');
    if (!testEmailModal) return;

    const testEmailBtn = testEmailModal.querySelector('#send-test-email-btn');
    const testEmailInput = testEmailModal.querySelector('#test_email');
    const sendEmailUrl = testEmailModal.dataset.url;

    if (!testEmailBtn || !testEmailInput || !sendEmailUrl) {
        console.error('Required elements for test email not found');
        return;
    }

    testEmailBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        const email = testEmailInput.value.trim();
        if (!email) {
            showErrorMessage('Please enter a test email address');
            return;
        }

        testEmailBtn.disabled = true;
        testEmailBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Sending...';

        try {
            const response = await fetch(sendEmailUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ test_email: email }),
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showSuccessMessage(data.message);
                const modal = bootstrap.Modal.getInstance(testEmailModal);
                if (modal) {
                    modal.hide();
                }
                // Clear the input
                testEmailInput.value = '';
            } else {
                throw new Error(data.message || 'Failed to send test email');
            }
        } catch (error) {
            console.error('Test email error:', error);
            showErrorMessage(error.message || 'Failed to send test email');
        } finally {
            testEmailBtn.disabled = false;
            testEmailBtn.innerHTML = 'Send Test Email';
        }
    });
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Success!',
            text: message,
            icon: 'success',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    } else {
        alert(message);
    }
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            customClass: {
                confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
    } else {
        alert(message);
    }
}
