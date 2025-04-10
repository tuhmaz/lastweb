'use strict';

document.addEventListener('DOMContentLoaded', function() {
    let dt_blocked_ips = $('.datatables-blocked-ips');
    
    if (dt_blocked_ips.length) {
        const table = dt_blocked_ips.DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            responsive: true,
            order: [[2, 'desc']],
            columns: [
                { // IP Address
                    data: 'ip_address',
                    render: function(data) {
                        return `<div class="d-flex align-items-center">
                            <i class="bx bx-shield-x text-danger me-2"></i>
                            <span>${data}</span>
                        </div>`;
                    }
                },
                { // Location
                    data: null,
                    render: function(data) {
                        if (data.country_code) {
                            return `<img src="${window.location.origin}/assets/img/flags/${data.country_code.toLowerCase()}.png"
                                     alt="${data.country_code}"
                                     class="me-1"
                                     style="width: 20px;">
                                ${data.city ? data.city + ', ' : ''}${data.country_code}`;
                        }
                        return window.translations.unknown || 'Unknown';
                    }
                },
                { // Last Attempt
                    data: 'last_attempt',
                    render: function(data) {
                        return `<div data-bs-toggle="tooltip" title="${data}">
                            ${moment(data).fromNow()}
                        </div>`;
                    }
                },
                { // Attempts
                    data: 'attempts_count',
                    render: function(data) {
                        return `<span class="badge bg-label-danger">${data}</span>`;
                    }
                },
                { // Risk Score
                    data: 'max_risk_score',
                    render: function(data) {
                        const colorClass = data >= 75 ? 'danger' : (data >= 50 ? 'warning' : 'primary');
                        return `<div class="d-flex align-items-center">
                            <div class="progress w-100" style="height: 8px;">
                                <div class="progress-bar bg-${colorClass}"
                                     role="progressbar"
                                     style="width: ${data}%"
                                     aria-valuenow="${data}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <span class="ms-2">${data}</span>
                        </div>`;
                    }
                },
                { // Attack Type
                    data: 'attack_type',
                    render: function(data) {
                        const type = data || 'unknown';
                        const colorClass = type === 'brute_force' ? 'danger' : (type === 'sql_injection' ? 'warning' : 'info');
                        const title = type.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                        return `<span class="text-truncate d-flex align-items-center">
                            <span class="badge bg-label-${colorClass} ms-1">
                                ${title}
                            </span>
                        </span>`;
                    }
                },
                { // Actions
                    data: 'ip_address',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `<div class="d-inline-block">
                            <button type="button"
                                    class="btn btn-sm btn-icon"
                                    data-bs-toggle="tooltip"
                                    title="${window.translations.view_details || 'View Details'}"
                                    onclick="viewIpDetails('${data}')">
                                <i class="bx bx-show text-primary"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-icon delete-record"
                                    data-bs-toggle="tooltip"
                                    title="${window.translations.unblock_ip || 'Unblock IP'}"
                                    onclick="unblockIp('${data}')">
                                <i class="bx bx-shield-quarter text-success"></i>
                            </button>
                        </div>`;
                    }
                }
            ]
        });

        // Initialize tooltips after DataTable render
        table.on('draw', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    }
});

// View IP Details
function viewIpDetails(ipAddress) {
    $.ajax({
        url: `/security/ip/${ipAddress}/details`,
        method: 'GET',
        success: function(response) {
            $('#ipDetailsModal .modal-body').html(response);
            $('#ipDetailsModal').modal('show');
        },
        error: function(xhr) {
            Swal.fire({
                title: window.translations.error || 'Error',
                text: xhr.responseJSON?.message || (window.translations.failed_to_load || 'Failed to load IP details'),
                icon: 'error',
                confirmButtonText: window.translations.ok || 'OK'
            });
        }
    });
}

// Unblock IP
function unblockIp(ipAddress) {
    Swal.fire({
        title: window.translations.confirm_unblock || 'Confirm Unblock',
        text: window.translations.unblock_confirmation || `Are you sure you want to unblock ${ipAddress}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: window.translations.yes_unblock || 'Yes, unblock it',
        cancelButtonText: window.translations.cancel || 'Cancel',
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: `/security/ip/${ipAddress}/unblock`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('.datatables-blocked-ips').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: window.translations.success || 'Success',
                        text: response.message || (window.translations.ip_unblocked || 'IP has been unblocked'),
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                        buttonsStyling: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: window.translations.error || 'Error',
                        text: xhr.responseJSON?.message || (window.translations.failed_to_unblock || 'Failed to unblock IP'),
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
}
