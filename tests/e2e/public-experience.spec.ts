import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const publicRoutes = ['/', '/nosotros/', '/actualidad/', '/selecciones/'];
const targetWidths = [320, 768, 1024, 1200, 1440];

test('Nosotros inicia con un banner editorial estático y responsive', async ({ page }) => {
  await page.goto('/nosotros/');
  const banner = page.locator('[data-labm-section="nosotros-banner"]');
  await expect(banner).toHaveCount(1);
  await expect(banner.getByRole('heading', { level: 1, name: 'Somos la Liga' })).toBeVisible();
  await expect(banner.locator('.labm-about-banner__media img')).toBeVisible();
  await expect(banner.locator('button, [data-labm-slider]')).toHaveCount(0);
  await expect(page.locator('main > section').first()).toHaveAttribute('data-labm-section', 'nosotros-banner');

  for (const width of [320, 1440]) {
    await page.setViewportSize({ width, height: 900 });
    await page.reload();
    const geometry = await banner.evaluate((element) => {
      const content = element.querySelector('.labm-about-banner__content');
      const media = element.querySelector('.labm-about-banner__media');
      if (!(content instanceof HTMLElement) || !(media instanceof HTMLElement)) throw new Error('Paneles ausentes');
      const contentBox = content.getBoundingClientRect();
      const mediaBox = media.getBoundingClientRect();
      return {
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
        sideBySide: Math.abs(contentBox.top - mediaBox.top) <= 1,
        stacked: mediaBox.top >= contentBox.bottom - 1,
      };
    });
    expect(geometry.overflow, `desborde a ${width}px`).toBe(false);
    expect(width === 320 ? geometry.stacked : geometry.sideBySide).toBe(true);

    await banner.locator('h1').evaluate((heading) => {
      heading.textContent = 'Somos la Liga Antioqueña de Balonmano y su comunidad deportiva';
    });
    await banner.locator('.labm-about-banner__summary').evaluate((summary) => {
      summary.textContent = 'Contenido editorial deliberadamente largo para comprobar que el banner conserva la lectura, adapta su altura y no invade el panel de imagen ni produce desplazamiento horizontal global.';
    });
    const longContent = await banner.evaluate((element) => {
      const content = element.querySelector('.labm-about-banner__content');
      const media = element.querySelector('.labm-about-banner__media');
      if (!(content instanceof HTMLElement) || !(media instanceof HTMLElement)) throw new Error('Paneles ausentes');
      const contentBox = content.getBoundingClientRect();
      const mediaBox = media.getBoundingClientRect();
      return {
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
        separated: contentBox.right <= mediaBox.left + 1 || mediaBox.top >= contentBox.bottom - 1,
      };
    });
    expect(longContent.overflow, `desborde con contenido largo a ${width}px`).toBe(false);
    expect(longContent.separated, `paneles separados a ${width}px`).toBe(true);
  }

  const results = await new AxeBuilder({ page }).include('[data-labm-section="nosotros-banner"]').analyze();
  expect(results.violations).toEqual([]);
});

