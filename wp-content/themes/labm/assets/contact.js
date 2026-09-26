(() => {
  const forms = document.querySelectorAll('[data-labm-contact-form]');

  forms.forEach((form) => {
    const submit = form.querySelector('[data-labm-contact-submit]');
    const label = form.querySelector('[data-labm-contact-submit-label]');
    const sending = form.querySelector('[data-labm-contact-sending]');

    if (!(submit instanceof HTMLButtonElement) || !(label instanceof HTMLElement) || !(sending instanceof HTMLElement)) {
      return;
    }

    const defaultLabel = label.textContent;
    const sendingLabel = sending.dataset.labmContactSendingLabel || '';

    const reset = () => {
      form.setAttribute('aria-busy', 'false');
      submit.disabled = false;
      label.textContent = defaultLabel;
      label.hidden = false;
      sending.replaceChildren();
      sending.hidden = true;
    };

    const renderSending = () => {
      const spinner = document.createElement('span');
      spinner.className = 'labm-contact__spinner';
      spinner.setAttribute('aria-hidden', 'true');
      sending.replaceChildren(spinner, document.createTextNode(sendingLabel));
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
      label.textContent = '';
      label.hidden = true;
      renderSending();
      sending.hidden = false;
    });

    window.addEventListener('pageshow', reset);
  });
})();
