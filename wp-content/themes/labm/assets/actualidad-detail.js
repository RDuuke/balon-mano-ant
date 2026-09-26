(() => {
  const button = document.querySelector('[data-labm-actualidad-copy]');
  const field = document.querySelector('#labm-actualidad-copy-url');
  const status = document.querySelector('[data-labm-actualidad-copy-status]');

  if (!(button instanceof HTMLButtonElement) || !(field instanceof HTMLInputElement) || !(status instanceof HTMLElement)) {
    return;
  }

  button.dataset.labmActualidadCopyReady = 'true';

  const selectFallback = () => {
    field.focus();
    field.select();
    status.textContent = 'Selecciona el enlace para copiarlo manualmente.';
  };

  button.addEventListener('click', async () => {
    status.textContent = 'Preparando el enlace para compartir.';

    if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') {
      selectFallback();
      return;
    }

    try {
      await Promise.race([
        navigator.clipboard.writeText(field.value),
        new Promise((_, reject) => window.setTimeout(() => reject(new Error('clipboard-timeout')), 1000)),
      ]);
      status.textContent = 'Enlace copiado.';
    } catch (error) {
      selectFallback();
    }
  });
})();