test('Nosotros presenta Misión y Visión administrables con composición responsive', async ({ page }, testInfo) => {
  await page.goto('/nosotros/');
  const section = page.locator('[data-labm-section="nosotros-proposito"]');
  const mission = section.locator('[data-labm-purpose="mision"]');
  const vision = section.locator('[data-labm-purpose="vision"]');
  await expect(section).toHaveCount(1);
  await expect(mission.getByText('01', { exact: true })).toBeVisible();
  await expect(vision.getByText('02', { exact: true })).toBeVisible();
  await expect(mission.getByRole('heading', { name: 'Misión' })).toBeVisible();
  await expect(vision.getByRole('heading', { name: 'Visión' })).toBeVisible();
  await expect(mission).toHaveClass(/labm-about-purpose__item--light/);
  await expect(vision).toHaveClass(/labm-about-purpose__item--dark/);

  for (const width of [320, 768, 1024, 1200, 1440]) {
    await page.setViewportSize({ width, height: 900 });
    const geometry = await section.evaluate((element) => {
      const items = Array.from(element.querySelectorAll<HTMLElement>('[data-labm-purpose]'));
      const boxes = items.map((item) => item.getBoundingClientRect());
      return {
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
        sideBySide: Math.abs(boxes[0].top - boxes[1].top) <= 1,
        stacked: boxes[1].top >= boxes[0].bottom - 1,
      };
    });
    expect(geometry.overflow, `desborde a ${width}px`).toBe(false);
    expect(width < 768 ? geometry.stacked : geometry.sideBySide).toBe(true);
  }

  await page.setViewportSize({ width: 1440, height: 1000 });
  const desktopVisual = await section.evaluate((element) => {
    const items = Array.from(element.querySelectorAll<HTMLElement>('[data-labm-purpose]'));
    const boxes = items.map((item) => item.getBoundingClientRect());
    const numbers = items.map((item) => Number.parseFloat(getComputedStyle(item.querySelector('.labm-about-purpose__number')!).fontSize));
    const headingSizes = items.map((item) => Number.parseFloat(getComputedStyle(item.querySelector('h2')!).fontSize));
    const headings = items.map((item) => item.querySelector('h2')!.getBoundingClientRect());
    const footer = document.querySelector<HTMLElement>('.labm-footer');
    return {
      sectionWidth: element.getBoundingClientRect().width,
      heights: boxes.map((box) => box.height),
      gap: boxes[1].left - boxes[0].right,
      numbers,
      headingSizes,
      headingsSingleLine: headings.every((box) => box.height < 90),
      visionBackground: getComputedStyle(items[1]).backgroundColor,
      footerBackground: footer ? getComputedStyle(footer).backgroundColor : '',
    };
  });
  expect(desktopVisual.sectionWidth).toBeLessThanOrEqual(1200.5);
  expect(Math.abs(desktopVisual.heights[0] - desktopVisual.heights[1])).toBeLessThanOrEqual(1);
  expect(desktopVisual.heights[0]).toBeGreaterThanOrEqual(280);
  expect(desktopVisual.heights[0]).toBeLessThanOrEqual(420);
  expect(desktopVisual.gap).toBeGreaterThanOrEqual(12);
  expect(desktopVisual.gap).toBeLessThanOrEqual(32);
  expect(Math.min(...desktopVisual.numbers)).toBeGreaterThanOrEqual(44);
  expect(Math.max(...desktopVisual.numbers)).toBeLessThanOrEqual(48);
  expect(Math.min(...desktopVisual.headingSizes)).toBeGreaterThanOrEqual(24);
  expect(Math.max(...desktopVisual.headingSizes)).toBeLessThanOrEqual(32);
  expect(desktopVisual.headingsSingleLine).toBe(true);
  expect(desktopVisual.visionBackground).toBe(desktopVisual.footerBackground);
  if (testInfo.project.name === 'wide-1440') {
    await section.screenshot({ path: 'artifacts/visual/mision-vision-1440.png' });
  }

  const results = await new AxeBuilder({ page }).include('[data-labm-section="nosotros-proposito"]').analyze();
  expect(results.violations).toEqual([]);
});

test('Misión y Visión adaptan contenido editorial largo sin desborde ni solapamiento', async ({ page }) => {
  await page.goto('/nosotros/');
  const section = page.locator('[data-labm-section="nosotros-proposito"]');
  const items = section.locator('[data-labm-purpose]');
  await expect(items).toHaveCount(2);

  await items.evaluateAll((purposeItems) => {
    const longTitle = 'Propósito institucional y comunitario de largo alcance';
    const longCopy = 'Contenido editorial deliberadamente extenso para comprobar que cada panel amplía su altura, conserva la lectura completa y permanece separado del panel contiguo sin provocar desplazamiento horizontal ni solapamientos en ningún ancho objetivo. '.repeat(4);
    purposeItems.forEach((item, index) => {
      const heading = item.querySelector('h2');
      const copy = item.querySelector('.labm-about-purpose__copy');
      if (!(heading instanceof HTMLElement) || !(copy instanceof HTMLElement)) throw new Error('Contenido de propósito ausente');
      heading.textContent = `${longTitle} ${index + 1}`;
      copy.textContent = longCopy;
    });
  });

  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 900 });
    const geometry = await section.evaluate((element) => {
      const purposeItems = Array.from(element.querySelectorAll<HTMLElement>('[data-labm-purpose]'));
      const boxes = purposeItems.map((item) => item.getBoundingClientRect());
      return {
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
        separated: boxes[0].right <= boxes[1].left + 1 || boxes[1].top >= boxes[0].bottom - 1,
        contentFits: purposeItems.every((item) => item.scrollHeight <= item.clientHeight + 1),
      };
    });
    expect(geometry.overflow, `desborde con contenido largo a ${width}px`).toBe(false);
    expect(geometry.separated, `paneles solapados a ${width}px`).toBe(true);
    expect(geometry.contentFits, `contenido recortado a ${width}px`).toBe(true);
  }
});

