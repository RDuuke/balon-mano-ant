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
    const headings = items.map((item) => item.querySelector('h2')!.getBoundingClientRect());
    return {
      sectionWidth: element.getBoundingClientRect().width,
      heights: boxes.map((box) => box.height),
      gap: boxes[1].left - boxes[0].right,
      numbers,
      headingsSingleLine: headings.every((box) => box.height < 90),
    };
  });
  expect(desktopVisual.sectionWidth).toBeLessThanOrEqual(1200.5);
  expect(Math.abs(desktopVisual.heights[0] - desktopVisual.heights[1])).toBeLessThanOrEqual(1);
  expect(desktopVisual.heights[0]).toBeGreaterThanOrEqual(280);
  expect(desktopVisual.heights[0]).toBeLessThanOrEqual(420);
  expect(desktopVisual.gap).toBeGreaterThanOrEqual(12);
  expect(desktopVisual.gap).toBeLessThanOrEqual(32);
  expect(Math.min(...desktopVisual.numbers)).toBeGreaterThanOrEqual(52);
  expect(desktopVisual.headingsSingleLine).toBe(true);
  if (testInfo.project.name === 'wide-1440') {
    await section.screenshot({ path: 'artifacts/visual/mision-vision-1440.png' });
  }

  const results = await new AxeBuilder({ page }).include('[data-labm-section="nosotros-proposito"]').analyze();
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
