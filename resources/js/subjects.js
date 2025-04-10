'use strict';

// دالة الترجمة
function __(key) {
    return window.translations[key] || key;
}

$(function() {
    // تهيئة Select2
    $('.select2').select2();

    // التأكد من اختيار الأردن افتراضياً إذا لم يتم اختيار أي دولة
    if (!$('#countrySelect').val()) {
        $('#countrySelect').val('1').trigger('change');
    }

    // تحديث الصفحة عند تغيير الدولة
    $('#countrySelect').on('change', function() {
        $('#filterForm').submit();
    });

    // تفعيل tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // حذف مادة دراسية
    $(document).on('click', '.delete-record', function() {
        let button = $(this);
        let url = button.data('url');

        Swal.fire({
            title: __('Are you sure?'),
            text: __('You will not be able to recover this!'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: __('Yes, delete it!'),
            cancelButtonText: __('No, cancel!'),
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        // إعادة تحميل الصفحة بعد الحذف
                        window.location.reload();
                        
                        Swal.fire({
                            icon: 'success',
                            title: __('Deleted!'),
                            text: __('Record has been deleted.'),
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: __('Error!'),
                            text: xhr.responseJSON?.message || __('Something went wrong!'),
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            }
        });
    });
});