test('Nosotros presenta integrantes editables, filtrables y responsive', async ({ page }) => {
  await page.goto('/nosotros/');
  const section = page.locator('[data-labm-section="nosotros-equipo"]');
  await expect(section).toHaveCount(1);
  await expect(section.getByRole('heading', { level: 2, name: 'Quiénes hacen posible la Liga' })).toBeVisible();
  await expect(section.locator('[data-labm-team-card]')).toHaveCount(4);
  await expect(section.getByText('Andrés Montoya')).toBeVisible();
  await expect(section.getByText('Director técnico')).toBeVisible();
  await expect(section.locator('.labm-about-team__media img')).toHaveCount(4);

  const purpose = page.locator('[data-labm-section="nosotros-proposito"]');
  const order = await page.locator('[data-labm-section]').evaluateAll((sections) =>
    sections.map((item) => item.getAttribute('data-labm-section')),
  );
  expect(order.indexOf('nosotros-equipo')).toBe(order.indexOf('nosotros-proposito') + 1);
  await expect(purpose).toBeVisible();

  await section.getByRole('link', { name: 'Entrenadores' }).click();
  await expect(page).toHaveURL(/grupo=entrenadores/);
  const filtered = page.locator('[data-labm-section="nosotros-equipo"]');
  await expect(filtered.getByRole('link', { name: 'Entrenadores' })).toHaveAttribute('aria-current', 'true');
  await expect(filtered.locator('[data-labm-team-card]')).toHaveCount(2);
  await expect(filtered.getByText('Mateo Giraldo')).toHaveCount(0);

  await page.goto('/nosotros/');
  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 1000 });
    const geometry = await page.locator('[data-labm-section="nosotros-equipo"]').evaluate((element) => {
      const cards = Array.from(element.querySelectorAll<HTMLElement>('[data-labm-team-card]'));
      const boxes = cards.map((card) => card.getBoundingClientRect());
      return {
        cardCount: cards.length,
        columns: new Set(boxes.map((box) => Math.round(box.left))).size,
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
        overlap: boxes.some((box, index) => boxes.slice(index + 1).some((other) =>
          box.left < other.right && box.right > other.left && box.top < other.bottom && box.bottom > other.top,
        )),
      };
    });
    expect(geometry.cardCount).toBe(4);
    expect(geometry.columns).toBe(width >= 768 ? 4 : 2);
    expect(geometry.overflow).toBe(false);
    expect(geometry.overlap).toBe(false);
  }

  await section.locator('[data-labm-team-card]').evaluateAll((cards) => {
    cards.forEach((card, index) => {
      card.querySelector('h3')!.textContent = `Nombre editorial deliberadamente largo para integrante ${index + 1}`;
      card.querySelector('p')!.textContent = 'Cargo institucional extenso que debe adaptarse sin recorte ni superposición visual';
    });
  });
  expect(await page.evaluate(() => document.documentElement.scrollWidth <= document.documentElement.clientWidth)).toBe(true);
  const results = await new AxeBuilder({ page }).include('[data-labm-section="nosotros-equipo"]').analyze();
  expect(results.violations).toEqual([]);
});

