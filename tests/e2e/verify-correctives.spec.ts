import { test, expect } from '@playwright/test';

test('navegacion escritorio completa y ubicacion activa', async ({ page }) => {
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menÃº/i });
  if (await open.isVisible()) await open.click();
  const nav = page.getByRole('navigation', { name: /principal/i });
  for (const label of ['Inicio', 'Nosotros', 'Actualidad', 'Selecciones', 'Documentos', 'Contacto']) {
    await expect(nav.getByRole('link', { name: label, exact: true })).toBeVisible();
  }
  await expect(nav.getByRole('link', { name: 'Inicio', exact: true })).toHaveAttribute('aria-current', 'page');
});

test('navegacion movil conserva foco visible y recorrido predecible', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menÃº/i });
  await open.focus();
  await page.keyboard.press('Enter');
  const links = page.getByRole('navigation', { name: /principal/i }).getByRole('link');
  await expect(links).toHaveCount(6);
  const close = page.getByRole('button', { name: /close menu|cerrar menÃº/i });
  await close.click();
  await expect(open).toBeFocused();
});

test('recorrido accesible llega al contenido y mantiene mensajes identificables', async ({ page }) => {
  await page.goto('/');
  const skip = page.getByRole('link', { name: /saltar al contenido/i });
  await skip.focus();
  await page.keyboard.press('Enter');
  await expect(page).toHaveURL(/#contenido-principal$/);
  await expect(page.locator('#contenido-principal')).toBeVisible();
});
