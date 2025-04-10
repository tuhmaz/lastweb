'use strict';

$(function () {
  var dt_news_table = $('.datatables-news');

  // Initialize TinyMCE
  tinymce.init({
      selector: '#content',
      plugins: 'advlist autolink lists link image charmap preview anchor pagebreak',
      toolbar_mode: 'floating',
      height: 400
  });

  // DataTable initialization
  if (dt_news_table.length) {
      var dt_news = dt_news_table.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: '{{ route("dashboard.news.data") }}?country={{ $currentCountry }}',
              type: 'GET'
          },
          columns: [
              {
                  data: 'image',
                  render: function(data) {
                      return `<div class="avatar"><img src="${data}" class="rounded" onerror="this.src='{{ asset('assets/img/illustrations/default_news_image.jpg') }}'"></div>`;
                  }
              },
              {
                  data: 'title',
                  render: function(data, type, row) {
                      return `<div class="d-flex flex-column">
                          <span class="fw-semibold">${data}</span>
                          <small class="text-muted">${row.meta_description || ''}</small>
                      </div>`;
                  }
              },
              {
                  data: 'category.name',
                  render: function(data) {
                      return `<span class="badge bg-label-primary">${data}</span>`;
                  }
              },
              {
                  data: 'is_active',
                  render: function(data, type, row) {
                      return `<div class="form-check form-switch d-flex justify-content-center">
                          <input type="checkbox" class="form-check-input toggle-status" ${data ? 'checked' : ''}
                              data-id="${row.id}" data-url="/dashboard/news/${row.id}/toggle-status"
                              style="width: 40px; height: 20px; cursor: pointer;">
                          <label class="form-check-label ms-2" style="cursor: pointer;">
                              <span class="badge ${data ? 'bg-success' : 'bg-secondary'}">
                                  ${data ? '{{ __("Active") }}' : '{{ __("Inactive") }}'}
                              </span>
                          </label>
                      </div>`;
                  }
              },
              {
                  data: 'is_featured',
                  render: function(data, type, row) {
                      return `<div class="form-check form-switch d-flex justify-content-center">
                          <input type="checkbox" class="form-check-input toggle-featured" ${data ? 'checked' : ''}
                              data-id="${row.id}" data-url="/dashboard/news/${row.id}/toggle-featured"
                              style="width: 40px; height: 20px; cursor: pointer;">
                          <label class="form-check-label ms-2" style="cursor: pointer;">
                              <span class="badge ${data ? 'bg-warning' : 'bg-secondary'}">
                                  ${data ? '{{ __("Featured") }}' : '{{ __("Normal") }}'}
                              </span>
                          </label>
                      </div>`;
                  }
              },
              {
                  data: 'views',
                  render: function(data) {
                      return `<span class="badge bg-label-info">${data}</span>`;
                  }
              },
              {
                  data: 'created_at',
                  render: function(data) {
                      return `<span class="text-muted"><i class="ti ti-calendar me-1"></i>${data}</span>`;
                  }
              },
              {
                  data: 'id',
                  render: function(data, type, row) {
                      return `<div class="d-flex gap-2">
                          <a href="/dashboard/news/${data}/edit?country={{ $currentCountry }}"
                             class="btn btn-icon btn-label-primary btn-sm"
                             data-bs-toggle="tooltip"
                             title="{{ __('Edit') }}">
                              <i class="ti ti-edit"></i>
                          </a>
                          <button type="button"
                                  class="btn btn-icon btn-label-danger btn-sm delete-record"
                                  data-id="${data}"
                                  data-country="{{ $currentCountry }}"
                                  data-bs-toggle="tooltip"
                                  title="{{ __('Delete') }}">
                              <i class="ti ti-trash"></i>
                          </button>
                      </div>`;
                  }
              }
          ],
          order: [[6, 'desc']],
          dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
          language: {
              url: '{{ asset("assets/vendor/libs/datatables/i18n/" . app()->getLocale() . ".json") }}'
          }
      });
  }

  // Handle delete
  $(document).on('click', '.delete-record', function() {
      var id = $(this).data('id');
      var country = $(this).data('country');
      var token = $('meta[name="csrf-token"]').attr('content');

      Swal.fire({
          title: '{{ __("Are you sure?") }}',
          text: '{{ __("You won\'t be able to revert this!") }}',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: '{{ __("Yes, delete it!") }}',
          customClass: {
              confirmButton: 'btn btn-danger me-3',
              cancelButton: 'btn btn-label-secondary'
          },
          buttonsStyling: false
      }).then(function(result) {
          if (result.value) {
              $.ajax({
                  url: '/dashboard/news/' + id,
                  type: 'DELETE',
                  headers: {
                      'X-CSRF-TOKEN': token
                  },
                  data: {
                      country: country
                  },
                  success: function(response) {
                      dt_news.ajax.reload();
                      Swal.fire({
                          icon: 'success',
                          title: '{{ __("Deleted!") }}',
                          text: response.message,
                          customClass: {
                              confirmButton: 'btn btn-success'
                          }
                      });
                  },
                  error: function(xhr) {
                      Swal.fire({
                          icon: 'error',
                          title: '{{ __("Error!") }}',
                          text: xhr.responseJSON?.message || '{{ __("Something went wrong!") }}',
                          customClass: {
                              confirmButton: 'btn btn-danger'
                          }
                      });
                  }
              });
          }
      });
  });

  // Handle status toggle
  $(document).on('change', '.toggle-status', function() {
      var $this = $(this);
      var url = $this.data('url');

      $.ajax({
          url: url,
          type: 'PATCH',
          data: {
              _token: '{{ csrf_token() }}'
          },
          success: function(response) {
              var badge = $this.siblings('label').find('.badge');
              if ($this.is(':checked')) {
                  badge.removeClass('bg-secondary').addClass('bg-success');
                  badge.text('{{ __("Active") }}');
              } else {
                  badge.removeClass('bg-success').addClass('bg-secondary');
                  badge.text('{{ __("Inactive") }}');
              }
          },
          error: function(xhr) {
              // Revert the checkbox state
              $this.prop('checked', !$this.is(':checked'));

              Swal.fire({
                  icon: 'error',
                  title: '{{ __("Error!") }}',
                  text: xhr.responseJSON?.message || '{{ __("Something went wrong!") }}',
                  customClass: {
                      confirmButton: 'btn btn-danger'
                  }
              });
          }
      });
  });

  // Handle featured toggle
  $(document).on('change', '.toggle-featured', function() {
      var $this = $(this);
      var url = $this.data('url');
      var token = $('meta[name="csrf-token"]').attr('content');
      var isChecked = $this.is(':checked');

      $.ajax({
          url: url,
          type: 'PATCH',
          headers: {
              'X-CSRF-TOKEN': token
          },
          beforeSend: function() {
              $this.prop('disabled', true);
          },
          success: function(response) {
              var badge = $this.siblings('label').find('.badge');
              if (isChecked) {
                  badge.removeClass('bg-secondary').addClass('bg-warning');
                  badge.text('{{ __("Featured") }}');
              } else {
                  badge.removeClass('bg-warning').addClass('bg-secondary');
                  badge.text('{{ __("Normal") }}');
              }
          },
          error: function(xhr) {
              // Revert the checkbox state
              $this.prop('checked', !isChecked);
              
              Swal.fire({
                  icon: 'error',
                  title: '{{ __("Error!") }}',
                  text: xhr.responseJSON?.message || '{{ __("Something went wrong!") }}',
                  customClass: {
                      confirmButton: 'btn btn-danger'
                  }
              });
          },
          complete: function() {
              $this.prop('disabled', false);
          }
      });
  });

  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
