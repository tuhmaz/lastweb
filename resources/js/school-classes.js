/**
 * School Classes DataTable
 */

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

    // تهيئة DataTable
    let dt = $('.datatables-classes').DataTable({
        ajax: {
            url: window.location.href,
            data: function(d) {
                d.country_id = $('#countrySelect').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'grade_name' },
            { data: 'grade_level', render: function(data) {
                return `<span class="badge bg-label-primary">${data}</span>`;
            }},
            { data: 'country_name' },
            { data: 'subjects_count', render: function(data) {
                return `<span class="badge bg-label-info">${data}</span>`;
            }},
            { data: 'created_at' },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="d-inline-flex">
                            <a href="${row.show_url}"
                               class="btn btn-sm btn-icon btn-label-primary me-1"
                               data-bs-toggle="tooltip"
                               title="${__('View Details')}">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="${row.edit_url}"
                               class="btn btn-sm btn-icon btn-label-warning me-1"
                               data-bs-toggle="tooltip"
                               title="${__('Edit')}">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-icon btn-label-danger delete-record"
                                    data-bs-toggle="tooltip"
                                    title="${__('Delete')}"
                                    data-url="${row.delete_url}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[2, 'asc']], // ترتيب حسب المستوى الدراسي
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            "emptyTable": "لا توجد بيانات متاحة في الجدول",
            "loadingRecords": "جارٍ التحميل...",
            "processing": "جارٍ التحميل...",
            "lengthMenu": "أظهر _MENU_ مدخلات",
            "zeroRecords": "لم يعثر على أية سجلات",
            "info": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            "infoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
            "infoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
            "search": "ابحث:",
            "paginate": {
                "first": "الأول",
                "previous": "السابق",
                "next": "التالي",
                "last": "الأخير"
            },
            "aria": {
                "sortAscending": ": تفعيل لترتيب العمود تصاعدياً",
                "sortDescending": ": تفعيل لترتيب العمود تنازلياً"
            }
        }
    });

    // حذف صف
    $('.datatables-classes tbody').on('click', '.delete-record', function() {
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
                        dt.ajax.reload();
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

    // تفعيل Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // تأكيد الحذف
    $('.delete-record').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: __('Are you sure?'),
            text: __('You will not be able to recover this!'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: __('Yes, delete it!'),
            cancelButtonText: __('No, cancel!'),
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
                form.submit();
            }
        });
    });
});
