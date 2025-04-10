'use strict';
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    const regenerateButton = document.getElementById('regenerateSitemapButton');
    
    if (regenerateButton) {
        regenerateButton.addEventListener('click', handleRegenerate);
    }

    async function handleRegenerate(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const originalText = button.innerHTML;

        try {
            // Set loading state
            button.disabled = true;
            button.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> جاري التحديث...';

            // Show loading dialog
            await Swal.fire({
                title: 'جاري إعادة إنشاء خريطة الموقع',
                html: 'يرجى الانتظار...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the API call
            const response = await fetch('/dashboard/sitemap/generate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `خطأ في الاتصال: ${response.status}`);
            }

            // Show success message
            await Swal.fire({
                title: 'نجاح',
                text: data.message || 'تم إعادة إنشاء الخرائط بنجاح.',
                icon: 'success',
                confirmButtonText: 'حسناً'
            });

            // Reload the page to show updated sitemaps
            window.location.reload();

        } catch (error) {
            console.error('Error:', error);
            await Swal.fire({
                title: 'خطأ',
                text: error.message || 'حدث خطأ غير متوقع',
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        } finally {
            // Reset button state
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
});
