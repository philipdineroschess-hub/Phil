(() => {
  const button = document.getElementById('scrollMenu');
  const panel = document.getElementById('menuPanel');
  const backdrop = document.getElementById('menuBackdrop');
  const closeButton = document.getElementById('menuClose');
  if (!button || !panel || !backdrop) return;
  const syncScroll = () => button.classList.toggle('is-visible', window.scrollY > 180);
  const close = () => { panel.hidden = true; backdrop.hidden = true; button.setAttribute('aria-expanded', 'false'); };
  const open = () => { panel.hidden = false; backdrop.hidden = false; button.setAttribute('aria-expanded', 'true'); closeButton?.focus(); };
  button.addEventListener('click', () => panel.hidden ? open() : close());
  closeButton?.addEventListener('click', close);
  backdrop.addEventListener('click', close);
  panel.querySelectorAll('a').forEach(link => link.addEventListener('click', close));
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && !panel.hidden) { close(); button.focus(); } });
  window.addEventListener('scroll', syncScroll, { passive: true });
  syncScroll();
  document.addEventListener('keydown', event => {
    if (event.altKey && event.shiftKey && event.key.toLowerCase() === 'a' && !event.ctrlKey && !event.metaKey) {
      event.preventDefault();
      const match = window.location.pathname.match(/^(.*\/public)(?:\/|$)/);
      window.location.href = `${match ? match[1] : ''}/admin/login.php`;
    }
  });
})();
