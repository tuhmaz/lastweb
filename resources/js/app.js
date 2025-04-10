import './bootstrap';
import '../css/app.css';
import '../css/pages/dashboard.css';

// Import ECharts
import * as echarts from 'echarts/core';
import { BarChart, LineChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';

// Register ECharts components
echarts.use([
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LegendComponent,
  BarChart,
  LineChart,
  CanvasRenderer
]);

// Make echarts available globally
window.echarts = echarts;

// Asset imports
import.meta.glob([
  '../assets/img/**',
  '../assets/vendor/fonts/**'
]);

// Initialize Summernote
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote editors if they exist
    const editors = document.querySelectorAll('.summernote');
    if (editors.length > 0 && typeof $.fn.summernote !== 'undefined') {
        editors.forEach(editor => {
            $(editor).summernote({
                lang: 'ar-AR',
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    }
});

// Initialize dashboard chart
const initializeChart = () => {
    const chartDom = document.getElementById('contentChart');
    if (chartDom) {
        try {
            const myChart = echarts.init(chartDom);
            const analyticsData = chartDom.dataset.analytics ? JSON.parse(chartDom.dataset.analytics) : [];
            
            const option = {
                title: { 
                    text: 'تحليلات المحتوى',
                    left: 'center',
                    top: 0
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'cross' }
                },
                legend: {
                    data: ['المشاهدات', 'التعليقات'],
                    bottom: '0%'
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '60px',
                    top: '60px',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: analyticsData.map(item => item.date),
                    axisLabel: { rotate: 45 },
                    boundaryGap: true
                },
                yAxis: [
                    {
                        type: 'value',
                        name: 'المشاهدات',
                        position: 'left',
                        axisLine: { show: true, lineStyle: { color: '#5470c6' } }
                    },
                    {
                        type: 'value',
                        name: 'التعليقات',
                        position: 'right',
                        axisLine: { show: true, lineStyle: { color: '#91cc75' } }
                    }
                ],
                series: [
                    {
                        name: 'المشاهدات',
                        type: 'bar',
                        data: analyticsData.map(item => item.views),
                        itemStyle: { color: '#5470c6' },
                        barWidth: '60%'
                    },
                    {
                        name: 'التعليقات',
                        type: 'line',
                        yAxisIndex: 1,
                        data: analyticsData.map(item => item.comments),
                        itemStyle: { color: '#91cc75' },
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 8,
                        lineStyle: { width: 3 }
                    }
                ]
            };
            
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        } catch (error) {
            console.error('Error initializing chart:', error);
        }
    }
};

// Handle user online/offline status
let isOnline = true;

// Function to update user status
async function updateUserStatus(status) {
    try {
        await fetch('/api/user/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                is_online: status,
                status: status ? 'online' : 'offline',
                last_seen: new Date().toISOString()
            })
        });
    } catch (error) {
        console.error('Error updating user status:', error);
    }
}

// Update status when user closes tab or browser
window.addEventListener('beforeunload', function() {
    if (isOnline) {
        // Use sendBeacon for more reliable delivery during page unload
        const data = new FormData();
        data.append('is_online', false);
        data.append('status', 'offline');
        data.append('last_seen', new Date().toISOString());
        data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        navigator.sendBeacon('/api/user/status', data);
    }
});

// Update status when user goes offline
window.addEventListener('offline', function() {
    isOnline = false;
    updateUserStatus(false);
});

// Update status when user comes back online
window.addEventListener('online', function() {
    isOnline = true;
    updateUserStatus(true);
});

// Update user's last activity periodically
setInterval(function() {
    if (isOnline) {
        updateUserStatus(true);
    }
}, 300000); // every 5 minutes

// Initialize status when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateUserStatus(true);
    initializeChart();
});
