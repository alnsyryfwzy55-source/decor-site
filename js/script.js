// قائمة الجوال
const navToggle = document.getElementById('navToggle');
const mainNav = document.getElementById('mainNav');

navToggle.addEventListener('click', () => {
  const isOpen = mainNav.classList.toggle('mobile-open');
  navToggle.classList.toggle('open', isOpen);
  navToggle.setAttribute('aria-expanded', isOpen);
});

mainNav.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    mainNav.classList.remove('mobile-open');
    navToggle.classList.remove('open');
  });
});

// ملاحظة: فلترة معرض الأعمال حسب التصنيف تتم الآن من السيرفر (portfolio.php)
// عبر روابط حقيقية قابلة للفهرسة من محركات البحث، وليس عبر جافاسكريبت.

// نموذج التواصل - يرسل البيانات فعليًا إلى قاعدة البيانات عبر api/contact.php
const contactForm = document.getElementById('contactForm');
const formStatus = document.getElementById('formStatus');

if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    formStatus.textContent = 'جاري الإرسال...';
    formStatus.className = 'form-status';

    try {
      const res = await fetch('api/contact.php', {
        method: 'POST',
        body: new FormData(contactForm),
      });
      const data = await res.json();

      if (data.success) {
        formStatus.textContent = 'تم استلام طلبك، سنتواصل معك قريبًا.';
        formStatus.className = 'form-status ok';
        contactForm.reset();
      } else {
        formStatus.textContent = data.error || 'حدث خطأ، حاول مجددًا.';
        formStatus.className = 'form-status err';
      }
    } catch (err) {
      formStatus.textContent = 'تعذّر الاتصال بالخادم، حاول مجددًا.';
      formStatus.className = 'form-status err';
    } finally {
      submitBtn.disabled = false;
    }
  });
}
