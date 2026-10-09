/**
 * Brainware Admin Panel Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  // Toggle Mobile Sidebar
  const toggleBtn = document.querySelector('.sidebar-toggle-btn');
  const sidebar = document.querySelector('.admin-sidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  // Auto-dismiss alert notifications
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

// Confirmation helper for deletes
function confirmDelete(message = 'Are you sure you want to delete this record? This action cannot be undone.') {
  return confirm(message);
}
