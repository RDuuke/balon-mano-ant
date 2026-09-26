(() => {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  document.querySelectorAll('[data-labm-team]').forEach((section) => {
    const filters = Array.from(section.querySelectorAll('[data-labm-team-filter]'));
    const cards = Array.from(section.querySelectorAll('[data-labm-team-card]'));
    if (!filters.length || !cards.length) return;

    const applyFilter = (group, updateUrl) => {
      filters.forEach((filter) => {
        if ((filter.dataset.labmTeamFilterGroup || '') === group) {
          filter.setAttribute('aria-current', 'true');
        } else {
          filter.removeAttribute('aria-current');
        }
      });
      cards.forEach((card) => {
        const visible = !group || card.dataset.labmTeamGroup === group;
        card.classList.remove('is-entering');
        card.hidden = !visible;
        if (visible && !reduceMotion.matches) {
          window.requestAnimationFrame(() => card.classList.add('is-entering'));
        }
      });

      if (updateUrl) {
        const url = new URL(window.location.href);
        group ? url.searchParams.set('grupo', group) : url.searchParams.delete('grupo');
        window.history.pushState({}, '', url);
      }
    };

    filters.forEach((filter) => filter.addEventListener('click', (event) => {
      event.preventDefault();
      applyFilter(filter.dataset.labmTeamFilterGroup || '', true);
    }));

    window.addEventListener('popstate', () => {
      const group = new URLSearchParams(window.location.search).get('grupo') || '';
      applyFilter(group, false);
    });
  });
})();
