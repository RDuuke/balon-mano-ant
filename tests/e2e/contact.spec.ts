import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const widths = [320, 768, 1024, 1200, 1440];

test('Contacto publica los datos institucionales y un formulario accesible', async ({ page }) => {
  await page.goto('/contacto/');
  const section = page.locator('[data-labm-section="contacto"]');
  await expect(section).toHaveCount(1);
  await expect(section.getByRole('heading', { level: 1, name: 'Contacto' })).toBeVisible();
  await expect(section.getByRole('link', { name: 'info@balonmanoantioquia.com' })).toHaveAttribute('href', 'mailto:info@balonmanoantioquia.com');
  await expect(section.getByText('3233212981', { exact: true })).toBeVisible();
  await expect(section.getByText('Carrera 70 N.48-273 Int. 106 Coliseo Yesid Santos, Medellín, Colombia')).toBeVisible();
  await expect(section.getByRole('link', { name: 'Abrir ubicación en Google Maps' })).toHaveAttribute('rel', /noopener/);
  await expect(section.getByRole('link', { name: 'Facebook' })).toBeVisible();
  await expect(section.getByRole('link', { name: 'Instagram' })).toBeVisible();
  await expect(section.getByRole('checkbox', { name: /acepto el tratamiento/i })).toBeVisible();
  await expect(section.locator('input[name="sitio_web"]')).toHaveAttribute('tabindex', '-1');
  await expect(section.locator('input[name="nonce"]')).toHaveCount(1);
  await expect(section.locator('input[name="token"]')).toHaveCount(1);

  for (const width of widths) {
    await page.setViewportSize({ width, height: 900 });
    expect(await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth), `desborde a ${width}px`).toBe(false);
  }
  const results = await new AxeBuilder({ page }).include('[data-labm-section="contacto"]').withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa']).analyze();
  expect(results.violations).toEqual([]);
});

test('Contacto asocia errores, conserva teclado y no revela la configuración interna', async ({ page }) => {
  await page.goto('/contacto/');
  const form = page.locator('.labm-contact__form');
  await form.getByRole('button', { name: 'Enviar mensaje' }).click();
  await expect(page).toHaveURL(/\/contacto\/\?contacto_estado=.*#formulario/);
  await expect(page.getByRole('alert')).toBeVisible();
  await expect(form.locator('[aria-invalid="true"]')).not.toHaveCount(0);
  await page.keyboard.press('Tab');
  await expect(page.locator(':focus-visible')).toBeVisible();
  expect(await page.content()).not.toContain('LABM_CONTACT_TEST_RECIPIENTS');
});
