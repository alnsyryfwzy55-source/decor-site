// فتح/إغلاق القائمة الجانبية على الجوال
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
if (sidebarToggle && adminSidebar) {
  sidebarToggle.addEventListener('click', () => {
    adminSidebar.classList.toggle('open');
  });
}

// تأكيد قبل أي عملية حذف
document.querySelectorAll('[data-confirm]').forEach(link => {
  link.addEventListener('click', (e) => {
    if (!confirm(link.dataset.confirm)) {
      e.preventDefault();
    }
  });
});
