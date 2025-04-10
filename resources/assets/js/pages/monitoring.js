'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // تهيئة الخريطة
  const initMap = () => {
    const mapElement = document.getElementById('visitor-map');
    if (!mapElement) return;

    // إنشاء خريطة بسيطة
    const map = L.map('visitor-map', {
      center: [20, 0],
      zoom: 2,
      minZoom: 1,
      maxZoom: 6,
      zoomControl: true,
      attributionControl: true
    });
    
    // إضافة طبقة خلفية بسيطة بدلاً من صور الخرائط
    L.rectangle([[-90, -180], [90, 180]], {
      color: "#e0e0e0",
      weight: 1,
      fillColor: "#f8f8f8",
      fillOpacity: 1
    }).addTo(map);

    // إضافة حدود الدول من ملف GeoJSON
    fetch('/assets/js/world-countries.json')
      .then(response => response.json())
      .then(data => {
        L.geoJSON(data, {
          style: {
            color: '#cccccc',
            weight: 1,
            fillColor: '#e8e8e8',
            fillOpacity: 0.5
          }
        }).addTo(map);
        
        // إضافة طبقة لعرض مواقع الزوار
        window.visitorMarkersLayer = L.layerGroup().addTo(map);
        
        // تحديث مواقع الزوار على الخريطة
        updateVisitorLocations();
      })
      .catch(error => {
        console.error('Error loading GeoJSON:', error);
        // إضافة طبقة خلفية بسيطة في حالة فشل تحميل GeoJSON
        L.tileLayer('/assets/img/map/fallback-tile.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);
      });

    window.visitorMap = map;
  };
  
  // وظيفة جديدة لتحديث مواقع الزوار على الخريطة
  const updateVisitorLocations = async () => {
    try {
      if (!window.visitorMap || !window.visitorMarkersLayer) return;
      
      // الحصول على بيانات الزوار النشطين
      const response = await fetch('/dashboard/monitoring/active-visitors/data', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          Accept: 'application/json'
        }
      });
      
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const data = await response.json();
      
      // مسح العلامات السابقة
      window.visitorMarkersLayer.clearLayers();
      
      // إضافة علامات للزوار النشطين
      if (data.visitors && Array.isArray(data.visitors)) {
        data.visitors.forEach(visitor => {
          // التحقق من وجود إحداثيات صالحة
          if (visitor.latitude && visitor.longitude) {
            const marker = L.circleMarker([visitor.latitude, visitor.longitude], {
              radius: 5,
              fillColor: visitor.user_id ? '#4CAF50' : '#2196F3',
              color: '#fff',
              weight: 1,
              opacity: 1,
              fillOpacity: 0.8
            });
            
            // إضافة معلومات عند النقر على العلامة
            const popupContent = `
              <div class="visitor-popup">
                <div><strong>IP:</strong> ${visitor.ip_address || 'Unknown'}</div>
                <div><strong>Location:</strong> ${visitor.country || 'Unknown'}, ${visitor.city || 'Unknown'}</div>
                <div><strong>Browser:</strong> ${visitor.browser || 'Unknown'}</div>
                <div><strong>OS:</strong> ${visitor.os || 'Unknown'}</div>
                <div><strong>Last Activity:</strong> ${new Date(visitor.last_activity).toLocaleString()}</div>
              </div>
            `;
            marker.bindPopup(popupContent);
            
            // إضافة العلامة إلى الطبقة
            window.visitorMarkersLayer.addLayer(marker);
          }
        });
      }
    } catch (error) {
      console.error('Error updating visitor locations:', error);
    }
  };

  // تهيئة الرسم البياني
  const initChart = () => {
    const chartElement = document.getElementById('visitor-chart');
    if (!chartElement) return;

    const visitorChart = new ApexCharts(chartElement, {
      chart: {
        type: 'line',
        height: 300,
        toolbar: { show: false },
        zoom: { enabled: false }
      },
      series: [{ name: 'Visitors', data: [] }],
      xaxis: { type: 'datetime' },
      stroke: { curve: 'smooth', width: 2 },
      colors: ['#696cff']
    });
    visitorChart.render();
    window.visitorChart = visitorChart;
  };

  // تحديث جدول المستخدمين النشطين
  const updateActiveUsersTable = users => {
    const tableBody = document.querySelector('#active-users-table tbody');
    if (!tableBody) return;

    tableBody.innerHTML = users
      .map(
        user => `
            <tr>
                <td>${user.user_id || 'Guest'}</td>
                <td>${user.ip_address || 'Unknown'}</td>
                <td>${new Date(user.last_activity).toLocaleString()}</td>
                <td>${user.url || '-'}</td>
                <td>${user.browser || '-'}</td>
                <td>${user.os || '-'}</td>
            </tr>
        `
      )
      .join('');
  };

  // تحديث إحصائيات الطلبات
  const updateRequestStats = stats => {
    const totalElement = document.getElementById('total-requests');
    const onlineElement = document.getElementById('online-requests');
    const offlineElement = document.getElementById('offline-requests');

    if (totalElement) totalElement.textContent = stats.total;
    if (onlineElement) onlineElement.textContent = stats.online;
    if (offlineElement) offlineElement.textContent = stats.offline;
  };

  // تحديث الإحصائيات العامة
  const updateStats = async () => {
    try {
      const response = await fetch('/dashboard/monitoring/stats', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          Accept: 'application/json'
        }
      });
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const data = await response.json();

      console.log('Received stats:', data); // للتحقق من البيانات

      // تحديث إحصائيات الطلبات
      if (data.data?.requestStats) {
        updateRequestStats(data.data.requestStats);
      }

      // تحديث جدول المستخدمين النشطين
      if (data.activeUsers) {
        updateActiveUsersTable(data.activeUsers);
      }

      // تحديث الرسم البياني
      if (window.visitorChart && data.visitorStats?.history) {
        window.visitorChart.updateSeries([
          {
            name: 'Visitors',
            data: data.visitorStats.history.map(item => ({
              x: item.timestamp,
              y: item.count
            }))
          }
        ]);
      }

      // تحديث وقت آخر تحديث
      const lastUpdatedSpan = document.getElementById('last-updated');
      if (lastUpdatedSpan) lastUpdatedSpan.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;

      // تحديث عدد المستخدمين النشطين
      const totalUsersBadge = document.getElementById('total-users');
      if (totalUsersBadge && Array.isArray(data.activeUsers)) totalUsersBadge.textContent = data.activeUsers.length;
    } catch (error) {
      console.error('Error updating stats:', error);
    }
  };

  // تهيئة الخريطة والرسم البياني
  initMap();
  initChart();

  // التحديث الأولي للإحصائيات
  updateStats();

  // تحديث الإحصائيات كل 5 ثواني
  setInterval(updateStats, 5000);

  // وظائف إدارة الأخطاء
  const errorLogTableBody = document.querySelector('#error-log-table tbody');
  const clearErrorsButton = document.getElementById('clear-errors');

  // وظيفة لجلب وتحديث الأخطاء
  const updateErrorLogs = async () => {
    try {
      const response = await fetch('/dashboard/monitoring/error-logs', {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          Accept: 'application/json'
        }
      });

      if (!response.ok) throw new Error('Network response was not ok');

      const result = await response.json();
      if (result.status === 'success' && result.data) {
        const errors = result.data.recent;
        errorLogTableBody.innerHTML = errors
          .map(
            error => `
                    <tr>
                        <td>${error.timestamp}</td>
                        <td>${error.type}</td>
                        <td>${error.message}</td>
                        <td>${error.file}</td>
                        <td>${error.line}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-error" data-error-id="${error.id}">
                              Delete
                            </button>
                        </td>
                    </tr>
                `
          )
          .join('');
      }
    } catch (error) {
      console.error('Error fetching error logs:', error);
    }
  };

  // وظيفة لحذف خطأ
  const deleteError = async errorId => {
    try {
      const response = await fetch('/dashboard/monitoring/delete-error', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          Accept: 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ errorId })
      });

      if (!response.ok) throw new Error('Network response was not ok');

      const result = await response.json();
      if (result.status === 'success') {
        alert('Error deleted successfully');
        updateErrorLogs(); // تحديث الجدول بعد الحذف
      }
    } catch (error) {
      console.error('Error deleting error:', error);
    }
  };

  // تحديث الأخطاء كل 5 ثواني
  updateErrorLogs();
  setInterval(updateErrorLogs, 5000);

  // إضافة حدث لحذف خطأ
  if (errorLogTableBody) {
    errorLogTableBody.addEventListener('click', function (event) {
      if (event.target.classList.contains('delete-error')) {
        const errorId = event.target.getAttribute('data-error-id');
        if (confirm('Are you sure you want to delete this error?')) {
          deleteError(errorId);
        }
      }
    });
  }

  // إضافة حدث لمسح السجل بالكامل
  if (clearErrorsButton) {
    clearErrorsButton.addEventListener('click', function () {
      if (confirm('Are you sure you want to clear the entire error log?')) {
        fetch('/dashboard/monitoring/clear-error-logs', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            Accept: 'application/json'
          }
        })
          .then(response => {
            if (response.ok) {
              alert('Error log cleared successfully');
              updateErrorLogs(); // تحديث الجدول بعد المسح
            }
          })
          .catch(error => {
            console.error('Error clearing error log:', error);
          });
      }
    });
  }
});
