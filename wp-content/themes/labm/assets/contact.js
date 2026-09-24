(() => {
  const forms = document.querySelectorAll('[data-labm-contact-form]');

  forms.forEach((form) => {
    const submit = form.querySelector('[data-labm-contact-submit]');
    const label = form.querySelector('[data-labm-contact-submit-label]');
    const sending = form.querySelector('[data-labm-contact-sending]');

    if (!(submit instanceof HTMLButtonElement) || !(label instanceof HTMLElement) || !(sending instanceof HTMLElement)) {
      return;
    }

    const reset = () => {
      form.setAttribute('aria-busy', 'false');
      submit.disabled = false;
      label.hidden = false;
      sending.hidden = true;
    };

    form.addEventListener('submit', (event) => {
      if (form.getAttribute('aria-busy') === 'true') {
        event.preventDefault();
        return;
      }

      if (!form.checkValidity()) {
        reset();
        return;
      }

      form.setAttribute('aria-busy', 'true');
      submit.disabled = true;
      label.hidden = true;
      sending.hidden = false;
    });

    window.addEventListener('pageshow', reset);
  });
})();
