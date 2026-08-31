(() => {
	'use strict';

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	document.querySelectorAll('[data-labm-slider]').forEach((slider) => {
		const slides = Array.from(slider.querySelectorAll('[data-labm-slide]'));
		const indicators = Array.from(slider.querySelectorAll('[data-labm-slide-to]'));
		const pause = slider.querySelector('[data-labm-slider-pause]');
		const pauseLabel = pause?.querySelector('[data-labm-pause-label]');
		let current = Math.max(0, slides.findIndex((slide) => slide.getAttribute('aria-current') === 'true'));
		let timer;

		const show = (index) => {
			current = (index + slides.length) % slides.length;
			slides.forEach((slide, itemIndex) => {
				slide.hidden = itemIndex !== current;
				slide.setAttribute('aria-current', itemIndex === current ? 'true' : 'false');
			});
			indicators.forEach((indicator, itemIndex) => indicator.setAttribute('aria-current', itemIndex === current ? 'true' : 'false'));
		};
		const stop = () => {
			window.clearInterval(timer);
			timer = undefined;
		};
		const start = () => {
			stop();
			if (!reduceMotion.matches && slider.dataset.labmPaused !== 'true' && slides.length > 1) {
				timer = window.setInterval(() => show(current + 1), 7000);
			}
		};

		if (!slides.length) return;
		show(current);
		slider.querySelector('[data-labm-slider-prev]')?.addEventListener('click', () => { show(current - 1); start(); });
		slider.querySelector('[data-labm-slider-next]')?.addEventListener('click', () => { show(current + 1); start(); });
		indicators.forEach((indicator) => indicator.addEventListener('click', () => { show(Number(indicator.dataset.labmSlideTo)); start(); }));
		pause?.addEventListener('click', () => {
			const paused = slider.dataset.labmPaused !== 'true';
			slider.dataset.labmPaused = String(paused);
			pause.setAttribute('aria-pressed', String(paused));
			const label = paused ? (pause.dataset.labelResume || 'Reanudar') : (pause.dataset.labelPause || 'Pausar');
			pause.setAttribute('aria-label', label);
			if (pauseLabel) pauseLabel.textContent = label;
			paused ? stop() : start();
		});
		reduceMotion.addEventListener?.('change', start);
		start();
	});

	document.querySelectorAll('[data-labm-allies]').forEach((allies) => {
		const pause = allies.querySelector('[data-labm-allies-pause]');
		const visualCopy = allies.querySelector('.labm-allies__visual');
		visualCopy?.setAttribute('inert', '');
		visualCopy?.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach((element) => element.setAttribute('tabindex', '-1'));
		const setPaused = (paused) => {
			allies.dataset.labmPaused = String(paused || reduceMotion.matches);
			pause?.setAttribute('aria-pressed', String(paused));
			if (pause) pause.textContent = paused ? (pause.dataset.labelResume || 'Reanudar movimiento') : (pause.dataset.labelPause || 'Pausar movimiento');
		};
		pause?.addEventListener('click', () => setPaused(pause.getAttribute('aria-pressed') !== 'true'));
		allies.addEventListener('focusin', () => { allies.dataset.labmFocusPaused = 'true'; });
		allies.addEventListener('focusout', () => { allies.dataset.labmFocusPaused = 'false'; });
		reduceMotion.addEventListener?.('change', () => setPaused(pause?.getAttribute('aria-pressed') === 'true'));
		setPaused(false);
	});
})();
