import { test, expect } from '@playwright/test';

test('navegacion escritorio completa y ubicacion activa', async ({ page }) => {
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menú/i });
  if (await open.isVisible()) await open.click();
  const nav = page.getByRole('navigation', { name: /principal/i });
  for (const label of ['Inicio', 'Nosotros', 'Actualidad', 'Selecciones', 'Documentos', 'Contacto']) {
    await expect(nav.getByRole('link', { name: label, exact: true })).toBeVisible();
  }
  await expect(nav.getByRole('link', { name: 'Inicio', exact: true })).toHaveAttribute('aria-current', 'page');
});

test('jerarquia de header permanece estable en Inicio y Actualidad', async ({ page }) => {
  for (const route of ['/', '/actualidad/']) {
    await page.goto(route);
    const open = page.getByRole('button', { name: /open menu|abrir menú/i });
    if (await open.isVisible()) {
      await open.click();
      const submenuToggle = page.getByRole('button', { name: /submen[uú] de selecciones/i });
      if ('false' === await submenuToggle.getAttribute('aria-expanded')) {
        await submenuToggle.click();
      }
    }
    const nav = page.getByRole('navigation', { name: /principal/i });
    const parent = nav.getByRole('link', { name: 'Selecciones', exact: true });
    await expect(parent).toHaveAttribute('href', /\/selecciones\/$/);
    await expect(nav.getByRole('link', { name: 'Balonmano Piso', exact: true })).toHaveAttribute('href', /modalidad=Piso/);
    await expect(nav.getByRole('link', { name: 'Balonmano Playa', exact: true })).toHaveAttribute('href', /modalidad=Playa/);
    await expect(nav.getByRole('link', { name: route === '/' ? 'Inicio' : 'Actualidad', exact: true })).toHaveAttribute('aria-current', 'page');
    await expect(parent).not.toHaveAttribute('aria-current', 'page');

    const headerPosition = await page.locator('header.wp-block-template-part').evaluate((element) => getComputedStyle(element).position);
    const expectedHeaderPositions = (page.viewportSize()?.width ?? 1024) < 768 ? ['static'] : ['sticky', 'fixed'];
    expect(expectedHeaderPositions).toContain(headerPosition);
  }
});

test('navegacion movil conserva foco visible y recorrido predecible', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menú/i });
  await open.focus();
  await expect(page.locator(':focus-visible')).toHaveCount(1);
  await page.keyboard.press('Enter');
  const links = page.getByRole('navigation', { name: /principal/i }).getByRole('link');
  await expect(links).toHaveCount(6);
  const submenuToggle = page.getByRole('button', { name: /abrir submen.*selecciones/i });
  await submenuToggle.click();
  await expect(page.getByRole('link', { name: 'Balonmano Piso', exact: true })).toBeVisible();
  await page.keyboard.press('Escape');
  await expect(submenuToggle).toHaveAttribute('aria-expanded', 'false');
  await expect(submenuToggle).toBeFocused();
  const close = page.getByRole('button', { name: /close menu|cerrar menú/i });
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
