'use strict';

$(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Setup AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const tableBody = $('#users-table-body');
    const pagination = $('#pagination-links');
    let loadingTimer;
    const loadingDelay = 300;

    /**
     * Load users via AJAX
     * @param {string} url - The URL to load users from.
     */
    function loadUsers(url = null) {
        // Get the base URL from the data attribute
        const baseUrl = $('#filterForm').data('users-url');
        const apiUrl = url || baseUrl;

        // Clear any existing loading timer
        if (loadingTimer) clearTimeout(loadingTimer);

        // Show loading spinner
        loadingTimer = setTimeout(() => {
            tableBody.html(`
                <tr>
                    <td colspan="4" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </td>
                </tr>
            `);
        }, loadingDelay);

        // Perform AJAX request
        $.ajax({
            url: apiUrl,
            method: 'GET',
            data: {
                role: $('#UserRole').val() || '',
                search: $('#UserSearch').val() || ''
            }
        })
        .done(function(response) {
            clearTimeout(loadingTimer);

            const $response = $(response);
            const $newTableBody = $response.find('#users-table-body');
            const $newPagination = $response.find('#pagination-links');

            if ($newTableBody.length) {
                tableBody.html($newTableBody.html());
            } else {
                tableBody.html(`
                    <tr>
                        <td colspan="4" class="text-center">
                            {{ __('No results found') }}
                        </td>
                    </tr>
                `);
            }

            if ($newPagination.length) {
                pagination.html($newPagination.html());
            }

            // Update URL without refreshing
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('role', $('#UserRole').val() || '');
            newUrl.searchParams.set('search', $('#UserSearch').val() || '');
            window.history.pushState({}, '', newUrl);
        })
        .fail(function(jqXHR) {
            clearTimeout(loadingTimer);
            tableBody.html(`
                <tr>
                    <td colspan="4" class="text-center text-danger p-3">
                        <i class="ti ti-alert-circle me-1"></i>
                        ${
                            jqXHR.status === 0 
                                ? $('#filterForm').data('network-error')
                                : $('#filterForm').data('loading-error')
                        }
                    </td>
                </tr>
            `);
        });
    }

    /**
     * Debounce function to limit the rate of function execution
     * @param {Function} func - Function to debounce.
     * @param {number} wait - Time to wait in milliseconds.
     */
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(this, args);
            }, wait);
        };
    }

    // Event Listeners
    $('#UserRole').on('change', function() {
        loadUsers();
    });

    $('#UserSearch').on('input', debounce(function() {
        loadUsers();
    }, 500));

    $(document).on('click', '#pagination-links a', function(e) {
        e.preventDefault();
        loadUsers($(this).attr('href'));
    });

    $('#resetFiltersBtn').on('click', function() {
        $('#UserRole').val('').trigger('change');
        $('#UserSearch').val('');
        loadUsers();
    });

    $(document).on('click', '.delete-record', function(e) {
        if (!confirm($('#filterForm').data('delete-confirm'))) {
            e.preventDefault();
        }
    });
});
