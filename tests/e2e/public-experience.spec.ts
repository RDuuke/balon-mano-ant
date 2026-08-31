import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const publicRoutes = ['/', '/nosotros/', '/actualidad/', '/selecciones/'];
const targetWidths = [320, 768, 1024, 1200, 1440];

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
  await expect(page.getByRole('heading', { level: 1, name: /nosotros/i })).toBeVisible();

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
