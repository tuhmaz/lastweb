'use strict';

let chartColors = {
    primary: '#696cff',
    secondary: '#8592a3',
    success: '#71dd37',
    info: '#03c3ec',
    warning: '#ffab00',
    danger: '#ff3e1d'
};

// تهيئة الرسوم البيانية
let cpuChart, memoryChart, diskChart, loadChart;

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    updateMetrics();
    setInterval(updateMetrics, 5000); // تحديث كل 5 ثواني
});

function initCharts() {
    // CPU Chart
    cpuChart = new ApexCharts(document.querySelector('#cpuChart'), {
        chart: {
            height: 200,
            type: 'line',
            zoom: { enabled: false },
            toolbar: { show: false }
        },
        series: [{ name: 'CPU Usage', data: [] }],
        colors: [chartColors.primary],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        grid: {
            borderColor: '#f1f1f1',
            padding: { top: 10, right: 0, bottom: 0, left: 0 }
        },
        xaxis: {
            categories: [],
            labels: { show: false }
        },
        yaxis: {
            max: 100,
            labels: { formatter: val => `${val}%` }
        }
    });
    cpuChart.render();

    // Memory Chart
    memoryChart = new ApexCharts(document.querySelector('#memoryChart'), {
        chart: {
            height: 200,
            type: 'donut'
        },
        series: [0, 0],
        labels: ['Used', 'Free'],
        colors: [chartColors.primary, chartColors.secondary],
        legend: { position: 'bottom' }
    });
    memoryChart.render();

    // Disk Chart
    diskChart = new ApexCharts(document.querySelector('#diskChart'), {
        chart: {
            height: 200,
            type: 'donut'
        },
        series: [0, 0],
        labels: ['Used', 'Free'],
        colors: [chartColors.warning, chartColors.secondary],
        legend: { position: 'bottom' }
    });
    diskChart.render();

    // Load Average Chart
    loadChart = new ApexCharts(document.querySelector('#loadChart'), {
        chart: {
            height: 100,
            type: 'area',
            sparkline: { enabled: true }
        },
        series: [{ name: 'Load', data: [] }],
        colors: [chartColors.info],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        }
    });
    loadChart.render();
}

async function updateMetrics() {
    try {
        const response = await fetch('/dashboard/performance/metrics/data', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        // تحديث الرسوم البيانية
        updateCharts(data);
        // تحديث المعلومات النصية
        updateStats(data);
        // تحديث وقت آخر تحديث
        updateLastUpdated(data.time);

    } catch (error) {
        console.error('Error updating metrics:', error);
    }
}

function updateCharts(data) {
    // تحديث CPU
    if (cpuChart) {
        cpuChart.updateSeries([{
            name: 'CPU Usage',
            data: [data.cpu.usage_percentage]
        }]);
    }

    // تحديث Memory
    if (memoryChart) {
        const usedMemory = (data.memory.used / data.memory.total) * 100;
        const freeMemory = 100 - usedMemory;
        memoryChart.updateSeries([usedMemory, freeMemory]);
    }

    // تحديث Disk
    if (diskChart) {
        const usedDisk = (data.disk.used / data.disk.total) * 100;
        const freeDisk = 100 - usedDisk;
        diskChart.updateSeries([usedDisk, freeDisk]);
    }

    // تحديث Load Average
    if (loadChart) {
        loadChart.updateSeries([{
            name: 'Load',
            data: data.cpu.load
        }]);
    }
}

function updateStats(data) {
    // تحديث معلومات النظام
    updateElement('system-info', `${data.os} (${data.version.system})`);
    updateElement('php-version', data.php_version);
    updateElement('laravel-version', data.version.laravel);
    updateElement('server-software', data.server_software);

    // تحديث معلومات CPU
    updateElement('cpu-cores', data.cpu.cores);
    updateElement('cpu-usage', `${data.cpu.usage_percentage.toFixed(2)}%`);
    updateElement('cpu-load', data.cpu.load.map(l => l.toFixed(2)).join(' | '));

    // تحديث معلومات الذاكرة
    updateElement('memory-total', formatBytes(data.memory.total));
    updateElement('memory-used', formatBytes(data.memory.used));
    updateElement('memory-free', formatBytes(data.memory.free));
    updateElement('memory-percentage', `${data.memory.usage_percentage}%`);

    // تحديث معلومات القرص
    updateElement('disk-total', formatBytes(data.disk.total));
    updateElement('disk-used', formatBytes(data.disk.used));
    updateElement('disk-free', formatBytes(data.disk.free));
    updateElement('disk-percentage', `${data.disk.usage_percentage}%`);

    // تحديث معلومات قاعدة البيانات
    updateElement('db-type', `${data.database.type} (${data.database.version})`);
    updateElement('db-name', data.database.name);
    updateElement('db-size', data.database.size);
}

function updateLastUpdated(timestamp) {
    const date = new Date(timestamp * 1000);
    updateElement('last-updated', date.toLocaleString());
}

function updateElement(id, value) {
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}