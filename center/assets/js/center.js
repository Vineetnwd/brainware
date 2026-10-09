/**
 * Brainware Center Portal JS
 */

document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.querySelector('.center-sidebar-toggle');
  const sidebar = document.querySelector('.center-sidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  // Auto-dismiss alerts
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(alert => {
    setTimeout(() => {
      if (alert && alert.parentElement) {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.4s ease';
        setTimeout(() => alert.remove(), 400);
      }
    }, 5000);
  });
});
