'use strict';

document.addEventListener('DOMContentLoaded', function() {
    let dt_trusted_ips = $('.datatables-trusted-ips');
    
    if (dt_trusted_ips.length) {
        dt_trusted_ips.DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            responsive: true,
            order: [[2, 'desc']],
            language: {
                search: window.translations.search,
                lengthMenu: window.translations.show_entries,
                info: window.translations.showing_entries,
                infoEmpty: window.translations.showing_zero,
                infoFiltered: window.translations.filtered,
                emptyTable: window.translations.no_trusted_ips,
                paginate: {
                    first: window.translations.first,
                    last: window.translations.last,
                    next: window.translations.next,
                    previous: window.translations.previous
                }
            }
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Trust IP function
function trustIp(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('/security/ip/trust', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            Swal.fire({
                title: window.translations.success,
                text: data.message,
                icon: 'success',
                confirmButtonText: window.translations.ok
            }).then(() => {
                // Close modal and refresh page
                bootstrap.Modal.getInstance(document.getElementById('trustIpModal')).hide();
                window.location.reload();
            });
        } else {
            throw new Error(data.message || window.translations.error);
        }
    })
    .catch(error => {
        Swal.fire({
            title: window.translations.error,
            text: error.message,
            icon: 'error',
            confirmButtonText: window.translations.ok
        });
    });

    return false;
}

// Untrust IP function
function untrustIp(ipAddress) {
    Swal.fire({
        title: window.translations.confirm_untrust,
        text: window.translations.untrust_confirmation,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: window.translations.yes_untrust,
        cancelButtonText: window.translations.cancel,
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/security/ip/${ipAddress}/untrust`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: window.translations.success,
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: window.translations.ok,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || window.translations.error);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: window.translations.error,
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: window.translations.ok,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            });
        }
    });
}

// View IP Details function
function viewIpDetails(ipAddress) {
    fetch(`/security/ip/${ipAddress}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('ipDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('ipDetailsModal')).show();
        })
        .catch(error => {
            Swal.fire({
                title: window.translations.error,
                text: window.translations.failed_to_load,
                icon: 'error',
                confirmButtonText: window.translations.ok,
                customClass: {
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        });
}

// Make functions globally available
window.trustIp = trustIp;
window.untrustIp = untrustIp;
window.viewIpDetails = viewIpDetails;
