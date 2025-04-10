/**
 * App Calendar
 */

'use strict';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import Swal from 'sweetalert2';
import moment from 'moment'; // Import moment.js

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const calendarEl = document.getElementById('calendar');
    const addEventSidebar = document.getElementById('addEventSidebar');
    const eventForm = document.getElementById('eventForm');
    const databaseSelect = document.getElementById('select-database');
    
    // Initialize date picker
    const eventDatePicker = flatpickr('#eventStartDate', {
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });

    // Function to get events for selected database
    const fetchEvents = (database) => {
      // إضافة timestamp لتجنب التخزين المؤقت
      return fetch(`/dashboard/calendar-events?database=${database}&_=${new Date().getTime()}`)
        .then(response => response.json())
        .then(result => {
          if (result.status === 'success') {
            calendar.removeAllEvents();
            calendar.addEventSource(result.data);
          }
        })
        .catch(error => {
          console.error('Error fetching events:', error);
          Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء جلب الأحداث'
          });
        });
    };

    // Handle database selection change
    databaseSelect.addEventListener('change', function() {
      fetchEvents(this.value);
    });

    // Initialize the calendar
    const calendar = new Calendar(calendarEl, {
      plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
      initialView: 'dayGridMonth',
      firstDay: 6,
      height: 800,
      selectable: true,
      editable: true,
      dayMaxEvents: 2,
      eventResizableFromStart: true,
      headerToolbar: {
        start: 'prev,next today',
        center: 'title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      events: function(info, successCallback, failureCallback) {
        const database = databaseSelect.value;
        // إضافة timestamp لتجنب التخزين المؤقت
        fetch(`/dashboard/calendar-events?database=${database}&_=${new Date().getTime()}`)
          .then(response => response.json())
          .then(result => {
            if (result.status === 'success') {
              successCallback(result.data);
            } else {
              failureCallback(new Error(result.message));
            }
          })
          .catch(error => {
            failureCallback(error);
          });
      },
      select: function(info) {
        eventDatePicker.setDate(info.start);
        const offcanvas = new bootstrap.Offcanvas(addEventSidebar);
        offcanvas.show();
      },
      eventClick: function(info) {
        const event = info.event;
        
        // تحديث قيم النموذج
        document.getElementById('eventTitle').value = event.title;
        document.getElementById('eventDescription').value = event.extendedProps.description || '';
        document.getElementById('eventDatabase').value = event.extendedProps.database;
        
        // تحديث التاريخ مع مراعاة المنطقة الزمنية
        const eventDate = moment(event.start).format('YYYY-MM-DD HH:mm');
        eventDatePicker.setDate(eventDate);

        // تحديث زر الإرسال
        const submitBtn = document.querySelector('.btn-add-event');
        submitBtn.textContent = 'تحديث';
        submitBtn.classList.remove('btn-add-event');
        submitBtn.classList.add('btn-update-event');
        submitBtn.dataset.eventId = event.id;

        // إظهار زر الحذف
        const deleteBtn = document.querySelector('.btn-delete-event');
        deleteBtn.classList.remove('d-none');
        
        // فتح النافذة الجانبية
        const offcanvas = new bootstrap.Offcanvas(addEventSidebar);
        offcanvas.show();
      }
    });

    // Form submit handler
    eventForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const submitBtn = document.querySelector('.btn-add-event, .btn-update-event');
      const isUpdate = submitBtn.classList.contains('btn-update-event');
      
      const formData = {
        title: document.getElementById('eventTitle').value,
        description: document.getElementById('eventDescription').value,
        event_date: eventDatePicker.selectedDates[0],
        eventDatabase: document.getElementById('eventDatabase').value
      };

      const url = isUpdate 
        ? `/dashboard/calendar/${submitBtn.dataset.eventId}?database=${formData.eventDatabase}`
        : '/dashboard/calendar/store';

      fetch(url, {
        method: isUpdate ? 'PUT' : 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          if (isUpdate) {
            const existingEvent = calendar.getEventById(data.data.id);
            if (existingEvent) {
              existingEvent.remove();
            }
          }

          calendar.addEvent({
            id: data.data.id,
            title: data.data.title,
            start: data.data.start,
            allDay: true,
            extendedProps: {
              description: data.data.extendedProps.description,
              database: data.data.extendedProps.database
            }
          });

          bootstrap.Offcanvas.getInstance(addEventSidebar).hide();
          eventForm.reset();

          Swal.fire({
            icon: 'success',
            title: isUpdate ? 'تم تحديث الحدث بنجاح' : 'تم إضافة الحدث بنجاح',
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          throw new Error(data.message || 'حدث خطأ غير معروف');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'خطأ',
          text: error.message || 'حدث خطأ أثناء حفظ الحدث'
        });
      });
    });

    // Delete event handler
    document.querySelector('.btn-delete-event').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const eventId = document.querySelector('.btn-update-event').dataset.eventId;
      const database = document.getElementById('eventDatabase').value;
      
      Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "لا يمكن التراجع عن هذا الإجراء!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذف!',
        cancelButtonText: 'إلغاء',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        focusConfirm: false,
        focusCancel: true,
        preConfirm: () => {
          return new Promise((resolve, reject) => {
            fetch(`/dashboard/calendar/${eventId}?database=${database}`, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.status === 'success') {
                resolve(data);
              } else {
                reject(new Error(data.message || 'حدث خطأ أثناء الحذف'));
              }
            })
            .catch(error => {
              reject(error);
            });
          }).catch(error => {
            Swal.showValidationMessage(error.message);
            throw error;
          });
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const event = calendar.getEventById(eventId);
          if (event) {
            event.remove();
          }
          
          bootstrap.Offcanvas.getInstance(addEventSidebar).hide();
          
          // تحديث الأحداث بعد الحذف
          fetchEvents(database);
          
          Swal.fire({
            icon: 'success',
            title: 'تم الحذف!',
            text: 'تم حذف الحدث بنجاح.',
            timer: 1500,
            showConfirmButton: false
          });
        }
      }).catch(() => {
        // تم معالجة الخطأ في preConfirm
      });
    });

    // Reset form when sidebar is hidden
    addEventSidebar.addEventListener('hidden.bs.offcanvas', function() {
      eventForm.reset();
      const submitBtn = document.querySelector('.btn-update-event');
      if (submitBtn) {
        submitBtn.classList.remove('btn-update-event');
        submitBtn.classList.add('btn-add-event');
        submitBtn.textContent = 'إضافة';
      }
      document.querySelector('.btn-delete-event').classList.add('d-none');

      // Set the default database
      document.getElementById('eventDatabase').value = databaseSelect.value;
    });

    // Set initial database value
    document.getElementById('eventDatabase').value = databaseSelect.value;

    // Render calendar
    calendar.render();
  })();
});