test('Nosotros reutiliza el CTA de vinculación después del equipo', async ({ page }) => {
  await page.goto('/');
  const homeCta = page.locator('[data-labm-section="vinculacion"]');
  await expect(homeCta).toHaveCount(1);
  const homeSignature = await homeCta.evaluate((element) => ({
    className: element.className,
    heading: element.querySelector('h2')?.textContent?.trim(),
    copy: element.querySelector('p')?.textContent?.trim(),
    href: element.querySelector('a')?.getAttribute('href'),
    label: element.querySelector('a')?.textContent?.trim(),
  }));

  await page.goto('/nosotros/');
  const cta = page.locator('[data-labm-section="vinculacion"]');
  await expect(cta).toHaveCount(1);
  await expect(cta.getByRole('heading', { level: 2, name: 'Haz parte del balonmano antioqueño' })).toBeVisible();
  await expect(cta.getByText('Conecta con la Liga, sus clubes y procesos deportivos.')).toBeVisible();
  await expect(cta.getByRole('link', { name: 'Contáctanos' })).toHaveAttribute('href', '/contacto/');
  const aboutSignature = await cta.evaluate((element) => ({
    className: element.className,
    heading: element.querySelector('h2')?.textContent?.trim(),
    copy: element.querySelector('p')?.textContent?.trim(),
    href: element.querySelector('a')?.getAttribute('href'),
    label: element.querySelector('a')?.textContent?.trim(),
  }));
  expect(aboutSignature).toEqual(homeSignature);

  const order = await page.locator('[data-labm-section]').evaluateAll((sections) =>
    sections.map((item) => item.getAttribute('data-labm-section')),
  );
  expect(order.indexOf('vinculacion')).toBe(order.indexOf('nosotros-equipo') + 1);

	await page.setViewportSize({ width: 1440, height: 900 });
	await page.evaluate(() => document.fonts.ready);
	const desktopGeometry = await cta.evaluate((element) => {
		const section = element.getBoundingClientRect();
		const heading = element.querySelector('h2')!.getBoundingClientRect();
		const copy = element.querySelector('p')!.getBoundingClientRect();
		const button = element.querySelector('a')!.getBoundingClientRect();
		const headingStyle = getComputedStyle(element.querySelector('h2')!);
		const copyStyle = getComputedStyle(element.querySelector('p')!);
		const accentStyle = getComputedStyle(element, '::before');
		const fontResources = performance.getEntriesByType('resource')
			.map((entry) => entry.name)
			.filter((url) => /\.(?:woff2?|ttf)(?:\?|$)/i.test(url));
		return {
			sectionHeight: section.height,
			contentWidth: section.width - Number.parseFloat(getComputedStyle(element).paddingLeft) - Number.parseFloat(getComputedStyle(element).paddingRight),
			accentWidth: Number.parseFloat(accentStyle.width),
			accentHeight: Number.parseFloat(accentStyle.height),
			textOffset: heading.left - section.left - Number.parseFloat(getComputedStyle(element).paddingLeft),
			textWidth: heading.width,
			copyGap: copy.top - heading.bottom,
			buttonWidth: button.width,
			buttonHeight: button.height,
			buttonRight: section.right - button.right,
			fontFamily: headingStyle.fontFamily,
			fontSize: headingStyle.fontSize,
			fontWeight: headingStyle.fontWeight,
			copyFontSize: copyStyle.fontSize,
			fontResources,
		};
	});
	expect(desktopGeometry.sectionHeight).toBeCloseTo(300, 0);
	expect(desktopGeometry.contentWidth).toBeCloseTo(1200, 0);
	expect(desktopGeometry.accentWidth).toBeCloseTo(8, 0);
	expect(desktopGeometry.accentHeight).toBeCloseTo(180, 0);
	expect(desktopGeometry.textOffset).toBeCloseTo(52, 0);
	expect(desktopGeometry.textWidth).toBeLessThanOrEqual(760.5);
	expect(desktopGeometry.copyGap).toBeCloseTo(8, 0);
	expect(desktopGeometry.buttonWidth).toBeCloseTo(163, 0);
	expect(desktopGeometry.buttonHeight).toBeCloseTo(48, 0);
	expect(desktopGeometry.buttonRight).toBeCloseTo(120, 0);
	expect(desktopGeometry.fontFamily).toContain('Barlow Condensed');
	expect(desktopGeometry.fontSize).toBe('46px');
	expect(desktopGeometry.fontWeight).toBe('700');
	expect(desktopGeometry.copyFontSize).toBe('17px');
	expect(desktopGeometry.fontResources).toHaveLength(1);
	expect(new URL(desktopGeometry.fontResources[0]).pathname).toMatch(/\/wp-content\/themes\/labm\/assets\/fonts\/barlow-condensed-latin-wght-normal\.woff2$/);
	expect(new URL(desktopGeometry.fontResources[0]).origin).toBe(new URL(page.url()).origin);

  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 900 });
    const geometry = await cta.evaluate((element) => {
      const box = element.getBoundingClientRect();
      return {
        insideViewport: box.left >= 0 && box.right <= document.documentElement.clientWidth + 1,
        contentFits: element.scrollHeight <= element.clientHeight + 1,
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
      };
    });
    expect(geometry.insideViewport).toBe(true);
    expect(geometry.contentFits).toBe(true);
    expect(geometry.overflow).toBe(false);
  }

  const results = await new AxeBuilder({ page }).include('[data-labm-section="vinculacion"]').analyze();
  expect(results.violations).toEqual([]);
});

