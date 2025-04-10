class PerformanceMetrics {
    constructor() {
        this.charts = {};
        this.metricsHistory = {
            cpu: [],
            memory: [],
            responseTime: []
        };
        this.init();
    }

    init() {
        this.initCharts();
        this.startMetricsUpdate();
    }

    initCharts() {
        this.initCpuChart();
        this.initMemoryChart();
        this.initResponseTimeChart();
    }

    initCpuChart() {
        const element = document.querySelector('#cpuChart');
        if (!element) return;

        const options = {
            series: [{
                name: 'CPU Usage',
                data: []
            }],
            chart: {
                type: 'area',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3
                }
            },
            xaxis: {
                type: 'datetime'
            },
            yaxis: {
                min: 0,
                max: 100,
                labels: {
                    formatter: (value) => `${value}%`
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm:ss'
                }
            }
        };

        this.charts.cpu = new ApexCharts(element, options);
        this.charts.cpu.render();
    }

    initMemoryChart() {
        const element = document.querySelector('#memoryChart');
        if (!element) return;

        const options = {
            series: [{
                name: 'Memory Usage',
                data: []
            }],
            chart: {
                type: 'area',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3
                }
            },
            xaxis: {
                type: 'datetime'
            },
            yaxis: {
                min: 0,
                max: 100,
                labels: {
                    formatter: (value) => `${value}%`
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm:ss'
                }
            }
        };

        this.charts.memory = new ApexCharts(element, options);
        this.charts.memory.render();
    }

    initResponseTimeChart() {
        const element = document.querySelector('#responseTimeChart');
        if (!element) return;

        const options = {
            series: [{
                name: 'Response Time',
                data: []
            }],
            chart: {
                type: 'line',
                height: 350,
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime'
            },
            yaxis: {
                labels: {
                    formatter: (value) => `${value}ms`
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm:ss'
                }
            }
        };

        this.charts.responseTime = new ApexCharts(element, options);
        this.charts.responseTime.render();
    }

    async updateMetrics() {
        try {
            const response = await fetch('/dashboard/performance/metrics');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();

            const timestamp = new Date().getTime();

            // تحديث CPU
            if (this.charts.cpu) {
                this.metricsHistory.cpu.push({
                    x: timestamp,
                    y: data.cpu.usage_percentage
                });
                if (this.metricsHistory.cpu.length > 20) {
                    this.metricsHistory.cpu.shift();
                }
                this.charts.cpu.updateSeries([{
                    data: this.metricsHistory.cpu
                }]);
            }

            // تحديث Memory
            if (this.charts.memory) {
                this.metricsHistory.memory.push({
                    x: timestamp,
                    y: data.memory.usage_percentage
                });
                if (this.metricsHistory.memory.length > 20) {
                    this.metricsHistory.memory.shift();
                }
                this.charts.memory.updateSeries([{
                    data: this.metricsHistory.memory
                }]);
            }

            // تحديث Response Time
            if (this.charts.responseTime) {
                this.metricsHistory.responseTime.push({
                    x: timestamp,
                    y: data.response_time
                });
                if (this.metricsHistory.responseTime.length > 20) {
                    this.metricsHistory.responseTime.shift();
                }
                this.charts.responseTime.updateSeries([{
                    data: this.metricsHistory.responseTime
                }]);
            }

            // تحديث الإحصائيات النصية
            this.updateStats(data);

        } catch (error) {
            console.error('Error updating metrics:', error);
        }
    }

    updateMetricsDisplay(data) {
        // CPU
        this.updateGauge('cpu-gauge', data.cpu.usage_percentage);
        document.getElementById('cpu-cores').textContent = data.cpu.cores;
        document.getElementById('cpu-load').textContent = data.cpu.load_average.toFixed(2);
    
        // Memory
        this.updateGauge('memory-gauge', data.memory.usage_percentage);
        document.getElementById('memory-used').textContent = this.formatBytes(data.memory.used);
        document.getElementById('memory-total').textContent = this.formatBytes(data.memory.total);
        document.getElementById('memory-free').textContent = this.formatBytes(data.memory.free);
    
        // Disk
        this.updateGauge('disk-gauge', data.disk.usage_percentage);
        document.getElementById('disk-used').textContent = this.formatBytes(data.disk.used);
        document.getElementById('disk-total').textContent = this.formatBytes(data.disk.total);
        document.getElementById('disk-free').textContent = this.formatBytes(data.disk.free);
    
        // Update last refresh time
        document.getElementById('last-updated').textContent = new Date(data.last_updated).toLocaleString();
    }
    

    updateStats(data) {
        // تحديث النص للإحصائيات
        const elements = {
            cpuUsage: document.querySelector('#cpuUsage'),
            memoryUsage: document.querySelector('#memoryUsage'),
            diskUsage: document.querySelector('#diskUsage'),
            uptime: document.querySelector('#uptime'),
            requestRate: document.querySelector('#requestRate')
        };

        if (elements.cpuUsage) {
            elements.cpuUsage.textContent = `${data.cpu.usage_percentage}%`;
        }
        if (elements.memoryUsage) {
            elements.memoryUsage.textContent = `${data.memory.usage_percentage}%`;
        }
        if (elements.diskUsage) {
            elements.diskUsage.textContent = `${data.disk.usage_percentage}%`;
        }
        if (elements.uptime) {
            elements.uptime.textContent = this.formatUptime(data.uptime);
        }
        if (elements.requestRate) {
            elements.requestRate.textContent = `${data.request_rate}/min`;
        }
    }

    formatUptime(seconds) {
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        
        const parts = [];
        if (days > 0) parts.push(`${days}d`);
        if (hours > 0) parts.push(`${hours}h`);
        if (minutes > 0) parts.push(`${minutes}m`);
        
        return parts.join(' ') || '0m';
    }

    startMetricsUpdate() {
        // تحديث فوري عند التحميل
        this.updateMetrics();
        // تحديث كل 5 ثواني
        setInterval(() => this.updateMetrics(), 5000);
    }
}

// تأكد من تحميل DOM قبل التنفيذ
document.addEventListener('DOMContentLoaded', () => {
    new PerformanceMetrics();
});