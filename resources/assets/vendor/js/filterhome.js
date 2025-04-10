document.addEventListener('DOMContentLoaded', function() {
  const classSelect = document.getElementById('class-select');
  const subjectSelect = document.getElementById('subject-select');
  const semesterSelect = document.getElementById('semester-select');
  
  // Get CSRF token from meta tag
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Default fetch options with CSRF token
  const fetchOptions = {
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }
  };

  if (classSelect) {
    classSelect.addEventListener('change', function() {
      const classId = this.value;
      
      if (classId) {
        fetch(`/api/subjects/${classId}`, fetchOptions)
          .then(response => response.json())
          .then(data => {
            subjectSelect.innerHTML = '<option value="">أختار المادة</option>';
            data.forEach(subject => {
              const option = document.createElement('option');
              option.value = subject.id;
              option.textContent = subject.subject_name;
              subjectSelect.appendChild(option);
            });
            subjectSelect.disabled = false;
            semesterSelect.innerHTML = '<option value="">أختار الفصل</option>';
            semesterSelect.disabled = true;
          })
          .catch(error => console.error('Error:', error));
      } else {
        subjectSelect.innerHTML = '<option value="">أختار المادة</option>';
        subjectSelect.disabled = true;
        semesterSelect.innerHTML = '<option value="">أختار الفصل</option>';
        semesterSelect.disabled = true;
      }
    });

    subjectSelect.addEventListener('change', function() {
      const subjectId = this.value;
      
      if (subjectId) {
        fetch(`/api/semesters/${subjectId}`, fetchOptions)
          .then(response => response.json())
          .then(data => {
            semesterSelect.innerHTML = '<option value="">أختار الفصل</option>';
            data.forEach(semester => {
              const option = document.createElement('option');
              option.value = semester.id;
              option.textContent = semester.semester_name;
              semesterSelect.appendChild(option);
            });
            semesterSelect.disabled = false;
          })
          .catch(error => console.error('Error:', error));
      } else {
        semesterSelect.innerHTML = '<option value="">أختار الفصل</option>';
        semesterSelect.disabled = true;
      }
    });
  }
});
