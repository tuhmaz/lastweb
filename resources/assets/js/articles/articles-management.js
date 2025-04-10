document.addEventListener('DOMContentLoaded', function() {
  // Delete article confirmation
  document.querySelectorAll('.delete-article').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const articleId = this.dataset.id;
      const articleTitle = this.dataset.title;

      Swal.fire({
        title: window.translations.deleteConfirmation,
        text: window.translations.deleteArticleConfirmation.replace(':title', articleTitle),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: window.translations.yes,
        cancelButtonText: window.translations.no,
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(function(result) {
        if (result.isConfirmed) {
          document.getElementById('delete-form-' + articleId).submit();
        }
      });
    });
  });

  // Initialize DataTable
  const table = $('.articles-table').DataTable({
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    language: {
      search: '',
      searchPlaceholder: window.translations.search,
      lengthMenu: window.translations.show + ' _MENU_',
      info: window.translations.showing + ' _START_ ' + window.translations.to + ' _END_ ' + window.translations.of + ' _TOTAL_ ' + window.translations.entries,
      paginate: {
        first: window.translations.first,
        last: window.translations.last,
        next: window.translations.next,
        previous: window.translations.previous
      }
    },
    pageLength: 10,
    responsive: true,
    order: [[0, 'desc']]
  });

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Stats cards animation
  const statsCards = document.querySelectorAll('.stats-card');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
      }
    });
  }, { threshold: 0.1 });

  statsCards.forEach(card => observer.observe(card));
});
