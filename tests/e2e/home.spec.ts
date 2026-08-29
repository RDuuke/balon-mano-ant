import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test('inicio responde y no presenta violaciones axe automaticas', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/LABM/i);
  await expect(page.locator('main h1')).toHaveText(/Liga Antioque/i);
  const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa']).analyze();
  expect(results.violations).toEqual([]);
});

test('inicio conserva el orden aprobado y excluye secciones retiradas', async ({ page }) => {
  await page.goto('/');
  const sections = await page.locator('main [data-labm-section]').evaluateAll((nodes) =>
    nodes.map((node) => node.getAttribute('data-labm-section')),
  );
  const approvedOrder = ['slider', 'presentacion', 'clubes', 'evento', 'actualidad', 'vinculacion', 'aliados'];
  expect(sections).toEqual(approvedOrder.filter((section) => sections.includes(section)));
  expect(sections).toContain('presentacion');
  expect(sections).toContain('vinculacion');
  await expect(page.getByRole('heading', { name: /balonmano de piso|balonmano playa|horarios|escenarios/i })).toHaveCount(0);
});

test('slider y aliados ofrecen controles accesibles y pausa', async ({ page }) => {
  await page.goto('/');
  const slider = page.locator('[data-labm-slider]');
  await expect(slider).toBeVisible();
  await expect(slider.locator('[data-labm-slide]')).toHaveCount(2);
  await expect(slider.getByText(/slide editorial en borrador/i)).toHaveCount(0);
  const initialIndicator = slider.locator('[data-labm-slide-to][aria-current="true"]');
  await expect(initialIndicator).toHaveAttribute('data-labm-slide-to', '0');
  await slider.getByRole('button', { name: /siguiente/i }).click();
  await expect(slider.locator('[data-labm-slide-to][aria-current="true"]')).toHaveAttribute('data-labm-slide-to', '1');
  await expect(slider.locator('[data-labm-slide]').nth(1)).toBeVisible();
  const pauseSlider = slider.locator('[data-labm-slider-pause]');
  await pauseSlider.click();
  await expect(slider).toHaveAttribute('data-labm-paused', 'true');
  await expect(pauseSlider).toHaveAttribute('aria-pressed', 'true');
  await expect(pauseSlider).toHaveText(/reanudar/i);

  const allies = page.locator('[data-labm-allies]');
  await expect(allies).toBeVisible();
  await expect(allies.getByRole('heading', { name: /aliados oficiales/i })).toBeVisible();
  await expect(allies.locator('.labm-allies__list > li')).toHaveCount(2);
  await expect(allies.getByText(/aliado editorial en borrador/i)).toHaveCount(0);
  const visualCopy = allies.locator('.labm-allies__visual');
  await expect(visualCopy).toHaveAttribute('aria-hidden', 'true');
  await expect(visualCopy).toHaveAttribute('inert', '');
  await expect(visualCopy).toHaveCSS('animation-name', 'labm-marquee');
  await expect(visualCopy).toHaveCSS('animation-direction', 'normal');
  await allies.getByRole('button', { name: /pausar/i }).click();
  await expect(allies).toHaveAttribute('data-labm-paused', 'true');
  await expect(visualCopy).toHaveCSS('animation-play-state', 'paused');
});

test('slider y aliados quedan estaticos con movimiento reducido', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto('/');
  const slider = page.locator('[data-labm-slider]');
  const allies = page.locator('[data-labm-allies]');
  await expect(slider).toBeVisible();
  await expect(allies).toBeVisible();
  await expect(allies).toHaveAttribute('data-labm-paused', 'true');
  await expect(allies.locator('.labm-allies__visual')).toHaveCSS('animation-name', 'none');
  const active = slider.locator('[data-labm-slide-to][aria-current="true"]');
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
  await page.waitForTimeout(7100);
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
});
