document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('documents');
  const output = document.getElementById('file-names');
  if (input && output) {
    input.addEventListener('change', () => {
      const files = Array.from(input.files || []);
      output.textContent = files.length
        ? files.map(file => `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`).join(' · ')
        : 'You can select multiple files';
      output.classList.toggle('has-files', files.length > 0);
    });
  }
  document.querySelectorAll('.alert').forEach(alert => {
    if (!alert.classList.contains('error')) {
      setTimeout(() => { alert.style.opacity = '0'; alert.style.transform = 'translateY(-5px)'; }, 4500);
      setTimeout(() => alert.remove(), 5000);
    }
  });
});
