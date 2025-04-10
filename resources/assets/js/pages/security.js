/**
 * Security Dashboard JavaScript
 */

'use strict';

// DOM Elements
const securityDashboard = {
    init() {
        this.initEventHandlers();
        this.initTooltips();
        this.initCharts();
    },

    initEventHandlers() {
        // Handle IP blocking
        document.querySelectorAll('[data-action="block-ip"]').forEach(button => {
            button.addEventListener('click', this.handleBlockIp.bind(this));
        });

        // Handle log resolution
        document.querySelectorAll('[data-action="resolve-log"]').forEach(button => {
            button.addEventListener('click', this.handleResolveLog.bind(this));
        });

        // Handle filters
        const filterForm = document.querySelector('#security-filters');
        if (filterForm) {
            filterForm.addEventListener('submit', this.handleFilter.bind(this));
        }
    },

    initTooltips() {
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    async handleBlockIp(event) {
        const button = event.currentTarget;
        const ip = button.dataset.ip;

        try {
            const response = await fetch('/api/dashboard/security/blocked-ips', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ip_address: ip,
                    reason: 'Blocked from security dashboard'
                })
            });

            const data = await response.json();

            if (response.ok) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });

                // Update UI
                button.closest('tr').classList.add('table-danger');
                button.disabled = true;
            } else {
                throw new Error(data.message || 'Failed to block IP');
            }
        } catch (error) {
            console.error('Error blocking IP:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        }
    },

    async handleResolveLog(event) {
        const button = event.currentTarget;
        const logId = button.dataset.logId;

        try {
            const { value: notes } = await Swal.fire({
                title: 'Resolution Notes',
                input: 'textarea',
                inputLabel: 'Please enter resolution notes',
                inputPlaceholder: 'Type your notes here...',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write something!';
                    }
                }
            });

            if (notes) {
                const response = await fetch(`/api/dashboard/security/logs/${logId}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ notes })
                });

                const data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Update UI
                    const row = button.closest('tr');
                    row.querySelector('.status-badge').textContent = 'Resolved';
                    row.querySelector('.status-badge').className = 'badge bg-success';
                    button.disabled = true;
                } else {
                    throw new Error(data.message || 'Failed to resolve log');
                }
            }
        } catch (error) {
            console.error('Error resolving log:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message
            });
        }
    },

    handleFilter(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const url = new URL(window.location.href);
        
        // Get all form data
        const formData = new FormData(form);
        
        // Update URL with form data
        for (const [key, value] of formData.entries()) {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        }
        
        // Navigate to filtered URL
        window.location.href = url.toString();
    },

    initCharts() {
        // Only initialize charts if we're on the analytics page
        if (!document.querySelector('#securityEventsChart')) return;

        // Security Events Chart
        const eventsChartEl = document.querySelector('#securityEventsChart');
        const eventsChartConfig = {
            series: [{
                name: 'Events',
                data: JSON.parse(eventsChartEl.dataset.events)
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: JSON.parse(eventsChartEl.dataset.dates)
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                }
            }
        };

        const eventsChart = new ApexCharts(eventsChartEl, eventsChartConfig);
        eventsChart.render();

        // Severity Distribution Chart
        const severityChartEl = document.querySelector('#severityDistributionChart');
        if (severityChartEl) {
            const severityChartConfig = {
                series: JSON.parse(severityChartEl.dataset.values),
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: JSON.parse(severityChartEl.dataset.labels),
                colors: ['#435ebe', '#fb7171', '#ff9f43', '#00cfe8'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const severityChart = new ApexCharts(severityChartEl, severityChartConfig);
            severityChart.render();
        }
    }
};

// Initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    securityDashboard.init();
});
