/* CPC London theme interactions. Content is fully visible without JavaScript. */
(() => {
  const d = document;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Header shadow once the page scrolls.
  const header = d.querySelector('[data-header]');
  const onScroll = () => header && header.classList.toggle('scrolled', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile menu.
  const toggle = d.querySelector('.menu-toggle');
  const closeMenu = () => { d.body.classList.remove('menu-open'); toggle && toggle.setAttribute('aria-expanded', 'false'); };
  toggle && toggle.addEventListener('click', () => {
    const open = d.body.classList.toggle('menu-open');
    toggle.setAttribute('aria-expanded', String(open));
  });

  // Dropdowns: hover on desktop (CSS), click/tap and keyboard everywhere.
  const closeSubs = (except) => d.querySelectorAll('.has-sub.open').forEach((li) => {
    if (li !== except) { li.classList.remove('open'); li.querySelector('.sub-toggle').setAttribute('aria-expanded', 'false'); }
  });
  d.querySelectorAll('.sub-toggle').forEach((btn) => btn.addEventListener('click', () => {
    const li = btn.parentElement;
    const open = !li.classList.contains('open');
    closeSubs(li);
    li.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', String(open));
  }));
  d.addEventListener('click', (e) => { if (!e.target.closest('.has-sub')) closeSubs(); });
  d.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeSubs(); closeMenu(); } });

  // Subtle reveal on scroll; siblings are staggered.
  const items = [...d.querySelectorAll('[data-reveal]')];
  if (reduce || !('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('in'));
  } else {
    const io = new IntersectionObserver((entries) => entries.forEach((entry) => {
      if (entry.isIntersecting) { entry.target.classList.add('in'); io.unobserve(entry.target); }
    }), { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });
    items.forEach((el) => {
      if (!el.style.getPropertyValue('--d')) {
        const siblings = [...el.parentElement.children].filter((c) => c.hasAttribute('data-reveal'));
        const index = siblings.indexOf(el);
        if (index > 0) el.style.setProperty('--d', String(Math.min(index, 6)));
      }
      io.observe(el);
    });
  }

  window.addEventListener('beforeprint', () => items.forEach((el) => el.classList.add('in')));

  // Click-to-play video previews.
  d.querySelectorAll('.video-facade').forEach((btn) => btn.addEventListener('click', () => {
    const frame = d.createElement('iframe');
    frame.src = btn.dataset.embed;
    frame.title = 'Video';
    frame.allow = 'autoplay; encrypted-media; picture-in-picture';
    frame.allowFullscreen = true;
    btn.replaceWith(frame);
  }));

  // Gallery lightbox.
  const links = [...d.querySelectorAll('[data-lightbox]')];
  if (links.length) {
    const box = d.createElement('dialog');
    box.className = 'lightbox';
    box.innerHTML = '<button class="lb-close" type="button" aria-label="Close">×</button><button class="lb-prev" type="button" aria-label="Previous image">‹</button><img alt=""><button class="lb-next" type="button" aria-label="Next image">›</button>';
    d.body.appendChild(box);
    const img = box.querySelector('img');
    let current = 0;
    const show = (i) => { current = (i + links.length) % links.length; img.src = links[current].href; img.alt = links[current].querySelector('img').alt; };
    links.forEach((a, i) => a.addEventListener('click', (e) => { e.preventDefault(); show(i); box.showModal(); }));
    box.querySelector('.lb-close').addEventListener('click', () => box.close());
    box.querySelector('.lb-prev').addEventListener('click', () => show(current - 1));
    box.querySelector('.lb-next').addEventListener('click', () => show(current + 1));
    box.addEventListener('click', (e) => { if (e.target === box) box.close(); });
    box.addEventListener('keydown', (e) => { if (e.key === 'ArrowLeft') show(current - 1); if (e.key === 'ArrowRight') show(current + 1); });
  }

  // Contact form: browser validation messages, then an in-page submit (falls back to a normal post).
  d.querySelectorAll('[data-cpc-form]').forEach((form) => {
    let loaded = Date.now();
    const elapsed = form.querySelector('[data-elapsed]');
    const status = form.querySelector('.form-status');
    const setError = (field, message) => {
      const wrap = field.closest('.field');
      field.setAttribute('aria-invalid', message ? 'true' : 'false');
      if (wrap) wrap.querySelector('.field-error').textContent = message || '';
    };
    const say = (cls, text) => { status.innerHTML = ''; const p = d.createElement('p'); p.className = cls; p.textContent = text; status.appendChild(p); };
    form.addEventListener('submit', async (e) => {
      elapsed.value = String(Date.now() - loaded);
      let first = null;
      form.querySelectorAll('input:not([type=hidden]):not([name=cpc_website]), textarea').forEach((field) => {
        const label = field.closest('.field').querySelector('label').firstChild.textContent.trim();
        let message = '';
        if (field.required && !field.value.trim()) message = label + ' is required.';
        else if (field.type === 'email' && field.value && !field.checkValidity()) message = 'Please enter a valid email address.';
        setError(field, message);
        if (message && !first) first = field;
      });
      e.preventDefault();
      if (first) { say('form-error', 'Please check the highlighted fields.'); first.focus(); return; }
      form.classList.add('sending');
      try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        const result = await response.json();
        if (result.ok) {
          form.reset();
          loaded = Date.now();
          say('form-success', result.message);
        } else {
          say('form-error', result.message);
          Object.entries(result.errors || {}).forEach(([key, message]) => { const field = form.querySelector('[name="cpc_' + key + '"]'); if (field) setError(field, message); });
        }
      } catch (error) {
        HTMLFormElement.prototype.submit.call(form);
        return;
      } finally {
        form.classList.remove('sending');
      }
      status.scrollIntoView({ block: 'nearest', behavior: reduce ? 'auto' : 'smooth' });
    });
  });
})();