test('contenido publico conserva ancho maximo, centrado y gutters coherentes', async ({ page }) => {
  test.setTimeout(90_000);
  await page.goto('/');
  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 900 });
    const geometry = await page.locator('[data-labm-section="presentacion"]').evaluate((section) => {
      const style = getComputedStyle(section);
      const left = Number.parseFloat(style.paddingLeft);
      const right = Number.parseFloat(style.paddingRight);
      return {
        contentWidth: section.getBoundingClientRect().width - left - right,
        left,
        right,
        overflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
      };
    });
    expect(geometry.contentWidth, `ancho de contenido a ${width}px`).toBeLessThanOrEqual(1200.5);
    expect(Math.abs(geometry.left - geometry.right), `centrado a ${width}px`).toBeLessThanOrEqual(1);
    expect(geometry.left, `gutter izquierdo a ${width}px`).toBeGreaterThanOrEqual(16);
    expect(geometry.overflow, `desborde global a ${width}px`).toBe(false);
  }
});

test('3.1 navegación global, páginas institucionales, foco y ruta ausente', async ({ page }) => {
  await page.goto('/');
  const openMenu = page.getByRole('button', { name: /open menu|abrir menÃº/i });
  if (await openMenu.isVisible()) await openMenu.click();
  const navigation = page.getByRole('navigation', { name: /navegación principal/i });
  await expect(navigation).toBeVisible();
  await expect(navigation.getByRole('link', { name: 'Inicio', exact: true })).toBeVisible();
  await expect(navigation.getByRole('link', { name: 'Nosotros', exact: true })).toHaveAttribute('href', /\/nosotros\/?$/);
  await page.keyboard.press('Tab');
  await expect(page.locator(':focus-visible')).toBeVisible();

  await page.goto('/nosotros/');
  await expect(page.getByRole('heading', { level: 1, name: /somos la liga/i })).toBeVisible();

  const missing = await page.goto('/ruta-ficticia-ausente/');
  expect(missing?.status()).toBe(404);
  await expect(page.getByRole('heading', { level: 1, name: /no encontramos/i })).toBeVisible();
  await expect(page.getByRole('link', { name: /volver al inicio/i })).toBeVisible();
});

test('3.2 actualidad ofrece filtros, detalle, estado vacío y privacidad', async ({ page }) => {
  await page.goto('/actualidad/');
  await expect(page.getByRole('heading', { level: 1, name: 'Actualidad' })).toBeVisible();
  await expect(page.locator('[data-labm-listado="actualidad"] article')).toHaveCount(3);
  await expect(page.getByRole('link', { name: /página siguiente/i })).toBeVisible();
  await expect(page.getByText(/actualidad incompleta/i)).toHaveCount(0);
  const detail = page.locator('[data-labm-listado="actualidad"] article').first().getByRole('link').first();
  await detail.click();
  await expect(page.locator('main h1')).toBeVisible();

  await page.goto('/actualidad/?categoria=sin-resultados-ficticios');
  await expect(page.getByText(/no hay publicaciones/i)).toBeVisible();
  await expect(page.getByRole('link', { name: /limpiar filtros/i })).toBeVisible();
});

test('3.2 selecciones filtra Piso y Playa sin exponer privados', async ({ page }) => {
  for (const modalidad of ['Piso', 'Playa']) {
    await page.goto(`/selecciones/?modalidad=${modalidad}`);
    await expect(page.getByRole('heading', { level: 1, name: 'Selecciones' })).toBeVisible();
    await expect(page.locator('[data-labm-listado="selecciones"] article')).not.toHaveCount(0);
    await expect(page.getByText(/selección privada/i)).toHaveCount(0);
    await expect(page.locator('[data-labm-modalidad]')).toContainText(modalidad);
  }
});

test('3.3 no hay desborde, axe pasa y reduced motion se respeta', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  for (const route of publicRoutes) {
    await page.goto(route);
    const overflow = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
    expect(overflow, `desborde horizontal en ${route}`).toBe(false);
    const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa']).analyze();
    expect(results.violations).toEqual([]);
  }
  const motion = await page.locator('main').evaluate((element) => getComputedStyle(element).scrollBehavior);
  expect(motion).toBe('auto');
});

test('portada y Selecciones conservan contenido en los anchos objetivo', async ({ page }) => {
  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 900 });
    for (const route of ['/', '/selecciones/?modalidad=Piso', '/selecciones/?modalidad=Playa']) {
      await page.goto(route);
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
      expect(overflow, `desborde en ${route} a ${width}px`).toBe(false);
      await expect(page.locator('main h1')).toBeVisible();
    }
  }
});
