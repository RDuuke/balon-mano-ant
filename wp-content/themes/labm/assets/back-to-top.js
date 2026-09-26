(() => {
  'use strict';

  document.querySelectorAll('[data-labm-back-to-top]').forEach((button) => {
    const header = document.querySelector('header.wp-block-template-part');
    let compact = false;
    const updateVisibility = () => {
      button.classList.toggle('is-visible', window.scrollY > 360);
      if (window.scrollY > 120) compact = true;
      if (window.scrollY < 24) compact = false;
      header?.classList.toggle('is-compact', compact);
    };

    button.addEventListener('click', (event) => {
      event.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', updateVisibility, { passive: true });
    updateVisibility();
  });
})();
