'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // تهيئة التقويم
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        direction: 'rtl',
        locale: 'ar',
        height: 800,
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        firstDay: 6, // السبت
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            week: 'أسبوع',
            day: 'يوم',
            list: 'قائمة'
        },
        events: function(info, successCallback, failureCallback) {
            const database = document.getElementById('select-database')?.value || 'mysql';

            fetch(`/dashboard/calendar-events?database=${database}&_=${new Date().getTime()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    console.log('Raw events data:', result.data);
                    successCallback(result.data);
                } else {
                    console.error('Failed to fetch events:', result.message);
                    failureCallback(new Error(result.message));
                }
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
        },
        eventClick: function(info) {
          // تنسيق التاريخ بالعربية
          const eventDate = new Date(info.event.start);
          const dateOptions = {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
          };
          const formattedDate = new Intl.DateTimeFormat('ar-SA', dateOptions).format(eventDate);

          // تعديل طريقة استرجاع الوصف
const description = info.event.extendedProps.description || 'لا يوجد وصف';

// وإضافة تحقق من القيمة الفارغة
if (description.trim() === '') {
    description = 'لا يوجد وصف';
}

          // إنشاء محتوى النافذة المنبثقة
          const modalContent = `
              <div class="modal-event-details">
                  <div class="event-date mb-3">
                      <i class="ti ti-calendar me-1"></i>
                      <strong>${formattedDate}</strong>
                  </div>
                  <div class="event-description">
                      <i class="ti ti-note me-1"></i>
                      <p>${description}</p>
                  </div>
                  <div class="event-database mt-3">
                      <i class="ti ti-database me-1"></i>
                      <small class="text-muted">قاعدة البيانات: ${document.getElementById('select-database')?.value || 'mysql'}</small>
                  </div>
              </div>
          `;

          // عرض النافذة المنبثقة
          Swal.fire({
              title: info.event.title,
              html: modalContent,
              icon: 'info',
              showCloseButton: true,
              showConfirmButton: false,
              customClass: {
                  popup: 'event-details-modal',
                  title: 'event-title',
                  content: 'event-content'
              }
          });
      }
    });

    // تهيئة التقويم
    calendar.render();

    // استمع لتغييرات قاعدة البيانات
    document.getElementById('select-database')?.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // تحديث دوري للأحداث كل 30 ثانية
    setInterval(() => calendar.refetchEvents(), 30000);
});
