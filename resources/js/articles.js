'use strict';

$(function () {
  // Initialize DataTable
  const dt_articles = $('#articlesTable').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
      url: `/dashboard/articles/data?country=${country}`,
      data: function (d) {
        return {
          ...d,
          class_id: $('#class_id').val(),
          subject_id: $('#subject_id').val(),
          semester_id: $('#semester_id').val(),
          search: $('#searchArticle').val()
        };
      }
    },
    columns: [
      { data: 'title' },
      { data: 'subject' },
      { data: 'semester' },
      { data: 'class' },
      { data: 'status' },
      { data: 'created_at' },
      { data: null, orderable: false }
    ],
    columnDefs: [
      {
        targets: 0,
        render: function (data, type, row) {
          return row.image ?
            '<div class="d-flex align-items-center">' +
              '<img src="' + row.image + '" alt="' + row.title + '" class="rounded me-2" width="48">' +
              '<div>' +
                '<h6 class="mb-0">' + row.title + '</h6>' +
              '</div>' +
            '</div>'
            : row.title;
        }
      },
      {
        targets: 4,
        render: function (data, type, row) {
          // Convert status to number to ensure correct comparison
          const status = parseInt(row.status);
          return '<span class="badge bg-' + (status === 1 ? 'success' : 'warning') + '">' +
            (status === 1 ? 'Published' : 'Draft') + '</span>';
        }
      },
      {
        targets: -1,
        render: function (data, type, row) {
          return '<div class="d-flex gap-2">' +
            '<a href="' + row.show_url + '" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>' +
            '<a href="' + row.edit_url + '" class="btn btn-sm btn-primary"><i class="ti ti-pencil"></i></a>' +
            '<form action="' + row.delete_url + '" method="POST" class="delete-form d-inline">' +
              '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">' +
              '<input type="hidden" name="_method" value="DELETE">' +
              '<button type="submit" class="btn btn-sm btn-danger"><i class="ti ti-trash"></i></button>' +
            '</form>' +
          '</div>';
        }
      }
    ],
    order: [[5, 'desc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    language: {
      search: '',
      searchPlaceholder: 'Search Articles...',
      paginate: {
        previous: '<i class="ti ti-chevron-left"></i>',
        next: '<i class="ti ti-chevron-right"></i>'
      }
    },
    drawCallback: function () {
      // Re-initialize delete confirmation after each draw
      initializeDeleteConfirmation();
    }
  });

  // Filter handlers
  $('#class_id, #subject_id, #semester_id').on('change', function() {
    dt_articles.ajax.reload();
  });

  // Search handler
  let searchTimeout;
  $('#searchArticle').on('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      dt_articles.ajax.reload();
    }, 500);
  });

  // Dynamic filtering for subjects and semesters based on class
  $('#class_id').on('change', function() {
    const classId = $(this).val();
    
    // Filter subjects
    $('#subject_id option').each(function() {
      const $option = $(this);
      const dataClass = $option.data('class');
      
      if (!dataClass || dataClass === '' || dataClass === parseInt(classId)) {
        $option.show();
      } else {
        $option.hide();
        if ($option.is(':selected')) {
          $('#subject_id').val('');
        }
      }
    });

    // Filter semesters
    $('#semester_id option').each(function() {
      const $option = $(this);
      const dataClass = $option.data('class');
      
      if (!dataClass || dataClass === '' || dataClass === parseInt(classId)) {
        $option.show();
      } else {
        $option.hide();
        if ($option.is(':selected')) {
          $('#semester_id').val('');
        }
      }
    });
  });

  // Initialize delete confirmation
  function initializeDeleteConfirmation() {
    $('.delete-form').on('submit', function(e) {
      e.preventDefault();
      const form = this;
      
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  }

  // Initial call to set up delete confirmation
  initializeDeleteConfirmation();
});
